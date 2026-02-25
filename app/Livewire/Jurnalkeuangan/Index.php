<?php

namespace App\Livewire\Jurnalkeuangan;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\KeuanganJurnal;
use App\Class\JurnalkeuanganClass;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $bulan, $jenis, $kategori;

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
    }

    public function delete($id)
    {
        $data = KeuanganJurnal::findOrFail($id);
        if (JurnalkeuanganClass::tutupBuku(substr($data->tanggal, 0, 7) . '-01')) {
            session()->flash('danger', 'Pembukuan periode ini sudah ditutup');
            return;
        }
        $data->delete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function getData()
    {
        return KeuanganJurnal::with(['keuanganJurnalDetail.kodeAkun', 'pengguna'])
            ->when($this->jenis, fn($q) => $q->where('jenis', $this->jenis))
            ->where('tanggal', 'like', $this->bulan . '%')
            ->when($this->kategori == 1, fn($q) => $q->where('system', $this->kategori))
            ->when($this->kategori == 2, fn($q) => $q->where('system', '0'))
            ->where(
                fn($q) => $q
                    ->where('id', 'like', $this->cari . '%')
                    ->orWhere('nomor', 'like', $this->cari . '%')
                    ->orWhere('uraian', 'like', '%' . $this->cari . '%')
            )
            ->orderBy('tanggal', 'desc')
            ->when(!auth()->user()->hasRole(['administrator', 'supervisor']), fn($q) => $q->where('pengguna_id', auth()->id()))
            ->paginate(10);
    }

    public function getJenis()
    {
        return KeuanganJurnal::where('tanggal', 'like', $this->bulan . '%')->select('jenis')->groupBy('jenis')->orderBy('jenis', 'asc')->get()->toArray();
    }

    public function render()
    {
        return view('livewire.jurnalkeuangan.index', [
            'data' => $this->getData(),
            'dataJenis' => $this->getJenis()
        ]);
    }
}
