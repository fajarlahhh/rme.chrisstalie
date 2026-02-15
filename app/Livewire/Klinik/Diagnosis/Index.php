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
    public $cari = '', $tanggal, $status = 1;

    public function updated()
    {
        $this->resetPage();
    }

    public function mount()
    {
        if (empty($this->tanggal)) {
            $this->tanggal = date('Y-m-d');
        }
    }

    public function delete($id)
    {
        Diagnosis::where('id', $id)->delete();
    }

    protected function getQuery()
    {
        $query = Registrasi::query()
            ->with(['pasien', 'nakes.kepegawaianPegawai', 'pengguna', 'pembayaran', 'diagnosis.pengguna'])
            ->whereHas('pemeriksaanAwal')
            ->where('ketemu_dokter', 1)
            ->whereHas('pasien', function ($q) {
                if (!empty($this->cari)) {
                    $q->where('nama', 'like', '%' . $this->cari . '%');
                }
            });

        if ($this->status == 2) {
            $query->whereHas('diagnosis', function ($q) {
                $q->whereDate('created_at', $this->tanggal);
            });
        } elseif ($this->status == 1) {
            $query->whereDoesntHave('diagnosis')->whereDoesntHave('pembayaran');
        }

        return $query->orderBy('id', 'asc');
    }

    public function render()
    {
        return view('livewire.klinik.diagnosis.index', [
            'data' => $this->getQuery()->paginate(10)
        ]);
    }
}
