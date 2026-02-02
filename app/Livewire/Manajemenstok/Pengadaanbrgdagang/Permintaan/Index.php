<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Permintaan;

use Livewire\Component;
use App\Models\PengadaanPermintaan;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $status = 'Belum Kirim Verifikasi', $bulan;

    public function updated()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
    }

    public function delete($id)
    {
        try {
            PengadaanPermintaan::findOrFail($id)
                ->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            session()->flash('danger', 'Gagal menghapus data');
        };
    }

    private function getData()
    {
        $data = PengadaanPermintaan::with([
            'pengadaanPermintaanDetail.barangSatuan.satuanKonversi',
            'pengadaanPermintaanDetail.barangSatuan.barang',
            'pengadaanPemesanan',
            'pengadaanPemesananDetail',
            'pengadaanVerifikasi.pengguna',
            'pengguna'
        ])
            ->where(fn($q) => $q
                ->where('deskripsi', 'like', '%' . $this->cari . '%'))
            ->when(auth()->user()->hasRole('operator|guest'), fn($q) => $q->whereIn('jenis_barang', ['Persediaan Apotek', 'Alat Dan Bahan']))
            ->when($this->status == 'Belum Kirim Verifikasi', fn($q) => $q->whereDoesntHave('pengadaanVerifikasi'))
            ->when($this->status == 'Pending', fn($q) => $q->whereHas('pengadaanVerifikasi', function ($q) {
                $q->whereNull('status');
            })->orWhereDoesntHave('pengadaanVerifikasi'))
            ->when($this->status == 'Ditolak', fn($q) => $q->whereHas('pengadaanVerifikasi', function ($q) {
                $q->whereNotNull('status');
                $q->where('status', 'Ditolak');
            }))
            ->when($this->status == 'Disetujui', fn($q) => $q->whereHas('pengadaanVerifikasi', function ($q) {
                $q->whereNotNull('status');
                $q->where('status', 'Disetujui');
            })->where('created_at', 'like', $this->bulan . '%'))
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return $data;
    }

    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.permintaan.index', [
            'data' => $this->getData()
        ]);
    }
}
