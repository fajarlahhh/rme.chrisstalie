<?php

namespace App\Livewire\Datamaster\Pasien;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Pasien;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DatamasterExport;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $exist = 1;

    public function updated()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        try {
            Pasien::findOrFail($id)
                ->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            session()->flash('danger', 'Gagal menghapus data');
        };
    }

    public function export()
    {
        return Excel::download(new DatamasterExport($this->getData(false), 'pasien'), 'pasien.xlsx');
    }

    public function getData($paginate = true)
    {
        $query = Pasien::with('pengguna', 'pembayaran')->where(fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%')->orWhere('id', 'like', '%' . $this->cari . '%'))
            ->orderBy('id');
        return $paginate ? $query->paginate(10) : $query->get();
    }

    public function render()
    {
        return view('livewire.datamaster.pasien.index', [
            'data' => $this->getData(true)
        ]);
    }
}
