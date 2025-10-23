<?php

namespace App\Livewire\Pengadaanbrglainnya\Alatdanbahan;

use Livewire\Component;
use App\Models\StokMasuk;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $bulan;

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
    }

    public function delete($id)
    {
        $data = StokMasuk::find($id);
        if ($data->keluar->count() == 0) {
            $data->jurnal->delete();
            $data->delete();
            session()->flash('success', 'Berhasil menghapus data');
        }
    }

    public function render()
    {
        return view('livewire.pengadaanbrglainnya.alatdanbahan.index', [
            'data' => StokMasuk::with(['pengguna', 'barangSatuan.barang', 'pembelian'])
                ->where('created_at', 'like', $this->bulan . '%')
                ->whereHas('barangSatuan.barang', fn($q) => $q->where('khusus', 0)->where('persediaan', 'Klinik')->where('nama', 'like', '%' . $this->cari . '%'))
                ->whereHas('pembelian', fn($q) => $q->where('uraian', 'like', '%' . $this->cari . '%'))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}
