<?php

namespace App\Livewire\Klinik\Kasir;

use App\Models\Nakes;
use Livewire\Component;
use App\Models\Pembayaran;
use App\Models\Registrasi;
use App\Models\MetodeBayar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Class\BarangClass;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;

    public $data;
    public $tindakan = [], $dataTindakan = [], $dataNakes = [], $resep = [], $dataMetodeBayar = [];
    public $metode_bayar = 1, $cash = 0, $keterangan, $dataBarang = [];
    public $keterangan_pembayaran, $total_tagihan = 0, $total_tindakan = 0, $total_resep = 0;

    public function mount(Registrasi $data)
    {
        $this->data = $data;
        $this->dataBarang = BarangClass::getBarang('apotek');

        $this->dataMetodeBayar = MetodeBayar::orderBy('nama')->get(['id', 'nama'])->toArray();

        $this->tindakan = $data->tindakan->map(function ($q) {
            return [
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
            ];
        })->toArray();

        $this->dataNakes = Nakes::orderBy('nama')->get(['id', 'nama', 'dokter'])->toArray();

        $this->resep = collect($data->resepobat)
            ->groupBy('resep')
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'catatan' => $first->catatan,
                    'nama' => $first->nama,
                    'barang' => $group->map(function ($r) {
                        return [
                            'id' => $r->barang_satuan_id,
                            'harga' => $r->harga,
                            'qty' => $r->qty,
                            'subtotal' => $r->harga * $r->qty,
                        ];
                    })->toArray(),
                ];
            })->values()->toArray();
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'metode_bayar' => 'required',
            'cash' => $this->metode_bayar == 1 ? 'required|numeric|min:' . $this->total_tagihan : 'nullable',
            'keterangan_pembayaran' => $this->metode_bayar != 1 ? 'required|string|max:1000' : 'nullable',
            'tindakan.*.dokter_id' => 'required|exists:nakes,id',
            'tindakan.*.perawat_id' => 'required|exists:nakes,id',
            'resep.*.barang.*.id' => 'required',
            'resep.*.barang.*.qty' => 'required',
        ]);
        DB::transaction(function () {
            $registrasi = $this->data;
            $id = (string) Str::uuid();

            $pembayaran = new Pembayaran();
            $pembayaran->id = $id;
            $pembayaran->jumlah = $total_jumlah;
            $pembayaran->registrasi_id = $registrasi->id; // asumsikan foreign key
            $pembayaran->metode = $this->metode_bayar;
            $pembayaran->keterangan_pembayaran = $this->keterangan_pembayaran;
            $pembayaran->save();

            // Ambil metode bayar aktif, gunakan eager query
            $metodeBayar = collect($this->dataMetodeBayar)->firstWhere('id', $this->metode_bayar);

            // Jurnal pendapatan, lempar objek registrasi & metode bayar
            $this->jurnalPendapatan($registrasi, $metodeBayar);
        });

        session()->flash('success', 'Berhasil menyimpan data');
        return redirect()->to('/klinik/kasir'); // pengalihan lebih eksplisit
    }

    /**
     * Jurnal pendapatan barang dan diskon
     */
    private function jurnalPendapatan($data, $metodeBayar)
    {
        // Validasi $metodeBayar
        if (!$metodeBayar || empty($metodeBayar['id'])) return;

        $id = (string) Str::uuid();
        $jurnalDetail = [];

        // Inisialisasi total barang dan diskon
        $totalBarang = 0;
        $totalDiskon = 0;
        $akunDetails = [];

        // Jika ada field barang, proses; jika tidak lewati.
        // asumsikan $this->barang adalah daftar barang penjualan (array), jika tidak ada, skip.

        if (!empty($this->dataBarang)) {
            foreach ($this->dataBarang as $barang) {
                if (!empty($barang['kode_akun_penjualan_id'])) {
                    $key = $barang['kode_akun_penjualan_id'];
                    if (!isset($akunDetails[$key])) {
                        $akunDetails[$key] = [
                            'kode_akun_id' => $barang['kode_akun_penjualan_id'],
                            'total' => 0,
                        ];
                    }
                    $akunDetails[$key]['total'] += ($barang['harga'] ?? 0) * ($barang['qty'] ?? 0);
                    $totalBarang += ($barang['harga'] ?? 0) * ($barang['qty'] ?? 0);
                }
                if (!empty($barang['diskon'])) {
                    $totalDiskon += $barang['diskon'];
                }
            }
        }

        foreach ($akunDetails as $item) {
            $jurnalDetail[] = [
                'jurnal_id' => $id,
                'debet' => 0,
                'kredit' => $item['total'],
                'kode_akun_id' => $item['kode_akun_id'],
            ];
        }

        if ($totalDiskon > 0) {
            $jurnalDetail[] = [
                'jurnal_id' => $id,
                'debet' => $totalDiskon,
                'kredit' => 0,
                'kode_akun_id' => '44100'
            ];
        }

        if (!empty($metodeBayar['id'])) {
            $jurnalDetail[] = [
                'jurnal_id' => $id,
                'debet' => $totalBarang - $totalDiskon,
                'kredit' => 0,
                'kode_akun_id' => $metodeBayar['id']
            ];
        }

        // Catat jurnal dengan class, asumsikan JurnalClass tersedia
        if (class_exists('\App\Class\JurnalClass')) {
            \App\Class\JurnalClass::insert($id, 'Penjualan', [
                'tanggal' => now(),
                'uraian' => 'Penjualan Barang Bebas ' . ($data->id ?? ''),
                'unit_bisnis' => 'Apotek',
                'referensi_id' => $data->id ?? null,
                'pengguna_id' => auth()->id(),
            ], $jurnalDetail);
        }
    }

    public function render()
    {
        return view('livewire.klinik.kasir.form');
    }
}
