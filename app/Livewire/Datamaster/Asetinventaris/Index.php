<?php

namespace App\Livewire\Datamaster\Asetinventaris;

use App\Models\Aset;
use Livewire\Component;
use App\Models\KodeAkun;
use App\Class\JurnalClass;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\AsetPenyusutan;
use Maatwebsite\Excel\Facades\Excel;
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
        $this->dataKodeAkun = KodeAkun::detail()->where('id', 'like', '151%')->get()->toArray();
    }

    public function export()
    {
        return Excel::download(new DatamasterExport($this->getData(false), 'asetinventaris'), 'aset_inventaris.xlsx');
    }

    public function print($id)
    {
        $data = Aset::findOrFail($id);
        $cetak = view('livewire.datamaster.asetinventaris.qr', [
            'cetak' => true,
            'data' => $data,
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function delete($id)
    {
        Aset::findOrFail($id)->forceDelete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function getData($paginate = true)
    {
        $query = Aset::with([
            'pengguna',
            'kodeAkun',
            'kodeAkunSumberDana'
        ])
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
        return view('livewire.datamaster.asetinventaris.index', [
            'data' => $this->getData(true),
            'dataRaw' => Aset::when($this->kode_akun_id, function ($q) {
                $q->where('kode_akun_id', $this->kode_akun_id);
            })
                ->where(fn($q) => $q
                    ->where('nama', 'like', '%' . $this->cari . '%'))
                ->get()
        ]);
    }
}
