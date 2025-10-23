<?php

namespace App\Livewire\Klinik\Kasir;

use App\Models\Stok;
use App\Models\Nakes;
use Livewire\Component;
use App\Models\Tindakan;
use App\Models\ResepObat;
use App\Class\BarangClass;
use App\Models\Pembayaran;
use App\Models\Registrasi;
use App\Models\MetodeBayar;
use App\Models\TindakanAlatBarang;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;

    public $data;
    public $tindakan = [], $dataTindakan = [], $dataNakes = [], $resep = [], $dataMetodeBayar = [];
    public $metode_bayar = 1, $cash = 0, $keterangan, $dataBarang = [];
    public $keterangan_pembayaran, $total_tagihan = 0, $total_tindakan = 0, $total_resep = 0, $diskon = 0;

    public function mount(Registrasi $data)
    {
        $this->data = $data;
        if ($data->pembayaran_id) {
            abort(404);
        }
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

        $this->dataNakes = Nakes::with('pegawai')->orderBy('nama')->get()->map(fn($q) => [
            'id' => $q->id,
            'dokter' => $q->dokter,
            'nama' => $q->pegawai ? $q->pegawai->nama : $q->nama,
        ])->toArray();

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
                            'nama' => collect($this->dataBarang)->firstWhere('id', $r->barang_satuan_id)['nama'],
                            'satuan' => collect($this->dataBarang)->firstWhere('id', $r->barang_satuan_id)['satuan'],
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
            // 'tindakan.*.dokter_id' => function ($attribute, $value, $fail) {
            //     $index = explode('.', $attribute)[1];
            //     if (
            //         !isset($this->tindakan[$index]['dokter_id'])
            //     ) {
            //         $fail('Dokter wajib dipilih untuk tindakan ' . $this->tindakan[$index]['nama'] . '.');
            //     }
            // },
            'tindakan.*.perawat_id' => 'required',
            'resep.*.barang.*.qty' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) {
                    $parts = explode('.', $attribute); // ['resep', '0', 'barang', '1', 'qty']
                    $resepIndex = $parts[1] ?? null;
                    $barangIndex = $parts[3] ?? null;

                    if (
                        !is_numeric($resepIndex) ||
                        !isset($this->resep[$resepIndex]['barang'][$barangIndex])
                    ) {
                        return;
                    }
                    $barangResep = $this->resep[$resepIndex]['barang'][$barangIndex];

                    $barang = collect($this->dataBarang)->firstWhere('id', $barangResep['id']);
                    if (!$barang) return;

                    $stokTersedia = Stok::where('barang_id', $barang['barang_id'])
                        ->available()
                        ->count();

                    if (($value * $barang['rasio_dari_terkecil']) > $stokTersedia) {
                        $stokAvailable = $stokTersedia / $barang['rasio_dari_terkecil'];
                        $fail("Stok {$barang['nama']} tidak mencukupi. Tersisa {$stokAvailable} {$barang['satuan']}.");
                    }
                }
            ],
        ]);
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
            $pembayaran->keterangan = $this->keterangan_pembayaran;
            $pembayaran->metode_bayar = $metodeBayar->nama;
            $pembayaran->bayar = $metodeBayar->nama == 'Cash' ? $this->cash : ($this->total_tagihan);
            $pembayaran->total_resep = $this->total_resep;
            $pembayaran->total_tindakan = $this->total_tindakan;
            $pembayaran->diskon = $this->diskon;
            $pembayaran->total_tagihan = $this->total_tagihan;
            $pembayaran->bebas = 0;
            $pembayaran->pengguna_id = auth()->id();
            $pembayaran->save();

            foreach ($this->tindakan as $tindakan) {
                Tindakan::where('tarif_tindakan_id', $tindakan['id'])->update(['diskon' => $tindakan['diskon']]);
            }

            Registrasi::where('id', $this->data->id)->update(['pembayaran_id' => $pembayaran->id]);
            Tindakan::where('id', $this->data->id)->update(['pembayaran_id' => $pembayaran->id]);

            ResepObat::where('id', $this->data->id)->update(['pembayaran_id' => $pembayaran->id]);

            foreach (TindakanAlatBarang::whereNotNull('barang_satuan_id')->where('tindakan_id', $this->data->id)->get() as $alatBarang) {
                $barang = collect($this->dataBarang)->firstWhere('id', $alatBarang->barang_satuan_id);
                BarangClass::stokKeluar([
                    'qty' => $alatBarang->qty,
                    'harga' => $alatBarang->biaya,
                    'barang_id' => $barang['barang_id'],
                    'barang_satuan_id' => $alatBarang->barang_satuan_id,
                    'rasio_dari_terkecil' => $barang['rasio_dari_terkecil'],
                ], $pembayaran->id);
            }
            foreach ($this->resep as $resep) {
                $barang = collect($resep['barang'])->map(function ($q) {
                    $brg = collect($this->dataBarang)->firstWhere('id', $q['id']);
                    return [
                        'qty' => $q['qty'],
                        'harga' => $q['harga'],
                        'barang_id' => $brg['barang_id'],
                        'barang_satuan_id' => $q['id'],
                        'rasio_dari_terkecil' => $brg['rasio_dari_terkecil'],
                    ];
                })->toArray();
                BarangClass::stokKeluar($barang, $pembayaran->id);
            }
            $data = Registrasi::findOrFail($this->data->id);
            $cetak = view('livewire.klinik.kasir.cetak', [
                'cetak' => true,
                'data' => $data,
            ])->render();
            session()->flash('cetak', $cetak);
            session()->flash('success', 'Berhasil menyimpan data');
        });

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
