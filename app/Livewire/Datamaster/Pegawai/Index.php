<?php

namespace App\Livewire\Datamaster\Pegawai;

use Livewire\Component;
use App\Models\Pegawai;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $status = 'Aktif', $unit_bisnis;

    public function delete($id)
    {
        Pegawai::findOrFail($id)
            ->forceDelete();
    }

    public function restore($id)
    {
        Pegawai::withTrashed()
            ->findOrFail($id)
            ->restore();
    }

    public function render()
    {
        return view('livewire.datamaster.pegawai.index', [
            'data' => Pegawai::where(fn($q) => $q
                ->where('nama', 'like', '%' . $this->cari . '%')
                ->where('status', $this->status))
                ->when($this->unit_bisnis, fn($q) => $q->where('unit_bisnis', $this->unit_bisnis))
                ->with('pengguna')
                ->orderBy('nama')
                ->paginate(10)
        ]);
    }
}
