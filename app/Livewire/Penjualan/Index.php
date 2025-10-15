<?php

namespace App\Livewire\Penjualan;

use App\Models\Stok;
use App\Models\Barang;
use Livewire\Component;
use App\Models\Penjualan;
use App\Class\JurnalClass;
use App\Models\StokKeluar;
use App\Models\MetodeBayar;
use Illuminate\Support\Str;
use App\Models\PenjualanDetail;
use App\Class\BarangClass;
use App\Models\Pembayaran;
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
    public $keterangan_pembayaran;

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
                // 'barang.*.qty' => [
                //     'required',
                //     'numeric',
                //     'min:1',
                //     function ($attribute, $value, $fail) {
                //         $index = explode('.', $attribute)[1];
                //         $barang = $this->barang[$index] ?? null;
                //         if (!$barang) return;
                //         // Cek stok tersedia
                //         $stokTersedia = Stok::where('barang_id', $barang['id'])
                //             ->available()
                //             ->count();
                //         $barang = collect($this->dataBarang)->firstWhere('id', $barang['id']);
                //         if (($value / ($barang['rasio_dari_terkecil'] ?? 1)) > $stokTersedia) {
                //             $stokAvailable = $stokTersedia / $barang['rasio_dari_terkecil'];
                //             $fail("Stok {$barang['nama']} tidak mencukupi. Tersisa {$stokAvailable} {$barang['satuan']}.");
                //         }
                //     }
                // ],
            ],
            $this->getValidationMessages(),
            $this->getValidationAttributes()
        );

        DB::transaction(function () {
            $dataTerakhir = Pembayaran::where('created_at', 'like',  date('Y-m') . '%')->orderBy('id', 'desc')->first();

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
            $pembayaran->pengguna_id = auth()->id();
            $pembayaran->save();

            $barang = collect($this->barang)->map(function ($q) {
                $brg = collect($this->dataBarang)->firstWhere('id', $q['id']);
                return [
                    'qty' => $q['qty'],
                    'harga' => $q['harga'],
                    'barang_id' => $brg['barang_id'],
                    'barang_satuan_id' => $q['id'],
                    'rasio_dari_terkecil' => $brg['rasio_dari_terkecil'],
                ];
            })->toArray();
            foreach ($barang as $brg) {
                $stokKeluarId = Str::uuid();
                StokKeluar::insert([
                    'id' => $stokKeluarId,
                    'tanggal' => now(),
                    'qty' => $brg['qty'],
                    'pembayaran_id' => $pembayaran->id,
                    'barang_id' => $brg['barang_id'],
                    'harga' => $brg['harga'],
                    'pengguna_id' => auth()->id(),
                    'barang_satuan_id' => $brg['barang_satuan_id'],
                    'rasio_dari_terkecil' => $brg['rasio_dari_terkecil'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Stok::where('barang_id', $brg['barang_id'])->available()->orderBy('tanggal_kedaluarsa', 'asc')->limit($brg['qty'])->update([
                    'tanggal_keluar' => now(),
                    'stok_keluar_id' => $stokKeluarId,
                ]);
            }

            $this->jurnalPendapatan($pembayaran, $metodeBayar);

            $cetak = view('livewire.penjualan.cetak', [
                'cetak' => true,
                'data' => Pembayaran::findOrFail($pembayaran->id),
            ])->render();
            session()->flash('cetak', $cetak);
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('penjualan');
    }

    private function jurnalPendapatan($pembayaran, $metodeBayar)
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
            'debet' => $this->total_tagihan,
            'kredit' => 0,
            'kode_akun_id' => $metodeBayar->kode_akun_id
        ];
        JurnalClass::insert($id, 'Penjualan Barang Bebas', [
            'tanggal' => now(),
            'uraian' => 'Penjualan Barang Bebas ' . $pembayaran->id,
            'unit_bisnis' => 'Apotek',
            'referensi_id' => $pembayaran->id,
            'pengguna_id' => auth()->id(),
        ], $jurnalDetail);
    }

    public function mount()
    {
        $this->dataMetodeBayar = MetodeBayar::get()->toArray();
        $this->dataBarang = BarangClass::getBarang('apotek');
    }

    public function render()
    {
        return view('livewire.penjualan.index');
    }

    /**
     * Custom validation messages berbahasa Indonesia
     */
    protected function getValidationMessages()
    {
        return [
            'metode_bayar.required' => 'Metode pembayaran wajib dipilih.',
            'cash.required' => 'Jumlah uang tunai wajib diisi.',
            'cash.numeric' => 'Jumlah uang tunai harus berupa angka.',
            'cash.min' => 'Jumlah uang tunai minimal Rp ' . number_format($this->total_tagihan, 0, ',', '.') . '.',
            'barang.required' => 'Tidak ada barang yang dipilih.',
            'barang.array' => 'Format data barang tidak valid.',
            'barang.*.id.required' => 'ID barang wajib diisi.',
            'barang.*.id.distinct' => 'Terdapat barang yang duplikat dalam daftar.',
            'barang.*.harga.required' => 'Harga barang wajib diisi.',
            'barang.*.harga.numeric' => 'Harga barang harus berupa angka.',
            'barang.*.qty.required' => 'Jumlah barang wajib diisi.',
            'barang.*.qty.numeric' => 'Jumlah barang harus berupa angka.',
            'barang.*.qty.min' => 'Jumlah barang minimal 1.',
        ];
    }

    /**
     * Custom validation attributes berbahasa Indonesia
     */
    protected function getValidationAttributes()
    {
        return [
            'metode_bayar' => 'metode pembayaran',
            'cash' => 'uang tunai',
            'barang' => 'daftar barang',
            'barang.*.id' => 'ID barang',
            'barang.*.harga' => 'harga barang',
            'barang.*.qty' => 'jumlah barang',
        ];
    }
}
