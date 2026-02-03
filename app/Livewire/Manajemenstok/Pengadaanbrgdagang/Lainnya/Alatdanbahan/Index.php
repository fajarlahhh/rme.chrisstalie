<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Lainnya\Alatdanbahan;

use Livewire\Component;
use App\Models\StokMasuk;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\KeuanganJurnal;
use Illuminate\Support\Facades\DB;
use App\Models\PengadaanPemesanan;

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
        return view('livewire.manajemenstok.pengadaanbrgdagang.lainnya.alatdanbahan.index', [
            'data' => PengadaanPemesanan::where('jenis', 'Alat dan Bahan')->where(fn($q) => $q->where('uraian', 'like', '%' . $this->cari . '%'))->with(['pengadaanPemesananDetail.barangSatuan.barang', 'pengguna.kepegawaianPegawai', 'supplier', 'stokKeluar', 'pengadaanPelunasanPemesanan.kodeAkunPembayaran', 'kodeAkun'])->where('tanggal', 'like', $this->bulan . '%')
                ->paginate(10)
        ]);
    }
}