<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Persetujuanpemesanan;

use Livewire\Component;
use App\Models\PengadaanPemesanan;
use Illuminate\Support\Facades\DB;
use App\Models\PengadaanVerifikasi;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $dataBarang = [], $dataPengguna = [], $barang = [], $data;

    public function submit()
    {
        $this->validateWithCustomMessages([
            'data' => 'required',
        ]);

        DB::transaction(function () {
            $terakhir = PengadaanPemesanan::where('tanggal', 'like', substr($this->data->tanggal, 0, 7) . '%')
                ->whereNotNull('nomor')
                ->orderBy('id', 'desc')
                ->first();
            $nomorTerakhir = $terakhir ? (int)substr($terakhir->nomor, 0, 5) : 0;
            $nomor = sprintf('%05d', $nomorTerakhir + 1) . '/SP-CHRISSTALIE/' . substr($this->data->tanggal, 5, 2) . '/' . substr($this->data->tanggal, 0, 4);
            $this->data->nomor = $nomor;
            $this->data->save();

            $pengadaanVerifikasi = PengadaanVerifikasi::where('pengadaan_pemesanan_id', $this->data->id)->where('jenis', 'Persetujuan Pemesanan Pengadaan')->whereNull('status')->first();
            $pengadaanVerifikasi->status = 'Disetujui';
            $pengadaanVerifikasi->waktu_verifikasi = now();
            $pengadaanVerifikasi->pengguna_id = auth()->id();
            $pengadaanVerifikasi->save();

            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/manajemenstok/pengadaanbrgdagang/persetujuanpemesanan');
    }

    public function mount(PengadaanPemesanan $data)
    {
        $this->data = $data;
        $this->fill($this->data->toArray());
        $this->barang = $data->pengadaanPemesananDetail->map(fn($q) => [
            'id' => $q->barang_satuan_id,
            'barang_id' => $q->barang_id,
            'nama' => $q->barangSatuan->barang->nama,
            'satuan' => $q->barangSatuan->nama,
            'rasio_dari_terkecil' => $q->rasio_dari_terkecil,
            'qty' => $q->qty,
            'harga_beli' => $q->harga_beli,
        ])->toArray();
    }
    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.persetujuanpemesanan.form');
    }
}
