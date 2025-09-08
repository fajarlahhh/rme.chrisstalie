<?php

namespace App\Livewire\Datamaster\Barang;

use App\Models\Barang;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $jenis = 'Obat', $kantor;

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
            'data' => Barang::with(['barangSatuan' => fn($q) => $q->orderBy('rasio_dari_terkecil', 'desc')])->with([
                'pengguna',
            ])->where('jenis', $this->jenis)->persediaan()
                ->when($this->kantor, fn($q) => $q->where('kantor', $this->kantor))
                ->where(fn($q) => $q
                    ->where('nama', 'like', '%' . $this->cari . '%'))
                ->orderBy('nama')
                ->paginate(10)
        ]);
    }
}
