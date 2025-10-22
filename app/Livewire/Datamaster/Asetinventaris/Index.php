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
        $this->dataKodeAkun = KodeAkun::detail()->where('id', 'like', '151%')->get()->toArray();
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
        $data = Aset::findOrFail($id);
        if ($data->asetPenyusutanGarisLurusTerjurnal->count() == 0 && $data->asetPenyusutanUnitProduksiTerjurnal->count() == 0) {
            $data->jurnal()->delete();
            $data->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        }
    }

    public function render()
    {
        return view('livewire.datamaster.asetinventaris.index', [
            'data' => Aset::with([
                'pengguna', 
                'kodeAkun',
                'kodeAkunSumberDana'
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
