<?php

namespace App\Livewire\Klinik\Sitemarking;

use Livewire\Component;
use App\Models\Registrasi;
use App\Models\SiteMarking;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari = '', $tanggal, $status = 1;

    public function mount()
    {
        $this->tanggal = $this->tanggal ?: date('Y-m-d');
        $this->cari = $this->cari ?? '';
    }

    public function delete($id)
    {
        SiteMarking::where('id', $id)->delete();
    }

    public function getQuery()
    {
        $query = Registrasi::query()
        ->whereHas('tindakan', fn($q) => $q->where('membutuhkan_sitemarking', 1))
            ->with([
                'pasien',
                'nakes',
                'pengguna',
                'siteMarking.pengguna'
            ]);

        if ($this->status == 2) {
            $query->whereHas('siteMarking', function ($q) {
                $q->whereDate('created_at', $this->tanggal);
            });
        } elseif ($this->status == 1) {
            $query->whereDoesntHave('siteMarking');
        }

        if (!empty($this->cari)) {
            $query->whereHas('pasien', function ($q) {
                $q->where('nama', 'like', '%' . $this->cari . '%');
            });
        }

        return $query->orderBy('urutan', 'asc');
    }

    public function render()
    {
        $data = $this->getQuery()->paginate(10);

        return view('livewire.klinik.sitemarking.index', [
            'data' => $data
        ]);
    }
}
