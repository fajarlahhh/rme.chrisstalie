<?php

namespace App\Livewire\Kepegawaian\Penggajian;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\KepegawaianPenggajian;

class Index extends Component
{
    #[Url]
    public $cari, $bulan;

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
    }

    public function delete($id)
    {
        KepegawaianPenggajian::findOrFail($id)->delete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function render()
    {
        return view('livewire.kepegawaian.penggajian.index', [
            'data' => KepegawaianPenggajian::with('kodeAkunPembayaran','kepegawaianPegawai','pengguna.kepegawaianPegawai')->where('periode', 'like', $this->bulan . '%')->orderBy('periode', 'desc')->get()
        ]);
    }
}
