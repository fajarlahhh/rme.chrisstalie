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
    public $cari, $tanggal, $dataHakKewajiban, $status = 1;

    public function mount()
    {
        $this->tanggal = $this->tanggal ?: date('Y-m-d');
    }

    public function delete($id)
    {
        Registrasi::where('id', $id)->delete();
    }

    public function hakKewajiban($id)
    {
        $this->dataHakKewajiban = Registrasi::find($id);
    }

    public function submitHakKewajiban()
    {
        dd($this->dataHakKewajiban);
    }

    public function render()
    {
        return view('livewire.klinik.registrasi.data', [
            'data' => Registrasi::with([
                'pasien',
                'nakes',
                'pengguna.pegawai',
                'pembayaran'
            ])
                ->when($this->status == 2, fn($q) => $q->whereHas('pembayaran')->where('tanggal', $this->tanggal))
                ->when($this->status == 1, fn($q) => $q->whereDoesntHave('pembayaran'))
                ->whereHas('pasien', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))

                ->orderBy('id', 'asc')
                ->paginate(10)
        ]);
    }
}
