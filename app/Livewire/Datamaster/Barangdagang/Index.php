<?php

namespace App\Livewire\Datamaster\Barangdagang;

use App\Models\Barang;
use App\Models\KodeAkun;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $unit_bisnis, $kode_akun_id, $dataKodeAkun = [], $klinik;

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
            session()->flash('error', 'Gagal menghapus data');
        };
    }


    public function updated()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.datamaster.barangdagang.index', [
            'data' => Barang::with(['barangSatuan' => fn($q) => $q->orderBy('rasio_dari_terkecil', 'desc')])->with([
                'pengguna',
                'kodeAkun'
            ])->persediaan()
                ->when($this->unit_bisnis, fn($q) => $q->where('unit_bisnis', $this->unit_bisnis))
                ->when($this->klinik, fn($q) => $q->where('klinik', $this->klinik))
                ->when($this->kode_akun_id, function ($q) {
                    $q->where('kode_akun_id', $this->kode_akun_id);
                })
                ->where(fn($q) => $q
                    ->where('nama', 'like', '%' . $this->cari . '%'))
                ->orderBy('nama')
                ->paginate(10)
        ]);
    }
}
