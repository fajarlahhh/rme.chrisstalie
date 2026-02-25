<?php

namespace App\Livewire\Member\Data;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Pasien;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DatamasterExport;
use App\Models\Member;

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
            Member::findOrFail($id)
                ->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            session()->flash('danger', 'Gagal menghapus data');
        };
    }

    public function export()
    {
        return Excel::download(new DatamasterExport($this->getData(false), 'member'), 'member.xlsx');
    }

    public function getData($paginate = true)
    {
        $query = Member::with('pasien', 'pengguna', 'memberPembayaran')
            ->whereHas('pasien', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%')->orWhere('id', 'like', '%' . $this->cari . '%'))
            ->orderBy('id');
        return $paginate ? $query->paginate(10) : $query->get();
    }

    public function render()
    {
        return view('livewire.member.data.index', [
            'data' => $this->getData(true)
        ]);
    }
}
