<?php

namespace App\Livewire\Kepegawaian\Penggajian;

use App\Models\Jurnal;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class Index extends Component
{
    use WithPagination;
    #[Url]
    public $cari, $bulan;

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
    }

    public function delete($id)
    {
        Jurnal::findOrFail($id)->delete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function render()
    {
        return view('livewire.kepegawaian.penggajian.index', [
            'data' => Jurnal::where('jenis', 'Gaji')->where('tanggal', 'like', $this->bulan . '%')->where(fn($q) => $q->where('uraian', 'like', '%' . $this->cari . '%'))->paginate(10)
        ]);
    }
}
