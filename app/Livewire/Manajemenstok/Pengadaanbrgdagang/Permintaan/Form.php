<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Permintaan;

use Livewire\Component;
use App\Class\BarangClass;
use App\Models\PengadaanVerifikasi;
use Illuminate\Support\Facades\DB;
use App\Models\PengadaanPermintaan;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $dataBarang = [], $dataPengguna = [], $barang = [];
    public $deskripsi, $data, $verifikator_id, $jenis_barang = 'Persediaan Apotek', $kirim = 0;

    public function getBarang($jenis)
    {
        if ($jenis == 'Persediaan Apotek') {
            return BarangClass::getBarangBySatuanUtama('Apotek');
        } else if ($jenis == 'Alat Dan Bahan') {
            return BarangClass::getBarangBySatuanUtama('Klinik', 0);
        } else if ($jenis == 'Barang Khusus') {
            return BarangClass::getBarangBySatuanUtama('Apotek', 1);
        }
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'deskripsi' => 'required',
            'barang' => 'required|array',
            'barang.*.id' => 'required',
            'barang.*.qty' => 'required',
        ]);

        DB::transaction(function () {
            if (!$this->data->exists) {
                $terakhir = PengadaanPermintaan::where('created_at', 'like', date('Y-m') . '%')
                    ->whereNotNull('nomor')
                    ->orderBy('id', 'desc')
                    ->first();
                $nomorTerakhir = $terakhir ? (int)substr($terakhir->id, 6, 5) : 0;
                $nomor = sprintf('%05d', $nomorTerakhir + 1) . '/PERMINTAAN-CHRISSTALIE/' . date('m', strtotime($this->data->created_at)) . '/' . date('Y', strtotime($this->data->created_at));
                $this->data->nomor = $nomor;
            }
            $this->data->jenis_barang = $this->jenis_barang;
            $this->data->deskripsi = $this->deskripsi;
            $this->data->kirim = $this->kirim;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();

            $this->data->pengadaanPermintaanDetail()->delete();
            $this->data->pengadaanPermintaanDetail()->insert(collect($this->barang)->map(function ($q) {
                $brg = collect($this->dataBarang)->firstWhere('id', $q['id']);
                return [
                    'qty_permintaan' => $q['qty'],
                    'pengadaan_permintaan_id' => $this->data->id,
                    'barang_satuan_id' => $q['id'],
                    'rasio_dari_terkecil' => $brg['rasio_dari_terkecil'],
                    'barang_id' => $brg['barang_id'],
                ];
            })->toArray());

            if ($this->kirim) {
                $pengadaanVerifikasi = new PengadaanVerifikasi();
                $pengadaanVerifikasi->pengadaan_permintaan_id = $this->data->id;
                $pengadaanVerifikasi->jenis = 'Permintaan Pengadaan';
                $pengadaanVerifikasi->save();
            }
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/manajemenstok/pengadaanbrgdagang/permintaan');
    }

    public function mount(PengadaanPermintaan $data)
    {
        $this->data = $data;
        $this->fill($this->data->toArray());
        if ($this->data->exists) {
            $this->barang = $this->data->pengadaanPermintaanDetail->map(fn($q) => [
                'id' => $q->barang_satuan_id,
                'barang_id' => $q->barang_id,
                'qty' => $q->qty_permintaan,
            ])->toArray();
        }
        $this->dataBarang = $this->getBarang($this->jenis_barang);
    }

    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.permintaan.form');
    }
}
