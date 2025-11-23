<?php

namespace App\Livewire\Klinik\Kasir;

use Livewire\Component;
use App\Models\Pembayaran;
use App\Models\Registrasi;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

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
        Pembayaran::findOrFail($id)->delete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function print($id)
    {
        $data = Registrasi::findOrFail($id);
        $cetak = view('livewire.klinik.kasir.cetak', [
            'cetak' => true,
            'data' => $data,
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function render()
    {
        return view('livewire.klinik.kasir.index', [
            'data' => Registrasi::with('pasien')->with('nakes')->with('pengguna')
                ->whereHas('tindakan')->whereHas('resepObat')
                ->when($this->status == 2, fn($q) => $q->whereHas('pembayaran', fn($q) => $q->where('created_at', 'like', $this->tanggal . '%')))
                ->when($this->status == 1, fn($q) => $q->whereDoesntHave('pembayaran'))
                ->whereHas('pasien', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
                ->orderBy('id', 'asc')->paginate(10)
        ]);
    }
}
