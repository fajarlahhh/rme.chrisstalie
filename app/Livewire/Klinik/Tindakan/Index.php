<?php

namespace App\Livewire\Klinik\Tindakan;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Tindakan;

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
        Tindakan::where('id', $id)->delete();
    }

    public function render()
    {
        return view('livewire.klinik.tindakan.index', [
            'data' => Registrasi::with('pasien')->with('nakes')->with('pengguna')
                ->when($this->status == 2, fn($q) => $q->whereHas('pemeriksaanAwal', fn($q) => $q->where('created_at', 'like', $this->tanggal . '%')))
                ->when($this->status == 1, fn($q) => $q->whereDoesntHave('pemeriksaanAwal'))
                ->whereHas('pasien', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
                ->orderBy('urutan', 'asc')->paginate(10)
        ]);
    }
}
