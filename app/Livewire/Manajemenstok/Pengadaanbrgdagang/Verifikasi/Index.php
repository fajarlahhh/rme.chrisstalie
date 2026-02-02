<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Verifikasi;

use Livewire\Component;
use App\Models\PengadaanVerifikasi;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\PengadaanPermintaan;

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
        return view('livewire.manajemenstok.pengadaanbrgdagang.verifikasi.index', [
            'data' => PengadaanPermintaan::with([
                'pengguna.kepegawaianPegawai',
                'pengadaanVerifikasi.pengguna.kepegawaianPegawai',
                'pengadaanPemesanan',
                'pengadaanPermintaanDetail.barangSatuan.satuanKonversi',
                'pengadaanPermintaanDetail.barangSatuan.barang',
                'pengadaanVerifikasiDisetujui',
                'pengadaanVerifikasiDitolak',
                'pengadaanVerifikasiPending',
                'pengadaanVerifikasi'
            ])->with(['pengadaanVerifikasi' => fn($q) => $q->whereNotNull('status')])
                ->when($this->status == 'Pending', fn($q) => $q->whereHas('pengadaanVerifikasi', function ($q) {
                    $q->whereNull('status')->where('jenis', 'Permintaan Pengadaan');
                }))
                ->when($this->status == 'Terverifikasi', fn($q) => $q->whereHas('pengadaanVerifikasi', function ($q) {
                    $q->whereNotNull('status')->where('jenis', 'Permintaan Pengadaan');
                }))
                ->where(fn($q) => $q
                    ->where('deskripsi', 'like', '%' . $this->cari . '%'))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}
