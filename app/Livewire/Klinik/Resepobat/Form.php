<?php

namespace App\Livewire\Klinik\Resepobat;

use App\Models\Stok;
use App\Models\Barang;
use Livewire\Component;
use App\Models\Penjualan;
use App\Models\ResepObat;
use App\Models\Registrasi;
use App\Models\StokKeluar;
use App\Models\JurnalClass;
use App\Models\MetodeBayar;
use Illuminate\Support\Str;
use App\Models\PenjualanDetail;
use Illuminate\Support\Facades\DB;

class Form extends Component
{
    public $dataBarang = [], $dataMetodeBayar = [];
    public $barang = [];
    public $keterangan;
    public $metode_bayar = "Cash";
    public $cash = 0;
    public $total_harga_barang = 0;
    public $diskon = 0;
    public $data;
    public $resep = [];

    public function tambahBarang($index)
    {
        array_push($this->resep[$index]['barang'], [
            'id' => null,
            'barang_satuan_id' => null,
            'kode_akun_id' => null,
            'barangSatuan' => [],
            'qty' => 0,
        ]);
    }

    public function updatedResep($value, $key)
    {
        $index = explode('.', $key);
        if (sizeof($index) == 4) {
            if ($value) {
                if ($index[3] == 'id') {
                    $barang = collect($this->dataBarang)->where('id', $value)->first();
                    $barangSatuan = collect($barang['barangSatuan']);
                    $this->resep[$index[0]]['barang'][$index[2]]['id'] =  $barang['id'] ?? null;
                    $this->resep[$index[0]]['barang'][$index[2]]['barang_satuan_id'] = null;
                    $this->resep[$index[0]]['barang'][$index[2]]['kode_akun_id'] = $barang['kode_akun_id'];
                    $this->resep[$index[0]]['barang'][$index[2]]['kode_akun_penjualan_id'] = $barang['kode_akun_penjualan_id'];
                    $this->resep[$index[0]]['barang'][$index[2]]['barangSatuan'] = $barangSatuan->toArray();
                    $this->resep[$index[0]]['barang'][$index[2]]['qty'] = $this->resep[$index[0]]['barang'][$index[2]]['qty'] ?? 0;
                }
            } else {
                $this->resep[$index[0]]['barang'][$index[2]]['id'] = null;
                $this->resep[$index[0]]['barang'][$index[2]]['barang_satuan_id'] = null;
                $this->resep[$index[0]]['barang'][$index[2]]['kode_akun_id'] = null;
                $this->resep[$index[0]]['barang'][$index[2]]['kode_akun_penjualan_id'] = null;
                $this->resep[$index[0]]['barang'][$index[2]]['barangSatuan'] = [];
                $this->resep[$index[0]]['barang'][$index[2]]['qty'] = 0;
            }
        }
    }

    public function hapusBarang($index, $key)
    {
        unset($this->barang[$index]['barang'][$key]);
        $this->barang = array_merge($this->barang[$index]['barang']);
        $this->total_harga_barang = collect($this->barang[$index]['barang'])->sum(fn($q) => $q['sub_total'] ?? 0);
    }

    public function submit()
    {
        $this->validate([
            'resep' => 'required',
            'resep.*.barang' => 'required|array',
            'resep.*.barang.*.id' => 'required',
            'resep.*.barang.*.barang_satuan_id' => 'required',
            'resep.*.barang.*.qty' => 'required|integer|min:1',
        ]);

        DB::transaction(function () {
            $resepObat = [];
            ResepObat::where('id', $this->data->id)->delete();
            foreach ($this->resep as $x => $row) {
                foreach ($row['barang'] as $barang) {
                    $resepObat[] = [
                        'id' => $this->data->id,
                        'barang_id' => $barang['id'],
                        'barang_satuan_id' => $barang['barang_satuan_id'],
                        'resep' => $x + 1,
                        'qty' => $barang['qty'],
                        'catatan' => $row['catatan'],
                        'pengguna_id' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            ResepObat::insert($resepObat);
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/klinik/resepobat');
    }

    public function mount(Registrasi $data)
    {
        $this->data = $data;
        $this->dataBarang = Barang::with(['barangSatuan.satuanKonversi', 'kodeAkun'])->apotek()->orderBy('nama')->get()->map(fn($q) => [
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
        if (!$data->resepobat) {
            $this->resep[] = [
                'barang' => [],
                'catatan' => '',
            ];
        } else {
            $this->resep = $data->resepobat->groupBy('resep')->map(fn($q) => [
                'catatan' => $q->first()->catatan,
                'barang' => $q->map(fn($r) => [
                    'id' => $r->barang_id,
                    'barang_satuan_id' => $r->barang_satuan_id,
                    'kode_akun_id' => $r->barang->kode_akun_id,
                    'barangSatuan' => $r->barang->barangSatuan->map(fn($s) => [
                        'id' => $s->id,
                        'nama' => $s->nama,
                        'konversi_satuan' => $s->konversi_satuan,
                        'harga_jual' => $s->harga_jual,
                        'rasio_dari_terkecil' => $s->rasio_dari_terkecil,
                    ]),
                    'qty' => $r->qty,
                ])->toArray(),
            ])->toArray();
        }
    }

    public function tambahResep()
    {
        $this->resep[] = [
            'barang' => [],
            'catatan' => '',
        ];
    }

    public function hapusResep($index)
    {
        unset($this->resep[$index]);
        $this->resep = array_merge($this->resep);
    }

    public function render()
    {
        return view('livewire.klinik.resepobat.form');
    }
}
