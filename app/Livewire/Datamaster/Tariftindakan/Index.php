<?php

namespace App\Livewire\Datamaster\Tariftindakan;

use Livewire\Component;
use App\Models\TarifTindakan;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $unit_bisnis;

    public function delete($id)
    {
        try {
            TarifTindakan::findOrFail($id)
                ->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            session()->flash('error', 'Gagal menghapus data');
        };
    }

    public function render()
    {
        return view('livewire.datamaster.tariftindakan.index', [
            'data' => TarifTindakan::with([
                'pengguna',
                'tarifTindakanAlatBahan',
            ])
                ->when($this->unit_bisnis, fn($q) => $q->where('unit_bisnis', $this->unit_bisnis))
                ->where(fn($q) => $q
                    ->where('nama', 'like', '%' . $this->cari . '%'))
                ->orderBy('nama')
                ->paginate(10)
        ]);
    }
}
