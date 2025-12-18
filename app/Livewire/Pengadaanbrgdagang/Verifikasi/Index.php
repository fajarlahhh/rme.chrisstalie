<?php

namespace App\Livewire\Pengadaanbrgdagang\Verifikasi;

use Livewire\Component;
use App\Models\Verifikasi;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\PermintaanPembelian;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $status = 'Pending';

    public function updated()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.pengadaanbrgdagang.verifikasi.index', [
            'data' => PermintaanPembelian::with([
                'pengguna',
                'verifikasi.pengguna.pegawai',
                'permintaanPembelianDetail.barangSatuan.satuanKonversi',
                'permintaanPembelianDetail.barangSatuan.barang',
            ])->with(['verifikasi' => fn($q) => $q->whereNotNull('status')])
                ->when($this->status == 'Pending', fn($q) => $q->whereHas('verifikasi', function ($q) {
                    $q->whereNull('status');
                }))
                ->when($this->status == 'Terverifikasi', fn($q) => $q->whereHas('verifikasi', function ($q) {
                    $q->whereNotNull('status');
                }))
                ->where(fn($q) => $q
                    ->where('deskripsi', 'like', '%' . $this->cari . '%'))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}
