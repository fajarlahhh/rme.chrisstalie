<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Lainnya\Barangkhusus;

use Livewire\Component;
use App\Models\PemesananPengadaan;
use App\Models\StokMasuk;
use App\Models\Jurnal;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

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
            'data' => PemesananPengadaan::where('jenis', 'Barang Khusus')->with(['pemesananPengadaanDetail.barangSatuan.barang', 'pengguna.pegawai', 'supplier', 'stokKeluar', 'pelunasanPemesananPengadaan.kodeAkunPembayaran', 'kodeAkun'])->where('created_at', 'like', $this->bulan . '%')
                ->paginate(10)
        ]);
    }
}
