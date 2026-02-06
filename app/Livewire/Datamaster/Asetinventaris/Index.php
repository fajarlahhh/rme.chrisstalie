<?php

namespace App\Livewire\Datamaster\Asetinventaris;

use App\Models\Aset;
use Livewire\Component;
use App\Models\KodeAkun;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Exports\DatamasterExport;
use App\Class\JurnalkeuanganClass;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $kode_akun_id, $dataKodeAkun = [], $bulanPerolehan;

    public function updated()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->dataKodeAkun = KodeAkun::detail()->where('id', 'like', '151%')->get()->toArray();
        $this->bulanPerolehan = $this->bulanPerolehan ?: null;
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
        $data = Aset::findOrFail($id);
        if (JurnalkeuanganClass::tutupBuku(substr($data->tanggal_perolehan, 0, 7) . '-01')) {
            session()->flash('error', 'Pembukuan periode ini sudah ditutup');
            return;
        }
        $data->forceDelete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function getData($paginate = true)
    {
        $query = Aset::with([
            'pengguna.kepegawaianPegawai',
            'kodeAkun',
            'kodeAkunSumberDana',
            'keuanganJurnal'
        ])
            ->when($this->kode_akun_id, function ($q) {
                $q->where('kode_akun_id', $this->kode_akun_id);
            })
            ->when($this->bulanPerolehan, function ($q) {
                $q->where('tanggal_perolehan', 'like', $this->bulanPerolehan . '%');
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
                ->when($this->bulanPerolehan, function ($q) {
                    $q->where('tanggal_perolehan', 'like', $this->bulanPerolehan . '%');
                })

                ->where(fn($q) => $q
                    ->where('nama', 'like', '%' . $this->cari . '%'))
                ->get()
        ]);
    }
}
