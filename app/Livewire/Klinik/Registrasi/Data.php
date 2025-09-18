<?php

namespace App\Livewire\Klinik\Registrasi;

use Livewire\Component;
use App\Models\Registrasi;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Data extends Component
{
    use WithPagination;
    #[Url]
    public $cari, $tanggal;

    public function mount()
    {
        $this->tanggal = $this->tanggal ?: date('Y-m-d');
    }

    public function delete($id)
    {
        Registrasi::where('id', $id)->delete();
    }

    public function render()
    {
        return view('livewire.klinik.registrasi.data', [
            'data' => Registrasi::with([
                'pasien',
                'nakes',
                'pengguna'
            ])->whereHas('pasien', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
                ->where('tanggal', $this->tanggal)
                ->orderBy('urutan', 'asc')
                ->paginate(10)
        ]);
    }
}
