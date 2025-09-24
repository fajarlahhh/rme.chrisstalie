<?php

namespace App\Livewire\Klinik\Sitemarking;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\Registrasi;
use App\Models\SiteMarking;

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
        SiteMarking::where('id', $id)->delete();
    }

    public function render()
    {
        return view('livewire.klinik.sitemarking.data', [
            'data' => Registrasi::with('pasien')->with('nakes')->with('pengguna')->with('siteMarking')
                ->whereHas('siteMarking')
                ->whereHas('siteMarking', fn($q) => $q->where('created_at', 'like', $this->tanggal . '%'))
                ->whereHas('pasien', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
                ->orderBy('urutan', 'asc')->paginate(10)
        ]);
    }
}
