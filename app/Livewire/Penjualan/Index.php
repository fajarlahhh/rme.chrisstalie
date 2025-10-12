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
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    public $dataBarang = [], $dataMetodeBayar = [];
    public $barang = [];
    public $keterangan;
    public $metode_bayar = "Cash";
    public $cash = 0;
    public $total_harga_barang = 0;
    public $diskon = 0;

    public function tambahBarang()
    {
        array_push($this->barang, [
            'id' => null,
            'barang_satuan_id' => null,
            'kode_akun_id' => null,
            'barangSatuan' => [],
            'qty' => 0,
            'harga' => 0,
            'rasio_dari_terkecil' => 0,
        ]);
    }

    public function updatedBarang($value, $key)
    {
        $index = explode('.', $key);
        if ($value) {
            if ($index[1] == 'id') {
                $barang = collect($this->dataBarang)->where('id', $value)->first();
                $barangSatuan = collect($barang['barangSatuan']);
                $this->barang[$index[0]]['id'] = $barang['id'] ?? null;
                $this->barang[$index[0]]['barang_satuan_id'] = null;
                $this->barang[$index[0]]['kode_akun_id'] = $barang['kode_akun_id'];
                $this->barang[$index[0]]['kode_akun_penjualan_id'] = $barang['kode_akun_penjualan_id'];
                $this->barang[$index[0]]['barangSatuan'] = $barangSatuan->toArray();
                $this->barang[$index[0]]['qty'] = $this->barang[$index[0]]['qty'] ?? 0;
                $this->barang[$index[0]]['rasio_dari_terkecil'] = null;
                $this->barang[$index[0]]['harga'] = 0;
            }

            if ($index[1] == 'barang_satuan_id') {
                $barang = collect($this->dataBarang)->where('id', $this->barang[$index[0]]['id'])->first();
                $barangSatuan = collect($barang['barangSatuan']);
                $selectedSatuan = $barangSatuan->where('id', $this->barang[$index[0]]['barang_satuan_id'])->first();
                $this->barang[$index[0]]['barang_satuan_id'] = $this->barang[$index[0]]['barang_satuan_id'];
                $this->barang[$index[0]]['rasio_dari_terkecil'] = $selectedSatuan['rasio_dari_terkecil'];
                $this->barang[$index[0]]['harga'] = $selectedSatuan['harga_jual'] ?? 0;
            }
        } else {
            $this->barang[$index[0]]['id'] = null;
            $this->barang[$index[0]]['barang_satuan_id'] = null;
            $this->barang[$index[0]]['kode_akun_id'] = null;
            $this->barang[$index[0]]['kode_akun_penjualan_id'] = null;
            $this->barang[$index[0]]['barangSatuan'] = [];
            $this->barang[$index[0]]['qty'] = 0;
            $this->barang[$index[0]]['rasio_dari_terkecil'] = null;
            $this->barang[$index[0]]['harga'] = 0;
        }
        $harga = (int) ($this->barang[$index[0]]['harga'] ?? 0);
        $qty = (int) ($this->barang[$index[0]]['qty'] ?? 0);
        $this->barang[$index[0]]['sub_total'] = $harga * $qty;
        $this->total_harga_barang = collect($this->barang)->count() > 0 ? collect($this->barang)->sum(fn($q) => $q['sub_total'] ?? 0) : 0;
    }

    public function hapusBarang($key)
    {
        unset($this->barang[$key]);
        $this->barang = array_merge($this->barang);
        $this->total_harga_barang = collect($this->barang)->sum(fn($q) => $q['sub_total'] ?? 0);
    }

    public function submit()
    {
        $this->validate([
            'metode_bayar' => 'required',
            'cash' => $this->metode_bayar == 1 ? 'required|integer|min:' . ($this->total_harga_barang - $this->diskon) : 'nullable',
            'barang' => 'required|array',
            'barang.*.kode_akun_penjualan_id' => 'required',
            'barang.*.id' => 'required',
            'barang.*.barang_satuan_id' => 'required',
            'barang.*.harga' => 'required|integer',
            'barang.*.qty' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $barang = $this->barang[$index] ?? null;
                    if (!$barang) return;
                    // Cek stok tersedia
                    $stokTersedia = Stok::where('barang_id', $barang['id'])
                        ->available()
                        ->count();
                    if (($value / ($barang['rasio_dari_terkecil'] ?? 1)) > $stokTersedia) {
                        $fail('Stok barang tidak mencukupi. Stok tersedia: ' . $stokTersedia);
                    }
                }
            ],
        ]);

        DB::transaction(function () {
            $dataTerakhir = Penjualan::where('created_at', 'like',  date('Y-m') . '%')->orderBy('id', 'desc')->first();

            $metodeBayar = MetodeBayar::findOrFail($this->metode_bayar);

            if ($dataTerakhir) {
                $id = $dataTerakhir->id + 1;
            } else {
                $id = date('Ym') . '00001';
            }

            $data = new Penjualan();
            $data->id = $id;
            $data->keterangan = $this->keterangan;
            $data->metode_bayar = $metodeBayar->nama;
            $data->total_harga_barang = $this->total_harga_barang;
            $data->diskon = $this->diskon;
            $data->total_tagihan = $this->total_harga_barang - $this->diskon;
            $data->bayar = $metodeBayar->nama == 'Cash' ? $this->cash : ($this->total_harga_barang - $this->diskon);
            $data->pengguna_id = auth()->id();
            $data->save();
            PenjualanDetail::insert(collect($this->barang)->map(fn($q) => [
                'qty' => $q['qty'],
                'harga' => $q['harga'],
                'penjualan_id' => $data->id,
                'barang_id' => $q['id'],
                'barang_satuan_id' => $q['barang_satuan_id'],
                'rasio_dari_terkecil' => $q['rasio_dari_terkecil'],
            ])->toArray());
            foreach ($this->barang as $barang) {
                $stokKeluarId = Str::uuid();
                StokKeluar::insert([
                    'id' => $stokKeluarId,
                    'tanggal' => now(),
                    'qty' => $barang['qty'],
                    'penjualan_id' => $data->id,
                    'barang_id' => $barang['id'],
                    'pengguna_id' => auth()->id(),
                    'barang_satuan_id' => $barang['barang_satuan_id'],
                    'rasio_dari_terkecil' => $barang['rasio_dari_terkecil'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Stok::where('barang_id', $barang['id'])->available()->orderBy('tanggal_kedaluarsa', 'asc')->limit($barang['qty'])->update([
                    'tanggal_keluar' => now(),
                    'stok_keluar_id' => $stokKeluarId,
                ]);
            }

            $this->jurnalPendapatan($data, $metodeBayar);

            $cetak = view('livewire.penjualan.cetak', [
                'cetak' => true,
                'data' => Penjualan::findOrFail($data->id),
            ])->render();
            session()->flash('cetak', $cetak);
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('penjualan');
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

    public function mount()
    {
        $this->dataMetodeBayar = MetodeBayar::get()->toArray();
        $this->dataBarang = Barang::with(['barangSatuan.satuanKonversi', 'kodeAkun'])->where('perlu_resep', 0)->apotek()
            ->orderBy('nama')->get()->map(fn($q) => [
                'id' => $q['id'],
                'nama' => $q['nama'],
                'kode_akun_id' => $q['kode_akun_id'],
                'kode_akun_penjualan_id' => $q['kode_akun_penjualan_id'],
                'kategori' => $q->kodeAkun->nama,
                'barangSatuan' => $q['barangSatuan']->map(fn($r) => [
                    'id' => $r['id'],
                    'nama' => $r['nama'],
                    'rasio_dari_terkecil' => $r['rasio_dari_terkecil'],
                    'konversi_satuan' => $r['konversi_satuan'],
                    'harga_jual' => $r['harga_jual'],
                    'satuan_konversi' => $r['satuanKonversi'] ? [
                        'id' => $r['satuanKonversi']['id'],
                        'nama' => $r['satuanKonversi']['nama'],
                        'rasio_dari_terkecil' => $r['satuanKonversi']['rasio_dari_terkecil'],
                    ] : null,
                ]),
            ])->toArray();
    }

    public function render()
    {
        return view('livewire.penjualan.index');
    }
}
