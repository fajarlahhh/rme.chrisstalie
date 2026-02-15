<?php

namespace App\Livewire\Kasir;

use App\Models\Stok;
use App\Models\Nakes;
use Livewire\Component;
use App\Models\Tindakan;
use App\Class\BarangClass;
use App\Models\Pembayaran;
use App\Models\Registrasi;
use App\Models\MetodeBayar;
use App\Class\JurnalkeuanganClass;
use App\Models\Pasien;
use App\Models\TindakanAlatBarang;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;
use App\Traits\KodeakuntransaksiTrait;

class Index extends Component
{
    use CustomValidationTrait, KodeakuntransaksiTrait;
    public $dataBarangApotek = [], $dataBarang = [], $dataMetodeBayar = [];
    public $barang = [];
    public $keterangan;
    public $metode_bayar = 1;
    public $metode_bayar_2 = 1;
    public $cash = 0;
    public $cash_2 = 0;
    public $total_tagihan = 0;
    public $pasien_id;
    public $tanggal;
    public $total_bayar = 0;

    public $dataPasienTindakanResepObat = [], $cari, $registrasi, $dataNakes = [], $tindakan = [], $resep = [], $bahan = [], $alat = [], $total_tindakan = 0, $total_resep = 0, $total_barang = 0, $total_diskon_tindakan = 0, $total_diskon_barang = 0;

    public function setRegistrasi($id)
    {
        $this->registrasi = Registrasi::find($id);
        if ($this->registrasi->pembayaran) {
            session()->flash('danger', 'Registrasi sudah memiliki pembayaran');
            return;
        } else {
            $this->barang = [];
            $this->pasien_id = $this->registrasi->pasien_id;

            $this->tindakan = $this->registrasi->tindakan->map(function ($q) {
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

            $this->resep = collect($this->registrasi->resepObat)
                ->groupBy('resep')
                ->map(function ($group) {
                    $first = $group->first();
                    return [
                        'resep' => $first->resep,
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

            $this->bahan = TindakanAlatBarang::whereNotNull('barang_satuan_id')->whereIn('tindakan_id', collect($this->tindakan)->pluck('id'))->get()->map(function ($q) {
                $barang = collect($this->dataBarang)->firstWhere('id', $q->barang_satuan_id);
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
            if (count($this->tindakan) > 0) {
                $this->dispatch('set-tindakan', data: $this->tindakan);
            }
            if (count($this->resep) > 0) {
                $this->dispatch('set-resep', data: $this->resep);
            }
            $this->total_tindakan = collect($this->tindakan)->sum(function ($q) {
                return $q['biaya'] * $q['qty'] - $q['diskon'];
            });
            $this->total_resep = collect($this->resep)->sum(function ($q) {
                return collect($q['barang'])->sum(function ($b) {
                    return $b['harga'] * $b['qty'];
                });
            });
            $this->dispatch('set-totaltindakan', data: $this->total_tindakan);
            $this->dispatch('set-totalresep', data: $this->total_resep);
        }
    }

    public function getDataPasienTindakanResepObat()
    {
        $this->dataPasienTindakanResepObat = Registrasi::with('pasien', 'nakes', 'pengguna', 'tindakan', 'resepObat', 'peracikanResepObat')
            ->whereDoesntHave('pembayaran')
            ->where(function ($query) {
                $query->whereDoesntHave('resepObat')
                    ->orWhere(function ($query2) {
                        $query2->whereHas('resepObat')
                            ->whereHas('peracikanResepObat');
                    });
            })
            ->where(fn($q) => $q->where('id', 'like', '%' . $this->cari . '%')
                ->orWhereHas('pasien', fn($r) => $r
                    ->where('nama', 'like', '%' . $this->cari . '%')
                    ->orWhere('id', 'like', '%' . $this->cari . '%')))
            ->orderBy('id', 'asc')->get()->toArray();
        $this->dispatch('pasien-tindakan-resep-obat', data: $this->dataPasienTindakanResepObat);
    }

    public function submit()
    {
        $this->total_bayar = $this->cash + ($this->cash_2 > 0 ? $this->cash_2 : 0);

        if ($this->total_bayar < $this->total_tagihan) {
            $this->addError('total_bayar', 'Total Pembayaran tidak mencukupi');
            return;
        }

        if ($this->registrasi) {
            if (Pembayaran::where('registrasi_id', $this->registrasi->id)->count() > 0) {
                abort(400, 'Pembayaran sudah ada');
            }

            $this->validateWithCustomMessages(
                [
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
                                $fail("Stok bahan {$bahan['nama']} tidak mencukupi. Tersisa {$stokAvailable} {$bahan['satuan']}. Yang dibutuhkan untuk tindakan  {$value} {$bahan['satuan']}.");
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
                                $fail("Stok {$barang['nama']} tidak mencukupi. Tersisa {$stokAvailable} {$barang['satuan']}. Yang dibutuhkan untuk resep {$this->resep[$resepIndex]['nama']} {$value} {$barang['satuan']}.");
                            }
                        }
                    ],
                ],
            );
        }

        if (collect($this->barang)->filter(fn($item) => !empty($item['id']))->count() > 0) {
            $this->validateWithCustomMessages(
                [
                    'barang' => 'nullable|array',
                    'barang.*.qty' => [
                        'required',
                        'numeric',
                        'min:1',
                        function ($attribute, $value, $fail) {
                            $index = explode('.', $attribute)[1];
                            $barang = $this->barang[$index] ?? null;
                            if (!$barang) return;

                            $barang = collect($this->dataBarangApotek)->firstWhere('id', $barang['id']);
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
        }

        $this->validateWithCustomMessages(
            [
                'metode_bayar' => 'required',
                'total_bayar' => 'required|numeric|min:' . $this->total_tagihan,
            ],
        );

        DB::transaction(function () {
            $dataTerakhir = Pembayaran::where('tanggal', 'like',  substr($this->tanggal, 0, 7) . '%')->orderBy('id', 'desc')->first();

            if ($dataTerakhir) {
                $id = $dataTerakhir->id + 1;
            } else {
                $id = date('Ym') . '00001';
            }
            $pembayaran = new Pembayaran();
            $pembayaran->id = $id;
            $pembayaran->total_barang = $this->total_barang + $this->total_diskon_barang;
            $pembayaran->total_tindakan = $this->total_tindakan + $this->total_diskon_tindakan;
            $pembayaran->total_harga_barang = $this->total_barang;
            $pembayaran->total_resep = $this->total_resep;
            $pembayaran->total_diskon_barang = $this->total_diskon_barang;
            $pembayaran->total_diskon_tindakan = $this->total_diskon_tindakan;
            $pembayaran->total_tagihan = $this->total_tagihan;
            $pembayaran->total_diskon = $this->total_diskon_tindakan + $this->total_diskon_barang;

            $pembayaran->metode_bayar = collect($this->dataMetodeBayar)->where('id', $this->metode_bayar)->first()['nama'];
            $pembayaran->metode_bayar_2 = $this->cash_2 > 0 ? collect($this->dataMetodeBayar)->where('id', $this->metode_bayar_2)->first()['nama'] : null;
            $pembayaran->bayar = $this->cash;
            $pembayaran->bayar_2 = $this->cash_2 > 0 ? $this->cash_2 : 0;
            $pembayaran->kode_akun_id = collect($this->dataMetodeBayar)->where('id', $this->metode_bayar)->first()['kode_akun_id'];
            $pembayaran->kode_akun_2_id = $this->cash_2 > 0 ? collect($this->dataMetodeBayar)->where('id', $this->metode_bayar_2)->first()['kode_akun_id'] : null;

            $pembayaran->pasien_id = $this->registrasi ? $this->registrasi->pasien_id : $this->pasien_id;
            $pembayaran->keterangan = $this->keterangan;
            $pembayaran->tanggal = $this->tanggal;
            $pembayaran->registrasi_id = $this->registrasi ? $this->registrasi->id : null;
            $pembayaran->pengguna_id = auth()->id();
            $pembayaran->save();

            $detail = $this->kas($pembayaran); // Kas dan diskon
            $detail = array_merge($detail, $this->tindakan($pembayaran->id)); // Pendapatan Tindakan
            $detail = array_merge($detail, $this->resep($pembayaran->id)); // Pendapatan Resep
            if (collect($this->barang)->filter(fn($item) => !empty($item['id']))->count() > 0) {
                $detail = array_merge($detail, $this->barangBebas($pembayaran->id)); // Pendapatan Barang Bebas
            }

            $this->jurnalKeuangan($pembayaran->id, (collect($detail)->groupBy('kode_akun_id')->map(fn($q) => [
                'kode_akun_id' => $q->first()['kode_akun_id'],
                'debet' => $q->sum('debet'),
                'kredit' => $q->sum('kredit'),
            ])->toArray()));

            $cetak = view('livewire.kasir.cetak', [
                'cetak' => true,
                'data' => Pembayaran::findOrFail($pembayaran->id),
            ])->render();
            session()->flash('cetak', $cetak);
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/kasir');
    }

    private function barangTindakan($pembayaranId)
    {
        return collect(BarangClass::stokKeluar(collect($this->bahan)->map(function ($q) {
            return [
                'barang_id' => $q['barang_id'],
                'barang_satuan_id' => $q['barang_satuan_id'],
                'qty' => $q['qty'],
                'harga' => 0,
                'diskon' => 0,
                'penjualan' => null,
                'kode_akun_id' => $q['kode_akun_id'],
                'kode_akun_penjualan_id' => $q['kode_akun_penjualan_id'],
                'kode_akun_modal_id' => $q['kode_akun_modal_id'],
                'rasio_dari_terkecil' => $q['rasio_dari_terkecil'],
            ];
        })->toArray(), $pembayaranId, $this->tanggal))->groupBy('kode_akun_id')->map(fn($q) => [
            'kode_akun_id' => $q->first()['kode_akun_id'],
            'debet' => $q->sum('debet'),
            'kredit' => $q->sum('kredit'),
        ])->toArray();
    }

    private function kas($pembayaran)
    {
        $pendapatan = [
            [
                'kode_akun_id' => $pembayaran->kode_akun_id,
                'debet' => $this->cash_2 > 0 ? $this->cash : $this->total_tagihan,
                'kredit' => 0,
            ]
        ];
        if ($this->cash_2 > 0) {
            $pendapatan = array_merge($pendapatan, [[
                'kode_akun_id' => $pembayaran->kode_akun_2_id,
                'debet' => $this->total_tagihan - $this->cash,
                'kredit' => 0,
            ]]);
        }

        $pendapatan = array_merge($pendapatan, [
            [
                'kode_akun_id' =>  $this->getKodeAkunTransaksiByTransaksi('Diskon Pendapatan')->kode_akun_id,
                'debet' => $this->total_diskon_barang + $this->total_diskon_tindakan,
                'kredit' => 0,
            ]
        ]);
        return $pendapatan;
    }

    private function tindakan($pembayaranId)
    {
        foreach ($this->tindakan as $t) {
            Tindakan::where('id', $t['id'])->update([
                'diskon' => $t['diskon'],
                'perawat_id' => $t['perawat_id'] && $t['perawat_id'] != '-' ? $t['perawat_id'] : null
            ]);
        }
        $data = [];

        $bahan = $this->barangTindakan($pembayaranId);

        $jasaDokter = collect($this->tindakan)->whereNotNull('dokter_id')->map(fn($q) => [
            'kode_akun_id' => collect($this->dataNakes)->firstWhere('id', $q['dokter_id'])['kode_akun_jasa_dokter_id'],
            'debet' => 0,
            'kredit' => $q['biaya_jasa_dokter'] * $q['qty'],
        ])->groupBy('kode_akun_id')->map(fn($q) => [
            'kode_akun_id' => $q->first()['kode_akun_id'],
            'debet' => $q->sum('debet'),
            'kredit' => $q->sum('kredit'),
        ])->toArray();

        $jasaPerawat = collect($this->tindakan)->whereNotNull('perawat_id')->map(fn($q) => [
            'kode_akun_id' => collect($this->dataNakes)->firstWhere('id', $q['perawat_id'])['kode_akun_jasa_perawat_id'],
            'debet' => 0,
            'kredit' => $q['biaya_jasa_perawat'] * $q['qty'],
        ])->groupBy('kode_akun_id')->map(fn($q) => [
            'kode_akun_id' => $q->first()['kode_akun_id'],
            'debet' => $q->sum('debet'),
            'kredit' => $q->sum('kredit'),
        ])->toArray();

        $hppJasaPelayan = [
            [
                'kode_akun_id' =>  $this->getKodeAkunTransaksiByTransaksi('HPP Jasa Pelayanan')->kode_akun_id,
                'debet' => collect($jasaDokter)->sum('kredit') + collect($jasaPerawat)->sum('kredit'),
                'kredit' => 0,
            ]
        ];

        $biayaPenyusutanAset = collect($this->alat)->where('metode_penyusutan', 'Satuan Hasil Produksi')->map(function ($q) {
            return [
                'kode_akun_id' => $this->getKodeAkunTransaksiByTransaksi('Biaya Penyusutan Aset')->kode_akun_id,
                'debet' => $q['biaya'] * $q['qty'],
                'kredit' => 0,
            ];
        })->all();

        $data = array_merge($data, $bahan); //bahan tindakan

        $data = array_merge($data, $jasaDokter); // Kewajiban Biaya Dokter

        $data = array_merge($data, $jasaPerawat); // Kewajiban Biaya Perawat

        $data = array_merge($data, $hppJasaPelayan); // HPP Jasa Pelayanan

        $data = array_merge($data, $biayaPenyusutanAset); // Biaya Penyusutan Aset

        $data = array_merge($data, collect($this->alat)->where('metode_penyusutan', 'Satuan Hasil Produksi')->map(function ($q) {
            return [
                'kode_akun_id' => $q['kode_akun_penyusutan_id'],
                'debet' => 0,
                'kredit' => $q['biaya'] * $q['qty'],
            ];
        })->all()); // HPP Aset  

        $data = array_merge($data, collect($this->tindakan)->map(function ($q) {
            return [
                'kode_akun_id' => $q['kode_akun_id'],
                'debet' => 0,
                'kredit' => $q['biaya'] * $q['qty'],
            ];
        })->all()); // Tindakan

        return collect($data)->groupBy('kode_akun_id')->map(fn($q) => [
            'kode_akun_id' => $q->first()['kode_akun_id'],
            'debet' => $q->sum('debet'),
            'kredit' => $q->sum('kredit'),
        ])->toArray();
    }

    private function resep($pembayaranId)
    {
        $data = [];
        foreach ($this->resep as $resep) {
            $barangRaw = $resep['barang'] ?? [];
            $barangMap = collect($barangRaw)->map(function ($q) {
                $brg = collect($this->dataBarang)->firstWhere('id', $q['id']);
                return [
                    'qty' => $q['qty'],
                    'harga' => $q['harga'],
                    'diskon' => 0,
                    'penjualan' => null,
                    'kode_akun_id' => $brg['kode_akun_id'],
                    'kode_akun_penjualan_id' => $brg['kode_akun_penjualan_id'],
                    'kode_akun_modal_id' => $brg['kode_akun_modal_id'],
                    'barang_id' => $brg['barang_id'],
                    'barang_satuan_id' => $q['id'],
                    'rasio_dari_terkecil' => $brg['rasio_dari_terkecil'],
                ];
            })->all();
            if (count($barangMap)) {
                $hpp = BarangClass::stokKeluar($barangMap, $pembayaranId, $this->tanggal);
                $data = array_merge($data, collect($hpp)->map(function ($q) {
                    return [
                        'kode_akun_id' => $q['kode_akun_id'],
                        'debet' => $q['debet'],
                        'kredit' => $q['kredit'],
                    ];
                })->all());
            }
        }

        return collect($data)->groupBy('kode_akun_id')->map(fn($q) => [
            'kode_akun_id' => $q->first()['kode_akun_id'],
            'debet' => $q->sum('debet'),
            'kredit' => $q->sum('kredit'),
        ])->toArray();
    }

    private function barangBebas($pembayaranId)
    {
        $hpp = BarangClass::stokKeluar(collect($this->barang)->map(function ($q) {
            $brg = collect($this->dataBarang)->firstWhere('id', $q['id']);
            return [
                'barang_id' => $brg['barang_id'],
                'barang_satuan_id' => $q['id'],
                'qty' => $q['qty'],
                'harga' => $q['harga'],
                'diskon' => $q['diskon'],
                'penjualan' => 1,
                'kode_akun_id' => $brg['kode_akun_id'],
                'kode_akun_penjualan_id' => $brg['kode_akun_penjualan_id'],
                'kode_akun_modal_id' => $brg['kode_akun_modal_id'],
                'rasio_dari_terkecil' => $brg['rasio_dari_terkecil'],
            ];
        })->toArray(), $pembayaranId, $this->tanggal);
        return collect($hpp)->groupBy('kode_akun_id')->map(fn($q) => [
            'kode_akun_id' => $q->first()['kode_akun_id'],
            'debet' => $q->sum('debet'),
            'kredit' => $q->sum('kredit'),
        ])->toArray();
    }

    private function jurnalKeuangan($pembayaranId, $detail)
    {
        JurnalkeuanganClass::insert(
            jenis: 'Pendapatan',
            sub_jenis: 'Pendapatan ' . ($this->registrasi ? (collect($this->barang)->count() > 0 ? 'Pasien Tindakan/Resep Obat & Penjualan Barang' : 'Pasien Tindakan/Resep Obat') : 'Penjualan Barang Bebas'),
            tanggal: $this->tanggal,
            uraian: ('Pendapatan ' . ($this->registrasi ? (collect($this->barang)->count() > 0 ? 'Pasien Tindakan/Resep Obat & Penjualan Barang' : 'Pasien Tindakan/Resep Obat') : 'Penjualan Barang Bebas')) . ' No. Nota : ' . $pembayaranId . ' a/n ' . ($this->registrasi ? $this->registrasi->pasien->nama : Pasien::find($this->pasien_id)?->nama ?? '-') . ' Ket : ' . $this->keterangan,
            system: 1,
            foreign_key: 'pembayaran_id',
            foreign_id: $pembayaranId,
            detail: $detail
        );
    }

    public function mount()
    {
        $this->dataMetodeBayar = MetodeBayar::orderBy('nama')->get()->toArray();
        $this->dataBarang = collect(BarangClass::getBarang());
        $this->dataBarangApotek = collect($this->dataBarang)->where('persediaan', 'Apotek')->toArray();
        $this->dataNakes = Nakes::with('kepegawaianPegawai')->orderBy('nama')->get()->map(fn($q) => [
            'id' => $q->id,
            'dokter' => $q->dokter,
            'perawat' => $q->perawat,
            'nama' => $q->kepegawaianPegawai ? $q->kepegawaianPegawai->nama : $q->nama,
            'kode_akun_jasa_dokter_id' => $q->kode_akun_jasa_dokter_id,
            'kode_akun_jasa_perawat_id' => $q->kode_akun_jasa_perawat_id,
        ])->toArray();
        $this->tanggal = date('Y-m-d');
    }

    public function render()
    {
        return view('livewire.kasir.index');
    }
}
