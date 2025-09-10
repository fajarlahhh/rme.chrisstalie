<?php

namespace App\Livewire\Datamaster\Barangdagang;

use App\Models\Barang;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $unit_bisnis;

    public function delete($id)
    {
        try {
            Barang::findOrFail($id)
                ->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            session()->flash('error', 'Gagal menghapus data');
        };
    }

    public function render()
    {
        return view('livewire.datamaster.barangdagang.index', [
            'data' => Barang::with(['barangSatuan' => fn($q) => $q->orderBy('rasio_dari_terkecil', 'desc')])->with([
                'pengguna',
                'kodeAkun'
            ])->persediaan()
                ->when($this->unit_bisnis, fn($q) => $q->where('unit_bisnis', $this->unit_bisnis))
                ->where(fn($q) => $q
                    ->where('nama', 'like', '%' . $this->cari . '%'))
                ->orderBy('nama')
                ->paginate(10)
        ]);
    }
}
