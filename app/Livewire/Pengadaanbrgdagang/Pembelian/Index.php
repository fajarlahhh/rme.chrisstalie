<?php

namespace App\Livewire\Pengadaanbrgdagang\Pembelian;

use Livewire\Component;
use App\Models\Pembelian;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\PermintaanPembelian;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $bulan, $status = 1;


    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
    }

    public function delete($id)
    {
        $data = Pembelian::findOrFail($id);
        if ($data->stokMasuk->count() == 0) {
            $data->jurnal->delete();
            $data->delete();
            session()->flash('success', 'Berhasil menghapus data');
        }
    }

    public function render()
    {
        return view('livewire.pengadaanbrgdagang.pembelian.index', [
            'data' => $this->status == 1 ? PermintaanPembelian::with([
                'pengguna',
                'permintaanPembelianDetail.barangSatuan.barang',
            ])->with(['verifikasi' => fn($q) => $q->whereNotNull('status')])
                ->when($this->status == 'Pending', fn($q) => $q->whereHas('verifikasi', function ($q) {
                    $q->whereNull('status');
                }))
                ->whereHas('verifikasi', function ($q) {
                    $q->whereNotNull('status');
                })
                ->whereDoesntHave('pembelian')
                ->where(fn($q) => $q
                    ->where('deskripsi', 'like', '%' . $this->cari . '%'))
                ->orderBy('created_at', 'desc')
                ->paginate(10) :
                Pembelian::with(['pembelianDetail.barangSatuan.barang', 'pengguna', 'stokMasuk'])
                ->where('jenis', 'Barang Dagang')
                ->where('tanggal', 'like', $this->bulan . '%')
                ->where(fn($q) => $q->where('uraian', 'like', '%' . $this->cari . '%'))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}
