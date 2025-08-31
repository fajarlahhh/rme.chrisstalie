<?php

namespace App\Livewire\Pengadaan\Pembelian;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Pembelian;

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
        $data = Pembelian::findOrFail($id);
        if ($data->stokMasuk->count() == 0) {
            $data->delete();
        }
    }

    public function render()
    {
        return view('livewire.pengadaan.pembelian.index', [
            'data' => Pembelian::with(['pembelianDetail.barang', 'pengguna', 'stokMasuk'])
                ->where('tanggal', 'like', $this->bulan . '%')
                ->where(fn($q) => $q->where('uraian', 'like', '%' . $this->cari . '%'))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}
