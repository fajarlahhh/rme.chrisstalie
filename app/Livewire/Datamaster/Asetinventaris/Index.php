<?php

namespace App\Livewire\Datamaster\Asetinventaris;

use App\Models\Aset;
use Livewire\Component;
use App\Models\KodeAkun;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $kode_akun_id, $dataKodeAkun = [];

    public function mount()
    {
        $this->dataKodeAkun = KodeAkun::detail()->where('id', 'like', '151%')->where('kategori', 'Aktiva')->get()->toArray();
    }

    public function delete($id)
    {
        $data = Aset::findOrFail($id);
        if ($data->asetPenyusutanTerjurnal()->count() == 0) {
            $data->jurnal()->delete();
            $data->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        }
    }

    public function render()
    {
        return view('livewire.datamaster.asetinventaris.index', [
            'data' => Aset::with([
                'pengguna'
            ])
                ->when($this->kode_akun_id, function ($q) {
                    $q->where('kode_akun_id', $this->kode_akun_id);
                })
                ->where(fn($q) => $q
                    ->where('nama', 'like', '%' . $this->cari . '%'))
                ->orderBy('nama')
                ->paginate(10),
            'dataRaw' => Aset::when($this->kode_akun_id, function ($q) {
                $q->where('kode_akun_id', $this->kode_akun_id);
            })
                ->where(fn($q) => $q
                    ->where('nama', 'like', '%' . $this->cari . '%'))
                ->get()
        ]);
    }
}
