<?php

namespace App\Livewire\Kepegawaian\Penggajian;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\KepegawaianPenggajian;

class Index extends Component
{
    #[Url]
    public $cari, $tahun;

    public function mount()
    {
        $this->tahun = $this->tahun ?: date('Y');
    }

    public function delete($id)
    {
        KepegawaianPenggajian::findOrFail($id)->delete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function render()
    {
        return view('livewire.kepegawaian.penggajian.index', [
            'data' => KepegawaianPenggajian::with('kodeAkunPembayaran','kepegawaianPegawai','pengguna.kepegawaianPegawai')->where('periode', 'like', $this->tahun . '%')->orderBy('periode', 'desc')->get()
        ]);
    }
}
