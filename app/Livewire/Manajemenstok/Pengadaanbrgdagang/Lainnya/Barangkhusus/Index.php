<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Lainnya\Barangkhusus;

use Livewire\Component;
use App\Models\PengadaanPemesanan;
use App\Models\StokMasuk;
use App\Models\KeuanganJurnal;
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
        PengadaanPemesanan::find($id)->delete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.lainnya.barangkhusus.index', [
            'data' => PengadaanPemesanan::where('jenis', 'Barang Khusus')->where(fn($q) => $q->where('uraian', 'like', '%' . $this->cari . '%'))->with(['pengadaanPemesananDetail.barangSatuan.barang', 'pengguna.kepegawaianPegawai', 'supplier', 'stokKeluar', 'pengadaanPelunasanPemesanan.kodeAkunPembayaran', 'kodeAkun'])->where('tanggal', 'like', $this->bulan . '%')
                ->paginate(10)
        ]);
    }
}