<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\VerifikasiPengadaan;

use Livewire\Component;
use App\Models\VerifikasiPengadaan;
use Illuminate\Support\Str;
use App\Models\BarangSatuan;
use Illuminate\Support\Facades\DB;
use App\Models\PermintaanPengadaan;
use App\Models\PermintaanPengadaanDetail;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $dataBarang = [], $dataPengguna = [], $barang = [], $deskripsi, $data, $verifikator_id, $status = 'Ditolak', $catatan;

    public function submit()
    {
        if ($this->status == 'Disetujui') {
            $this->validateWithCustomMessages([
                'status' => 'required',
                'deskripsi' => 'required',
                'barang' => 'required|array',
                'barang.*.qty_disetujui' => 'required|numeric|min:1',
            ]);
        }else{
            $this->validateWithCustomMessages([
                'status' => 'required',
                'catatan' => 'required',
            ]);
        }

        DB::transaction(function () {
            if ($this->status == 'Disetujui') {
                $this->data->permintaanPengadaanDetail()->delete();
                $this->data->permintaanPengadaanDetail()->insert(collect($this->barang)->map(fn($q) => [
                    'barang_id' => $q['barang_id'],
                    'qty_permintaan' => $q['qty'],
                    'qty_disetujui' => $q['qty_disetujui'],
                    'barang_satuan_id' => $q['id'],
                    'rasio_dari_terkecil' => $q['rasio_dari_terkecil'],
                    'permintaan_pengadaan_id' => $this->data->id,
                ])->toArray());
            }
            $verifikasiPengadaan = VerifikasiPengadaan::where('permintaan_pengadaan_id', $this->data->id)->where('jenis', 'Permintaan Pengadaan')->whereNull('status')->first();
            $verifikasiPengadaan->status = $this->status;
            $verifikasiPengadaan->catatan = $this->catatan;
            $verifikasiPengadaan->waktu_verifikasi = now();
            $verifikasiPengadaan->save();

            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/manajemenstok/pengadaanbrgdagang/verifikasi');
    }

    public function mount(PermintaanPengadaan $data)
    {
        
        $this->data = $data;
        $this->fill($this->data->toArray());
        $this->barang = $data->permintaanPengadaanDetail->map(fn($q) => [
            'id' => $q->barang_satuan_id,
            'barang_id' => $q->barang_id,
            'nama' => $q->barangSatuan->barang->nama,
            'satuan' => $q->barangSatuan->nama,
            'rasio_dari_terkecil' => $q->rasio_dari_terkecil,
            'qty' => $q->qty_permintaan,
            'qty_disetujui' => 0,
        ])->toArray();
    }

    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.verifikasi.form');
    }
}
