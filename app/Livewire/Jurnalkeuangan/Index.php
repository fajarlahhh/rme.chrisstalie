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
    public $cari, $bulan;


    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
    }

    public function delete($id)
    {
        $data = Jurnal::findOrFail($id);
        if ($data->referensi_id == null) {
            $data->jurnalDetail->delete();
            $data->delete();
        }
    }

    public function render()
    {
        return view('livewire.jurnalkeuangan.index', [
            'data' => Jurnal::with(['jurnalDetail.kodeAkun'])
                ->where('tanggal', 'like', $this->bulan . '%')
                ->where(fn($q) => $q->where('uraian', 'like', '%' . $this->cari . '%'))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}
