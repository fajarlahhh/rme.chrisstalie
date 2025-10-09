<?php

namespace App\Livewire\Klinik\Kasir;

use App\Models\Nakes;
use Livewire\Component;
use App\Models\Pembayaran;
use App\Models\Registrasi;
use App\Models\MetodeBayar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Form extends Component
{
    public $tindakan = [], $dataTindakan = [], $dataNakes = [], $resep = [], $dataMetodeBayar = [];
    public $data, $metode = 'Cash', $cash = 0, $keterangan;

    public function updatedMetode()
    {
        if ($this->metode == 'Cash') {
            $this->cash = 0;
        }
    }

    public function mount(Registrasi $data)
    {
        $this->data = $data;
        $this->dataMetodeBayar = MetodeBayar::orderBy('nama')->get()->map(fn($q) => [
            'id' => $q->id,
            'nama' => $q->nama,
        ])->toArray();

        $this->tindakan = $data->tindakan->map(fn($q) => [
            'id' => $q->tarif_tindakan_id,
            'nama' => $q->tarifTindakan->nama,
            'diskon' => 0,
            'qty' => $q->qty,
            'harga' => $q->harga,
            'catatan' => $q->catatan,
            'dokter_id' => $q->dokter_id,
            'perawat_id' => $q->perawat_id,
            'biaya_jasa_dokter' => $q->biaya_jasa_dokter > 0 ? 1 : ($q->dokter_id ? 1 : 0),
            'biaya_jasa_perawat' => $q->biaya_jasa_perawat > 0 ? 1 : ($q->perawat_id ? 1 : 0),
            'biaya' => $q->biaya,
        ])->toArray();

        $this->dataNakes = Nakes::orderBy('nama')->get()->map(fn($q) => [
            'id' => $q->id,
            'nama' => $q->nama,
            'dokter' => $q->dokter,
        ])->toArray();


        $this->resep = $data->resepobat->groupBy('resep')->map(fn($q) => [
            'catatan' => $q->first()->catatan,
            'resep' => $q->first()->resep,
            'barang' => $q->map(fn($r) => [
                'id' => $r->barang_id,
                'nama' => $r->barang->nama,
                'satuan' => $r->barang->barangSatuan->where('id', $r->barang_satuan_id)->first()->nama,
                'biaya' => $r->barang->barangSatuan->where('id', $r->barang_satuan_id)->first()->harga_jual,
                'qty' => $r->qty,
            ])->toArray(),
        ])->toArray();
    }

    public function submit()
    {
        $this->validate([
            'metode' => 'required',
            'tindakan.*.dokter_id' => 'required|exists:nakes,id',
            'tindakan.*.perawat_id' => 'required|exists:nakes,id',
        ]);
        if ($this->metode == 'Cash') {
            $this->validate([
                'cash' => 'required|numeric|min:' . collect($this->tindakan)->sum(fn($q) => $q['biaya'] * $q['qty']) + collect($this->resep)->sum(fn($q) => collect($q['barang'])->sum(fn($q) => $q['biaya'] * $q['qty'])),
            ]);
        }

        DB::transaction(function () {
            $id = Str::uuid();
            $pembayaran = new Pembayaran();
            $pembayaran->jumlah = collect($this->tindakan)->sum('biaya') * collect($this->tindakan)->sum('qty') - collect($this->tindakan)->sum('diskon') + collect($this->resep)->sum(fn($q) => collect($q['barang'])->sum(fn($q) => $q['biaya'] * $q['qty']));
            $pembayaran->metode = $this->metode;
            $pembayaran->id = $id;
            $pembayaran->save();
            

            $this->jurnalPendapatan($data, $metodeBayar);
        });
        session()->flash('success', 'Berhasil menyimpan data');
        $this->redirect($this->previous);
    }


    private function jurnalPendapatan($data, $metodeBayar)
    {
        $id = Str::uuid();
        $jurnalDetail = [];

        foreach (
            collect($this->barang)->groupBy('kode_akun_penjualan_id')->map(fn($q) => [
                'kode_akun_id' => $q->first()['kode_akun_penjualan_id'],
                'total' => $q->sum(fn($q) => $q['harga'] * $q['qty']),
            ]) as $barang
        ) {
            $jurnalDetail[] = [
                'jurnal_id' => $id,
                'debet' => 0,
                'kredit' => $barang['total'],
                'kode_akun_id' => $barang['kode_akun_id']
            ];
        }
        if ($this->diskon > 0) {
            $jurnalDetail[] = [
                'jurnal_id' => $id,
                'debet' => $this->diskon,
                'kredit' => 0,
                'kode_akun_id' => '44100'
            ];
        }
        $jurnalDetail[] = [
            'jurnal_id' => $id,
            'debet' => $this->total_harga_barang - $this->diskon,
            'kredit' => 0,
            'kode_akun_id' => $metodeBayar->kode_akun_id
        ];
        JurnalClass::insert($id, 'Penjualan', [
            'tanggal' => now(),
            'uraian' => 'Penjualan Barang Bebas ' . $data->id,
            'unit_bisnis' => 'Apotek',
            'referensi_id' => $data->id,
            'pengguna_id' => auth()->id(),
        ], $jurnalDetail);
    }
    
    public function render()
    {
        return view('livewire.klinik.kasir.form');
    }
}
