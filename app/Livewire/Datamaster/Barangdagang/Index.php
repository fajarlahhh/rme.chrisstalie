<?php

namespace App\Livewire\Datamaster\Barangdagang;

use App\Models\Barang;
use App\Models\KodeAkun;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DatamasterExport;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $persediaan, $kode_akun_id, $dataKodeAkun = [], $klinik;

    public function mount()
    {
        $this->dataKodeAkun = KodeAkun::detail()->where('parent_id', '11300')->get()->toArray();
    }

    public function delete($id)
    {
        try {
            Barang::findOrFail($id)
                ->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            session()->flash('danger', 'Gagal menghapus data');
        };
    }

    public function export()
    {
        return Excel::download(new DatamasterExport($this->getData(false), 'barangdagang'), 'barang_dagang.xlsx');
    }

    public function updated()
    {
        $this->resetPage();
    }

    public function getData($paginate = true)
    {
        $query = Barang::with(['barangSatuan' => fn($q) => $q->orderBy('rasio_dari_terkecil', 'desc')])->with([
            'pengguna',
            'kodeAkun'
        ])->persediaan()
            ->when($this->persediaan, fn($q) => $q->where('persediaan', $this->persediaan))
            ->when($this->klinik, fn($q) => $q->where('klinik', $this->klinik))
            ->when($this->kode_akun_id, function ($q) {
                $q->where('kode_akun_id', $this->kode_akun_id);
            })
            ->where(fn($q) => $q
                ->where('nama', 'like', '%' . $this->cari . '%'))
            ->orderBy('nama');
        return $paginate ? $query->paginate(10) : $query->get();
    }

    public function render()
    {
        return view('livewire.datamaster.barangdagang.index', [
            'data' => $this->getData(true),
        ]);
    }
}
