<?php

namespace App\Livewire\Datamaster\Barang;

use App\Models\Barang;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $jenis = 'Obat';

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
        return view('livewire.datamaster.barang.index', [
            'data' => Barang::with([
                'pengguna'
            ])->where('jenis', $this->jenis)->persediaan()
                ->where(fn($q) => $q
                    ->where('nama', 'like', '%' . $this->cari . '%'))
                ->orderBy('nama')
                ->paginate(10)
        ]);
    }
}
