<?php

namespace App\Livewire\Klinik\Kasir;

use App\Models\Stok;
use App\Models\Nakes;
use Livewire\Component;
use App\Models\KodeAkun;
use App\Models\Tindakan;
use App\Models\ResepObat;
use App\Class\BarangClass;
use App\Class\JurnalClass;
use App\Models\Pembayaran;
use App\Models\Registrasi;
use App\Models\MetodeBayar;
use Illuminate\Support\Str;
use App\Models\AsetPenyusutan;
use App\Models\PembayaranDetail;
use App\Models\TindakanAlatBarang;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;

    public $data;
    public $tindakan = [], $dataTindakan = [], $dataNakes = [], $resep = [], $dataMetodeBayar = [];
    public $metode_bayar = 1, $cash = 0, $keterangan, $dataBarang = [];
    public $keterangan_pembayaran, $total_tagihan = 0, $total_tindakan = 0, $total_resep = 0, $diskon = 0, $bahan = [], $alat = [];
    public $dataKodeAkunPenyusutan = [];

    public function mount(Registrasi $data)
    {
        $this->dataKodeAkunPenyusutan = KodeAkun::where('parent_id', '15200')->detail()->get()->toArray();
        $this->data = $data;
        if ($data->pembayaran) {
            abort(404);
        }
        $barang = collect(BarangClass::getBarang());
        $this->dataBarang = $barang->where('persediaan', 'Apotek')->toArray();
        $barangKlinik = $barang->where('persediaan', 'Klinik')->toArray();

        $this->dataMetodeBayar = MetodeBayar::orderBy('nama')->get(['id', 'nama'])->toArray();

        $this->dataNakes = Nakes::with('pegawai')->orderBy('nama')->get()->map(fn($q) => [
            'id' => $q->id,
            'dokter' => $q->dokter,
            'nama' => $q->pegawai ? $q->pegawai->nama : $q->nama,
        ])->toArray();

        $this->tindakan = $data->tindakan->map(function ($q) {
            return [
                'id' => $q->id,
                'tarif_tindakan_id' => $q->tarif_tindakan_id,
                'nama' => $q->tarifTindakan->nama,
                'diskon' => 0,
                'qty' => $q->qty,
                'kode_akun_id' => $q->tarifTindakan->kode_akun_id,
                'harga' => $q->harga,
                'catatan' => $q->catatan,
                'dokter_id' => $q->dokter_id,
                'perawat_id' => $q->perawat_id,
                'biaya_alat' => collect($q->tindakanAlatBarang)->whereNotNull('aset_id')->sum(function ($q) {
                    return $q->qty * $q->biaya;
                }),
                'biaya_alat_barang' => $q->biaya_alat_barang,
                'biaya_jasa_dokter' => $q->biaya_jasa_dokter,
                'biaya_jasa_perawat' => $q->biaya_jasa_perawat,
                'biaya' => $q->biaya,
            ];
        })->toArray();
        $this->resep = collect($data->resepObat)
            ->groupBy('resep')
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'catatan' => $first->catatan,
                    'nama' => $first->nama,
                    'barang' => $group->map(function ($r) {
                        $barang = collect($this->dataBarang)->firstWhere('id', $r->barang_satuan_id);
                        if (!$barang) {
                            return [
                                'id' => null,
                                'nama' => 'Terjadi Kesalahan Resep Obat',
                                'satuan' => null,
                                'kode_akun_id' => null,
                                'kode_akun_penjualan_id' => null,
                                'kode_akun_modal_id' => null,
                                'harga' => null,
                                'qty' => null,
                                'subtotal' => null,
                            ];
                        }
                        return [
                            'id' => $r->barang_satuan_id,
                            'nama' => $barang['nama'],
                            'satuan' => $barang['satuan'],
                            'kode_akun_id' => $barang['kode_akun_id'],
                            'kode_akun_penjualan_id' => $barang['kode_akun_penjualan_id'],
                            'kode_akun_modal_id' => $barang['kode_akun_modal_id'],
                            'harga' => $r->harga,
                            'qty' => $r->qty,
                            'subtotal' => $r->harga * $r->qty,
                        ];
                    })->toArray(),
                ];
            })->values()->toArray();

        $this->bahan = TindakanAlatBarang::whereNotNull('barang_satuan_id')->whereIn('tindakan_id', collect($this->tindakan)->pluck('id'))->get()->map(function ($q) use ($barangKlinik) {
            $barang = collect($barangKlinik)->firstWhere('id', $q->barang_satuan_id);
            return [
                'barang_id' => $barang['barang_id'],
                'nama' => $barang['nama'],
                'satuan' => $barang['satuan'],
                'kode_akun_id' => $barang['kode_akun_id'],
                'kode_akun_penjualan_id' => $barang['kode_akun_penjualan_id'],
                'kode_akun_modal_id' => $barang['kode_akun_modal_id'],
                'qty' => $q->qty,
                'biaya' => $q->biaya,
                'barang_satuan_id' => $q->barang_satuan_id,
                'rasio_dari_terkecil' => $q->rasio_dari_terkecil,
            ];
        })->toArray();

        $this->alat = TindakanAlatBarang::whereNotNull('aset_id')->where('biaya', '>', 0)->with('alat')->whereIn('tindakan_id', collect($this->tindakan)->pluck('id'))->get()->map(function ($q) {
            return [
                'id' => $q->aset_id,
                'nama' => $q->alat->nama,
                'metode_penyusutan' => $q->alat->metode_penyusutan,
                'kode_akun_penyusutan_id' => $q->alat->kode_akun_penyusutan_id,
                'qty' => $q->qty,
                'biaya' => $q->biaya,
                'kode_akun_id' => $q->alat->kode_akun_id,
            ];
        })->toArray();
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'metode_bayar' => 'required',
            'cash' => $this->metode_bayar == 1 ? 'required|numeric|min:' . $this->total_tagihan : 'nullable',
            'keterangan_pembayaran' => $this->metode_bayar != 1 ? 'required|max:1000' : 'nullable',
            // 'tindakan.*.dokter_id' => function ($attribute, $value, $fail) {
            //     $index = explode('.', $attribute)[1];
            //     if (
            //         !isset($this->tindakan[$index]['dokter_id'])
            //     ) {
            //         $fail('Dokter wajib dipilih untuk tindakan ' . $this->tindakan[$index]['nama'] . '.');
            //     }
            // },
            'tindakan.*.perawat_id' => function ($attribute, $value, $fail) {
                $index = explode('.', $attribute)[1];
                if (
                    isset($this->tindakan[$index]['biaya_jasa_perawat']) &&
                    $this->tindakan[$index]['biaya_jasa_perawat'] > 0 &&
                    (empty($value) || $value == "" || $value == null)
                ) {
                    $fail('Perawat wajib dipilih untuk tindakan ' . $this->tindakan[$index]['nama']);
                }
            },
            'bahan.*.qty' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $bahan = $this->bahan[$index] ?? null;
                    if (!$bahan) return;

                    $stokTersedia = Stok::where('barang_id', $bahan['barang_id'])
                        ->available()
                        ->count();
                    if (($value * ($bahan['rasio_dari_terkecil'] ?? 1)) > $stokTersedia) {
                        $stokAvailable = $stokTersedia / $bahan['rasio_dari_terkecil'];
                        $fail("Stok bahan {$bahan['nama']} tidak mencukupi. Tersisa {$stokAvailable} {$bahan['satuan']}.");
                    }
                }
            ],
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
            // Ambil data pembayaran terakhir di bulan berjalan
            if (Pembayaran::where('registrasi_id', $this->data->id)->count() > 0) {
                abort(400, 'Pembayaran sudah ada');
            }

            $dataTerakhir = Pembayaran::where('created_at', 'like', date('Y-m') . '%')->orderByDesc('id')->first();

            $metodeBayar = MetodeBayar::findOrFail($this->metode_bayar);

            $id = $dataTerakhir ? ($dataTerakhir->id + 1) : (date('Ym') . '00001');

            $pembayaran = new Pembayaran();
            $pembayaran->id = $id;
            $pembayaran->keterangan = $this->keterangan_pembayaran;
            $pembayaran->metode_bayar = $metodeBayar->nama;
            $pembayaran->bayar = $metodeBayar->nama == 'Cash' ? $this->cash : $this->total_tagihan;
            $pembayaran->total_resep = $this->total_resep;
            $pembayaran->total_tindakan = $this->total_tindakan;
            $pembayaran->diskon = $this->diskon;
            $pembayaran->total_tagihan = $this->total_tagihan;
            $pembayaran->kode_akun_id = $metodeBayar->kode_akun_id;
            $pembayaran->bebas = 0;
            $pembayaran->pasien_id = $this->data->pasien_id;
            $pembayaran->registrasi_id = $this->data->id;
            $pembayaran->pengguna_id = auth()->id();
            $pembayaran->save();

            // Optimisasi: Update diskon tindakan sekaligus dengan filter id & tarif_tindakan_id
            foreach ($this->tindakan as $t) {
                // Kolom id -> id registrasi kunjungan
                Tindakan::where('id', $t['id'])->update([
                    'diskon' => $t['diskon'],
                    'perawat_id' => $t['perawat_id'] && $t['perawat_id'] != '-' ? $t['perawat_id'] : null
                ]);
            }

            //Jurnal Kas
            $detail[] = [
                'debet' => $this->total_tagihan,
                'kredit' => 0,
                'kode_akun_id' => $metodeBayar->kode_akun_id
            ];

            //Jurnal Kewajiban Biaya Dokter & Perawat
            $detail = array_merge($detail, collect($this->tindakan)->whereNotNull('dokter_id')->map(function ($q) {
                return [
                    'kode_akun_id' => '21300',
                    'debet' => 0,
                    'kredit' => $q['biaya_jasa_dokter'] * $q['qty'],
                ];
            })->all());

            $detail = array_merge($detail, collect($this->tindakan)
                ->where('perawat_id', '!=', '-')->whereNotNull('perawat_id')->map(function ($q) {
                    return [
                        'kode_akun_id' => '21400',
                        'debet' => 0,
                        'kredit' => $q['biaya_jasa_perawat'] * $q['qty'],
                    ];
                })->all());

            //Jurnal Pendapatan Tindakan
            $detail = array_merge($detail, collect($this->tindakan)->map(function ($q) {
                return [
                    'kode_akun_id' => $q['kode_akun_id'],
                    'debet' => 0,
                    'kredit' => ($q['biaya'] + $q['biaya_alat'] - $q['biaya_alat_barang'] - ($q['dokter_id'] ? $q['biaya_jasa_dokter'] : 0) - ($q['perawat_id'] && $q['perawat_id'] != '-' ? $q['biaya_jasa_perawat'] : 0)) * $q['qty'],
                ];
            })->all());

            // Stok keluar & detail bahan/alat diinsert batch
            $hppBahan = BarangClass::stokKeluar(collect($this->bahan)->map(function ($q) {
                return [
                    'qty' => $q['qty'],
                    'harga' => $q['biaya'],
                    'barang_id' => $q['barang_id'],
                    'barang_satuan_id' => $q['barang_satuan_id'],
                    'rasio_dari_terkecil' => $q['rasio_dari_terkecil'],
                    'kode_akun_id' => $q['kode_akun_id'],
                    'kode_akun_penjualan_id' => $q['kode_akun_penjualan_id'],
                    'kode_akun_modal_id' => $q['kode_akun_modal_id'],
                ];
            })->toArray(), $pembayaran->id);

            //Jurnal Penyusutan Alat
            $detail = array_merge($detail, collect($this->alat)->where('metode_penyusutan', 'Satuan Hasil Produksi')->map(function ($q) {
                return [
                    'kode_akun_id' => $q['kode_akun_penyusutan_id'],
                    'debet' => 0,
                    'kredit' => $q['biaya'] * $q['qty'],
                ];
            })->all());
            $detail = array_merge($detail, collect($this->alat)->where('metode_penyusutan', 'Satuan Hasil Produksi')->map(function ($q) {
                return [
                    'kode_akun_id' => '65900',
                    'debet' => $q['biaya'] * $q['qty'],
                    'kredit' => 0,
                ];
            })->all());

            // Diskon
            $detail[] = [
                'kode_akun_id' => '44000',
                'debet' => $this->diskon,
                'kredit' => 0,
            ];

            //Pembayaran Barang Dagang
            foreach ($this->resep as $resep) {
                $barangRaw = $resep['barang'] ?? [];
                $barangMap = collect($barangRaw)->map(function ($q) {
                    $brg = collect($this->dataBarang)->firstWhere('id', $q['id']);
                    return [
                        'qty' => $q['qty'],
                        'harga' => $q['harga'],
                        'kode_akun_id' => $brg['kode_akun_id'],
                        'kode_akun_penjualan_id' => $brg['kode_akun_penjualan_id'],
                        'kode_akun_modal_id' => $brg['kode_akun_modal_id'],
                        'barang_id' => $brg['barang_id'],
                        'barang_satuan_id' => $q['id'],
                        'rasio_dari_terkecil' => $brg['rasio_dari_terkecil'],
                    ];
                })->all();
                if (count($barangMap)) {
                    $hppBarang = BarangClass::stokKeluar($barangMap, $pembayaran->id);
                    $detail = array_merge($detail, collect($hppBarang)->map(function ($q) {
                        return [
                            'kode_akun_id' => $q['kode_akun_id'],
                            'debet' => $q['debet'],
                            'kredit' => $q['kredit'],
                        ];
                    })->all());
                }
            }

            $detail = array_merge($detail, collect($hppBahan)->map(function ($q) {
                return [
                    'kode_akun_id' => $q['kode_akun_id'],
                    'debet' => $q['debet'],
                    'kredit' => $q['kredit'],
                ];
            })->all());
            $this->jurnalPendapatan($pembayaran, $metodeBayar, $detail);
            // Ambil data untuk cetak hanya sekali, pakai find (sudah pasti ada), dan simpan flash
            $data = Pembayaran::findOrFail($pembayaran->id);
            $cetak = view('livewire.klinik.kasir.cetak', [
                'cetak' => true,
                'data' => $data,
            ])->render();
            session()->flash('cetak', $cetak);
            session()->flash('success', 'Berhasil menyimpan data');
        });

        return redirect()->to('/klinik/kasir');
    }

    private function jurnalPendapatan($pembayaran, $metodeBayar, $detail)
    {
        $id = Str::uuid();

        $jurnalDetail = collect($detail)->map(function ($q) use ($id) {
            return [
                'jurnal_id' => $id,
                'debet' => $q['debet'],
                'kredit' => $q['kredit'],
                'kode_akun_id' => $q['kode_akun_id'],
            ];
        })->all();

        $jurnal = JurnalClass::insert(
            jenis: 'Pendapatan Pasien Klinik',
            sub_jenis: 'Pendapatan',
            tanggal: now(),
            uraian: 'Pendapatan Pasien Klinik No. Nota ' . $pembayaran->id,
            system: 1,
            pembayaran_id: $pembayaran->id,
            penggajian_id: null,
            pelunasan_pembelian_id: null,
            aset_id: null,
            pembelian_id: null,
            stok_masuk_id: null,
            stok_keluar_id: null,
            detail: collect($jurnalDetail)->groupBy('kode_akun_id')->map(function ($q) {
                return [
                    'debet' => $q->sum('debet'),
                    'kredit' => $q->sum('kredit'),
                    'kode_akun_id' => $q->first()['kode_akun_id'],
                ];
            })->values()->toArray()
        );

        $asetPenyusutan = [];
        foreach (
            collect($this->alat)->where('metode_penyusutan', 'Satuan Hasil Produksi')->all() as $alat
        ) {
            for ($i = 0; $i < $alat['qty']; $i++) {
                $asetPenyusutan[] = [
                    'aset_id' => $alat['id'],
                    'nilai' => $alat['biaya'],
                    'jurnal_id' => $jurnal->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        AsetPenyusutan::insert($asetPenyusutan);
    }

    public function render()
    {
        return view('livewire.klinik.kasir.form');
    }
}
