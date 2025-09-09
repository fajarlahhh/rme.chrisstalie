<?php

namespace App\Livewire\Penjualan;

use App\Models\Sale;
use App\Models\Stok;
use App\Models\Barang;
use Livewire\Component;
use App\Models\Penjualan;
use App\Models\SaleDetail;
use App\Models\StokKeluar;
use Illuminate\Support\Str;
use App\Models\PenjualanDetail;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    public $dataBarang = [];
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
            'barang' => 'required|array',
            'barang.*.id' => 'required',
            'barang.*.barang_satuan_id' => 'required',
            'barang.*.harga' => 'required|integer',
            'barang.*.qty' => 'required',
        ]);

        DB::transaction(function () {
            $dataTerakhir = Penjualan::where('created_at', 'like',  date('Y-m') . '%')->orderBy('id', 'desc')->first();

            if ($dataTerakhir) {
                $id = $dataTerakhir->id + 1;
            } else {
                $id = date('Ym') . '00001';
            }

            $data = new Penjualan();
            $data->id = $id;
            $data->keterangan = $this->keterangan;
            $data->metode_bayar = $this->metode_bayar;
            $data->total_harga_barang = $this->total_harga_barang;
            $data->diskon = $this->diskon;
            $data->total_tagihan = $this->total_harga_barang - $this->diskon;
            $data->bayar = $this->metode_bayar == 'Cash' ? $this->cash : ($this->total_harga_barang - $this->diskon);
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

            $cetak = view('livewire.penjualan.cetak', [
                'cetak' => true,
                'data' => Penjualan::findOrFail($data->id),
            ])->render();
            session()->flash('cetak', $cetak);
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('penjualan');
    }

    public function mount()
    {
        $this->dataBarang = Barang::with('barangSatuan.satuanKonversi')->orderBy('nama')->get()->map(fn($q) => [
            'id' => $q['id'],
            'nama' => $q['nama'],
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
