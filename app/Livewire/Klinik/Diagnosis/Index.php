<?php

namespace App\Livewire\Klinik\Diagnosis;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Registrasi;
use App\Models\Diagnosis;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $tanggal, $status = 1;
    
    public function mount()
    {
        $this->tanggal = $this->tanggal ?: date('Y-m-d');
    }

    public function delete($id)
    {
        Diagnosis::where('id', $id)->delete();
    }

    public function render()
    {
        return view('livewire.klinik.diagnosis.index', [
            'data' => Registrasi::with('pasien')->with('nakes')->with('pengguna')
                ->when($this->status == 2, fn($q) => $q->whereHas('diagnosis', fn($q) => $q->where('created_at', 'like', $this->tanggal . '%')))
                ->when($this->status == 1, fn($q) => $q->whereDoesntHave('diagnosis'))
                ->whereHas('pasien', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
                ->orderBy('urutan', 'asc')->paginate(10)
        ]);
    }
}
