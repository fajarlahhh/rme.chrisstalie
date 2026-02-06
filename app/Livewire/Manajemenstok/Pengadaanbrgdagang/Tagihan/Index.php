<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Tagihan;

use Livewire\Component;
use App\Models\PengadaanTagihan;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Index extends Component
{
    #[Url]
    public $bulan;

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
        $data = PengadaanTagihan::find($id);
        $data->delete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.tagihan.index', [
            'data' => PengadaanTagihan::with(['pengadaanPemesanan.pengadaanPemesananDetail.barangSatuan.barang', 'supplier'])->where('tanggal', 'like', $this->bulan . '%')->orderBy('created_at', 'desc')->paginate(10)
        ]);
    }
}
