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

    public function export()
    {
        return Excel::download(new DatamasterExport($this->getData(false), 'pasien'), 'pasien.xlsx');
    }

    public function getData($paginate = true)
    {
        $query = Pasien::with('pengguna')->where(fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%')->orWhere('id', 'like', '%' . $this->cari . '%'))
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
