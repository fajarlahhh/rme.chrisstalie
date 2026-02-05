<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Stokmasuk;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\StokMasuk;
use App\Models\PengadaanPemesanan;
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
        StokMasuk::findOrFail($id)->forceDelete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.stokmasuk.index', [
            'data' => StokMasuk::with(['pengguna.kepegawaianPegawai', 'barangSatuan.barang', 'pengadaanPemesanan.supplier', 'keluar'])
                ->where('tanggal', 'like', $this->bulan . '%')
                ->whereNotNull('pengadaan_pemesanan_id')
                ->when(auth()->user()->hasRole('operator|guest'), fn($q) => $q->whereHas('pengadaanPemesanan', fn($q) => $q->whereIn('jenis', ['Persediaan Apotek', 'Alat Dan Bahan'])))
                ->where(
                    fn($q) => $q
                        ->whereHas('barangSatuan.barang', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
                        ->orWhereHas('pengadaanPemesanan', fn($q) => $q->where('uraian', 'like', '%' . $this->cari . '%'))
                )
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}
