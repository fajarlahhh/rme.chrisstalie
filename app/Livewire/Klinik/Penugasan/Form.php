<?php

namespace App\Livewire\Klinik\Penugasan;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Registrasi;

class Form extends Component
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
    }

    public function render()
    {
        return view('livewire.klinik.penugasan.form', [
            'data' => Registrasi::with('pasien')->with('nakes')->with('pengguna')
                ->whereHas('tindakan')
                ->whereHas('pasien', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
                ->orderBy('urutan', 'asc')->paginate(10)
        ]);
    }
}
