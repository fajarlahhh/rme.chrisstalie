<?php

namespace App\Livewire\Manajemenstok\Opname\Pengurangan;

use App\Models\Stok;
use App\Models\Barang;
use Livewire\Component;
use App\Class\BarangClass;
use App\Class\JurnalkeuanganClass;
use App\Models\StokKeluar;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $dataBarang = [], $barang, $dataStok = [], $barang_id, $catatan, $qty_keluar;

    public function updatedBarangId($value)
    {
        $this->barang = collect($this->dataBarang)->firstWhere('id', $value);
    }

    public function mount()
    {
        $this->barang_id = '';
        $this->dataBarang = Stok::select('no_batch', 'barang_id', 'tanggal_kedaluarsa', 'harga_beli', DB::raw('COUNT(*) as qty'))->groupBy('no_batch', 'barang_id', 'tanggal_kedaluarsa', 'harga_beli')->whereNull('stok_keluar_id')->with('barang.barangSatuanTerkecil')->get()->map(fn($q) => [
            'id' => $q->barang_id . '-' . $q->no_batch . '-' . $q->tanggal_kedaluarsa . '-' . $q->harga_beli,
            'barang_id' => $q->barang_id,
            'nama' => $q->barang->nama,
            'satuan' => $q->barang->barangSatuanTerkecil->nama,
            'barang_satuan_id' => $q->barang->barangSatuanTerkecil->id,
            'tanggal_kedaluarsa' => $q->tanggal_kedaluarsa,
            'harga' => $q->harga_beli,
            'kode_akun_id' => $q->barang->kode_akun_id,
            'kode_akun_modal_id' => $q->barang->kode_akun_modal_id,
            'qty' => $q->qty,
            'no_batch' => $q->no_batch,
        ])->toArray();
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'barang_id' => 'required',
            'qty_keluar' => 'required',
        ]);

        DB::transaction(function () {
            if (Stok::where('barang_id', $this->barang['barang_id'])->where('no_batch', $this->barang['no_batch'])->where('tanggal_kedaluarsa', $this->barang['tanggal_kedaluarsa'])->count() < $this->qty_keluar) {
                session()->flash('danger', 'Qty dikeluarkan melebihi stok yang tersedia');
                return $this->render();
            }
            $data = new StokKeluar();
            $data->tanggal = now();
            $data->barang_id = $this->barang['barang_id'];
            $data->qty = $this->qty_keluar;
            $data->harga = $this->barang['harga'];
            $data->catatan = $this->catatan;
            $data->pengguna_id = auth()->id();
            $data->barang_satuan_id = $this->barang['barang_satuan_id'];
            $data->rasio_dari_terkecil = 1;
            $data->koreksi = 1;
            $data->save();

            Stok::where('barang_id', $this->barang['barang_id'])
                ->where('no_batch', $this->barang['no_batch'])
                ->where('tanggal_kedaluarsa', $this->barang['tanggal_kedaluarsa'])
                ->where('harga_beli', $this->barang['harga'])
                ->limit($this->qty_keluar)->update([
                    'stok_keluar_id' => $data->id,
                    'tanggal_keluar' => now(),
                ]);

            $hargaBeli = Stok::where('barang_id', $this->barang['barang_id'])
                ->where('stok_keluar_id', $data->id)
                ->sum('harga_beli');
            $this->keuanganJurnal($data, $hargaBeli);

            session()->flash('success', 'Berhasil menyimpan data');
        });
        return $this->redirect('/manajemenstok/opname/pengurangan');
    }

    private function keuanganJurnal($koreksi, $hargaBeli)
    {
        $detail[] = [
            'kode_akun_id' => $this->barang['kode_akun_id'],
            'debet' => 0,
            'kredit' => $hargaBeli,
        ];
        $detail[] = [
            'kode_akun_id' => $this->barang['kode_akun_modal_id'],
            'debet' => $hargaBeli,
            'kredit' => 0,
        ];

        JurnalkeuanganClass::insert(
            jenis: 'Koreksi',
            sub_jenis: 'Koreksi Pengeluaran Stok',
            tanggal: now(),
            uraian: 'Koreksi Pengeluaran Stok Barang ' . $this->barang['nama'] . ' sejumlah ' . $this->qty_keluar . ' ' . $this->barang['satuan'],
            system: 1,
            foreign_key: 'stok_keluar_id',
            foreign_id: $koreksi->id,
            detail: $detail
        );
    }

    public function render()
    {
        return view('livewire.manajemenstok.opname.pengurangan.form');
    }
}
