<?php

namespace App\Livewire\Klinik\Upload;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Registrasi;
use App\Models\Tindakan;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari = '', $tanggal, $status = 1;

    public function mount()
    {
        if (empty($this->tanggal)) {
            $this->tanggal = date('Y-m-d');
        }
    }

    public function delete($id)
    {
        Tindakan::find($id)?->delete();
    }

    public function getQuery()
    {
        $query = Registrasi::query()
            ->with(['pasien', 'nakes', 'pengguna'])
            ->whereHas('file', function ($q) {
                $q->where('jenis', 'Informed Consent');
            })
            ->whereHas('pasien', function ($q) {
                if (!empty($this->cari)) {
                    $q->where('nama', 'like', '%' . $this->cari . '%');
                }
            });

        if ($this->status == 2) {
            $query->whereHas('tindakan', function ($q) {
                $q->whereDate('created_at', $this->tanggal);
            });
        } elseif ($this->status == 1) {
            $query->whereDoesntHave('tindakan');
        }

        return $query->orderBy('id', 'asc');
    }

    public function updated()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.klinik.upload.index', [
            'data' => $this->getQuery()->paginate(10)
        ]);
    }
}
