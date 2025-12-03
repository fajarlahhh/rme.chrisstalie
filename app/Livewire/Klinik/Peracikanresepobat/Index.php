<?php

namespace App\Livewire\Klinik\Peracikanresepobat;

use App\Models\PeracikanResepObat;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Registrasi;
use App\Models\ResepObat;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $tanggal, $status = 1;

    public function updated()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->tanggal = $this->tanggal ?: date('Y-m-d');
    }

    public function delete($id)
    {
        PeracikanResepObat::where('registrasi_id', $id)->delete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function render()
    {
        return view('livewire.klinik.peracikanresepobat.index', [
            
            'data' => Registrasi::with('pasien')->with('nakes')->with('pengguna')
                ->when($this->status == 2, fn($q) => $q->whereHas('peracikanResepObat', fn($q) => $q->where('created_at', 'like', $this->tanggal . '%')))
                ->whereDoesntHave('pembayaran')
                ->when($this->status == 1, fn($q) => $q->whereDoesntHave('peracikanResepObat'))
                ->where(fn($q) => $q->where('id', 'like', '%' . $this->cari . '%')
                    ->orWhereHas('pasien', fn($r) => $r
                        ->where('nama', 'like', '%' . $this->cari . '%')
                        ->orWhere('id', 'like', '%' . $this->cari . '%')))
                ->orderBy('id', 'asc')->paginate(10)
        ]);
    }
}
