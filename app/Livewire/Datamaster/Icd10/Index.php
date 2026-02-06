<?php

namespace App\Livewire\Datamaster\Icd10;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Icd10;
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
        return Excel::download(new DatamasterExport($this->getData(false), 'icd10'), 'icd10.xlsx');
    }

    public function getData($paginate = true)
    {
        $query = Icd10::where(fn($q) => $q
            ->where('id', 'like', '%' . $this->cari . '%')
            ->orWhere('uraian', 'like', '%' . $this->cari . '%'))
            ->with('pengguna')
            ->orderBy('id');
        return $paginate ? $query->paginate(10) : $query->get();
    }
    public function delete($id)
    {
        try {
            Icd10::findOrFail($id)
                ->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            session()->flash('danger', 'Gagal menghapus data');
        };
    }

    public function render()
    {
        return view('livewire.datamaster.icd10.index', [
            'data' => $this->getData(true)
        ]);
    }
}
