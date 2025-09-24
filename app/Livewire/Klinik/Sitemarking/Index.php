<?php

namespace App\Livewire\Klinik\Sitemarking;

use Livewire\Component;
use App\Models\Registrasi;
use App\Models\SiteMarking;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

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
        SiteMarking::where('id', $id)->delete();
    }

    public function render()
    {
        return view('livewire.klinik.sitemarking.index', [
            'data' => Registrasi::with('pasien')->with('nakes')->with('pengguna')->with('siteMarking')
                ->when($this->status == 2, fn($q) => $q->whereHas('siteMarking', fn($q) => $q->where('created_at', 'like', $this->tanggal . '%')))
                ->when($this->status == 1, fn($q) => $q->whereDoesntHave('siteMarking'))
                ->whereHas('pasien', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
                ->orderBy('urutan', 'asc')->paginate(10)
        ]);
    }
}
