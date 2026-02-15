<?php

namespace App\Livewire\Klinik\Resepobat;

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
        ResepObat::where('registrasi_id', $id)->forceDelete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function render()
    {
        return view('livewire.klinik.resepobat.index', [
            'data' => Registrasi::with('pasien')->with('nakes.kepegawaianPegawai')->with('pengguna')->with('peracikanResepObat')->with('resepObat.pengguna')->with('pembayaran')
                ->when($this->status == 2, fn($q) => $q->whereHas('resepObat', fn($r) => $r->withTrashed()->where('created_at', 'like', $this->tanggal . '%')))                
                ->when($this->status == 1, fn($q) => $q->whereDoesntHave('resepObat')->whereDoesntHave('pembayaran'))
                ->where(fn($q) => $q->where('id', 'like', '%' . $this->cari . '%')
                    ->orWhereHas('pasien', fn($r) => $r
                        ->where('nama', 'like', '%' . $this->cari . '%')
                        ->orWhere('id', 'like', '%' . $this->cari . '%')))
                ->orderBy('id', 'asc')->paginate(10)
        ]);
    }
}
