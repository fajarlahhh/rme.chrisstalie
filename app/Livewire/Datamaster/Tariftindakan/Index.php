<?php

namespace App\Livewire\Datamaster\Tariftindakan;

use Livewire\Component;
use App\Models\TarifTindakan;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\KodeAkun;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DatamastertariftindakanExport;
use App\Exports\DatamasterExport;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $kode_akun_id, $dataKodeAkun = [];

    public function updated()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->dataKodeAkun = KodeAkun::detail()->where('parent_id', '42000')->get()->toArray();
    }

    public function delete($id)
    {
        try {
            TarifTindakan::findOrFail($id)
                ->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            session()->flash('danger', 'Gagal menghapus data');
        };
    }

    private function getData($paginate = true)
    {
        $query = TarifTindakan::with([
            'pengguna',
            'kodeAkun',
            'tarifTindakanAlatBarang.barangSatuan',
        ])
            ->when($this->kode_akun_id, function ($q) {
                $q->where('kode_akun_id', $this->kode_akun_id);
            })
            ->where(fn($q) => $q
                ->where('nama', 'like', '%' . $this->cari . '%'))
            ->orderBy('nama');

        return $paginate ? $query->paginate(10) : $query->get();
    }

    public function export()
    {
        return Excel::download(new DatamasterExport($this->getData(false), 'tariftindakan'), 'tarif_tindakan.xlsx');
    }

    public function render()
    {
        return view('livewire.datamaster.tariftindakan.index', [
            'data' => $this->getData(true),
        ]);
    }
}
