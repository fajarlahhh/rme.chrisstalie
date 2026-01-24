<?php

namespace App\Livewire\Penjualan;

use App\Models\Stok;
use Livewire\Component;
use App\Class\JurnalkeuanganClass;
use App\Models\Pembayaran;
use App\Models\MetodeBayar;
use App\Class\BarangClass;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Index extends Component
{
    use CustomValidationTrait;
    public $dataBarang = [], $dataMetodeBayar = [];
    public $barang = [];
    public $keterangan;
    public $metode_bayar = 1;
    public $cash = 0;
    public $diskon = 0;
    public $total_tagihan = 0;
    public $pasien_id;
    public $keterangan_pembayaran;
    public $tanggal;
    public function submit()
    {
        $this->validateWithCustomMessages(
            [
                'metode_bayar' => 'required',
                'cash' => $this->metode_bayar == 1 ? 'required|numeric|min:' . ($this->total_tagihan) : 'nullable',
                'keterangan_pembayaran' => $this->metode_bayar != 1 ? 'required|string|max:1000' : 'nullable',
                'barang' => 'required|array',
                'barang.*.id' => 'required|distinct',
                'barang.*.harga' => 'required|numeric',
                'barang.*.qty' => [
                    'required',
                    'numeric',
                    'min:1',
                    function ($attribute, $value, $fail) {
                        $index = explode('.', $attribute)[1];
                        $barang = $this->barang[$index] ?? null;
                        if (!$barang) return;

                        $barang = collect($this->dataBarang)->firstWhere('id', $barang['id']);
                        $stokTersedia = Stok::where('barang_id', $barang['barang_id'])
                            ->available()
                            ->count();
                        if (($value * ($barang['rasio_dari_terkecil'] ?? 1)) > $stokTersedia) {
                            $stokAvailable = $stokTersedia / $barang['rasio_dari_terkecil'];
                            $fail("Stok {$barang['nama']} tidak mencukupi. Tersisa {$stokAvailable} {$barang['satuan']}.");
                        }
                    }
                ],
            ],
        );

        DB::transaction(function () {
            $dataTerakhir = Pembayaran::where('tanggal', 'like',  substr($this->tanggal, 0, 7) . '%')->orderBy('id', 'desc')->first();

            $metodeBayar = MetodeBayar::findOrFail($this->metode_bayar);

            if ($dataTerakhir) {
                $id = $dataTerakhir->id + 1;
            } else {
                $id = date('Ym') . '00001';
            }
            $pembayaran = new Pembayaran();
            $pembayaran->id = $id;
            $pembayaran->keterangan = $this->keterangan;
            $pembayaran->metode_bayar = $metodeBayar->nama;
            $pembayaran->bayar = $metodeBayar->nama == 'Cash' ? $this->cash : ($this->total_tagihan);
            $pembayaran->total_harga_barang = $this->total_tagihan + $this->diskon;
            $pembayaran->diskon = $this->diskon;
            $pembayaran->total_tagihan = $this->total_tagihan;
            $pembayaran->kode_akun_id = $metodeBayar->kode_akun_id;
            $pembayaran->bebas = 1;
            $pembayaran->pasien_id = $this->pasien_id;
            $pembayaran->pengguna_id = auth()->id();
            $pembayaran->tanggal = $this->tanggal;
            $pembayaran->save();

            $barang = collect($this->barang)->map(function ($q) {
                $brg = collect($this->dataBarang)->firstWhere('id', $q['id']);
                return [
                    'qty' => $q['qty'],
                    'harga' => $q['harga'],
                    'kode_akun_penjualan_id' => $brg['kode_akun_penjualan_id'],
                    'kode_akun_modal_id' => $brg['kode_akun_modal_id'],
                    'kode_akun_id' => $brg['kode_akun_id'],
                    'barang_id' => $brg['barang_id'],
                    'barang_satuan_id' => $q['id'],
                    'rasio_dari_terkecil' => $brg['rasio_dari_terkecil'],
                ];
            })->toArray();

            $hpp = BarangClass::stokKeluar($barang, $pembayaran->id, $this->tanggal);

            $this->jurnalPendapatan($pembayaran, $metodeBayar, $hpp);

            $cetak = view('livewire.penjualan.cetak', [
                'cetak' => true,
                'data' => Pembayaran::findOrFail($pembayaran->id),
            ])->render();
            session()->flash('cetak', $cetak);
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/penjualan');
    }

    private function jurnalPendapatan($pembayaran, $metodeBayar, $hpp)
    {
        $jurnalKeuanganDetail = [];

        // foreach (
        //     collect($this->barang)->groupBy('kode_akun_penjualan_id')->map(fn($q) => [
        //         'kode_akun_id' => $q->first()['kode_akun_penjualan_id'],
        //         'total' => $q->sum(fn($q) => $q['harga'] * $q['qty']),
        //     ]) as $barang
        // ) {
        //     $jurnalKeuanganDetail[] = [
        //         'debet' => 0,
        //         'kredit' => $barang['total'],
        //         'kode_akun_id' => $barang['kode_akun_id']
        //     ];
        // }
        if ($this->diskon > 0) {
            $jurnalKeuanganDetail[] = [
                'debet' => $this->diskon,
                'kredit' => 0,
                'kode_akun_id' => '44000'
            ];
        }
        $jurnalKeuanganDetail[] = [
            'debet' => $this->total_tagihan,
            'kredit' => 0,
            'kode_akun_id' => $metodeBayar->kode_akun_id
        ];
        $jurnalKeuanganDetail = array_merge($jurnalKeuanganDetail, collect($hpp)->map(function ($q) {
            return [
                'kode_akun_id' => $q['kode_akun_id'],
                'debet' =>  $q['debet'],
                'kredit' => $q['kredit'],
            ];
        })->toArray());

        JurnalkeuanganClass::insert(
            jenis: 'Pendapatan',
            sub_jenis: 'Pendapatan Penjualan Barang Bebas',
            tanggal: $this->tanggal,
            uraian: 'Pendapatan Penjualan Barang Bebas ' . $pembayaran->id,
            system: 1,
            aset_id: null,
            pemesanan_pengadaan_id: null,
            stok_masuk_id: null,
            pembayaran_id: $pembayaran->id,
            penggajian_id: null,
            pelunasan_pemesanan_pengadaan_id: null,
            stok_keluar_id: null,
            detail: $jurnalKeuanganDetail
        );
    }

    public function mount()
    {
        $this->dataMetodeBayar = MetodeBayar::get()->toArray();
        $this->dataBarang = BarangClass::getBarang('Apotek', null, 0);
        $this->tanggal = date('Y-m-d');
    }

    public function render()
    {
        return view('livewire.penjualan.index');
    }
}
