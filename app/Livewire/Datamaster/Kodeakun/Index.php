<?php

namespace App\Livewire\Datamaster\Kodeakun;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\KodeAkun;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DatamasterExport;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari;

    public function updated()
    {
        $this->resetPage();
    }

    public function export()
    {
        return Excel::download(new DatamasterExport($this->getData(false), 'kodeakun'), 'kode_akun.xlsx');
    }

    public function getData($paginate = true)
    {
        $query = KodeAkun::where(fn($q) => $q
            ->where('id', 'like', '%' . $this->cari . '%')
            ->orWhere('nama', 'like', '%' . $this->cari . '%'))
            ->orderBy('id');
        return $paginate ? $query->paginate(10) : $query->get();
    }
    public function delete($id)
    {
        try {
            KodeAkun::findOrFail($id)
                ->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            session()->flash('danger', 'Gagal menghapus data');
        };
    }

    public function render()
    {
        return view('livewire.datamaster.kodeakun.index', [
            'data' => $this->getData(true)
        ]);
    }
}
