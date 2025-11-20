<?php

namespace App\Livewire\Kepegawaian\Jadwalshift;

use Livewire\Component;
use App\Models\Absensi;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\Pegawai;

class Index extends Component
{
    use WithPagination;
    #[Url]
    public $cari, $bulan, $pegawai_id;
    public $dataPegawai = [];

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
        $this->dataPegawai = Pegawai::orderBy('nama')->get()->toArray();
    }

    public function updated()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        if (Absensi::where('id', $id)->whereNull('masuk')->exists()) {
            Absensi::findOrFail($id)->delete();
            session()->flash('success', 'Berhasil menghapus data');
        } else {
            Absensi::where('id', $id)->update(['shift' => null, 'jam_masuk' => null, 'jam_pulang' => null]);
            session()->flash('success', 'Berhasil menghapus data');
        }
    }

    public function render()
    {
        return view('livewire.kepegawaian.jadwalshift.index', [
            'data' => $this->pegawai_id ? Absensi::with(['pegawai'])->whereNotNull('shift')
                ->where('pegawai_id', $this->pegawai_id)
                ->where('tanggal', 'like', $this->bulan . '%')->orderBy('tanggal', 'asc')->paginate(10) : collect([]),
        ]);
    }
}
