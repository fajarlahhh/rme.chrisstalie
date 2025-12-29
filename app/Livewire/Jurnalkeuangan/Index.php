<?php

namespace App\Livewire\Jurnalkeuangan;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Jurnal;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $bulan, $jenis;

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
    }

    public function delete($id)
    {
        Jurnal::findOrFail($id)->delete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function getData()
    {
        return Jurnal::with(['jurnalDetail.kodeAkun', 'pengguna.pegawai'])
            ->when($this->jenis, fn($q) => $q->where('jenis', $this->jenis))
            ->where('tanggal', 'like', $this->bulan . '%')
            ->where(
                fn($q) => $q
                    ->where('id', 'like', $this->cari . '%')
                    ->orWhere('nomor', 'like', $this->cari . '%')
                    ->orWhere('uraian', 'like', '%' . $this->cari . '%')
            )
            ->orderBy('tanggal', 'desc')
            ->when(!auth()->user()->hasRole('administrator'), fn($q) => $q->where('pengguna_id', auth()->id()))
            ->paginate(10);
    }

    public function getJenis()
    {
        return Jurnal::where('tanggal', 'like', $this->bulan . '%')->select('jenis')->groupBy('jenis')->orderBy('jenis', 'asc')->get()->toArray();
    }

    public function render()
    {
        return view('livewire.jurnalkeuangan.index', [
            'data' => $this->getData(),
            'dataJenis' => $this->getJenis()
        ]);
    }
}
