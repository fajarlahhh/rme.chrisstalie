<?php

namespace App\Livewire\Klinik\Kasir;

use Livewire\Component;
use App\Models\Penjualan;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Registrasi;

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
        Penjualan::where('id', $id)->delete();
    }

    public function render()
    {
        return view('livewire.klinik.kasir.index', [
            'data' => Registrasi::with('pasien')->with('nakes')->with('pengguna')
                ->whereHas('tindakan')->whereHas('resepObat')
                ->when($this->status == 2, fn($q) => $q->whereHas('pembayaran', fn($q) => $q->where('created_at', 'like', $this->tanggal . '%')))
                ->when($this->status == 1, fn($q) => $q->whereDoesntHave('pembayaran'))
                ->whereHas('pasien', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
                ->orderBy('urutan', 'asc')->paginate(10)
        ]);
    }
}
