<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Lainnya\Alatdanbahan;

use Livewire\Component;
use App\Models\StokMasuk;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\JurnalKeuangan;
use Illuminate\Support\Facades\DB;
use App\Models\PemesananPengadaan;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $bulan;

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
    }

    public function updated()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        PemesananPengadaan::find($id)->delete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.lainnya.barangkhusus.index', [
            'data' => PemesananPengadaan::where('jenis', 'Alat dan Bahan')->with(['pemesananPengadaanDetail.barangSatuan.barang', 'pengguna.pegawai', 'supplier', 'stokKeluar', 'pelunasanPemesananPengadaan.kodeAkunPembayaran', 'kodeAkun'])->where('tanggal', 'like', $this->bulan . '%')
                ->paginate(10)
        ]);
    }
}
