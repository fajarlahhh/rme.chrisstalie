<?php

namespace App\Livewire\Kasir;

use Livewire\Component;
use App\Models\Pembayaran;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Data extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $tanggal1, $tanggal2;

    public function updated()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->tanggal1 = $this->tanggal1 ?: date('Y-m-d');
        $this->tanggal2 = $this->tanggal2 ?: date('Y-m-d');
    }

    public function delete($id)
    {
        Pembayaran::findOrFail($id)->delete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function print($id)
    {
        $data = Pembayaran::findOrFail($id);
        $cetak = view('livewire.kasir.cetak', [
            'cetak' => true,
            'data' => $data,
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function render()
    {
        return view('livewire.kasir.data', [
            'data' => Pembayaran::with('registrasi.pasien', 'registrasi.nakes', 'pengguna', 'registrasi.tindakan', 'registrasi.resepObat', 'registrasi.peracikanResepObat')
                ->where('tanggal', '>=', $this->tanggal1 . ' 00:00:00')
                ->where('tanggal', '<=', $this->tanggal2 . ' 23:59:59')
                ->where(fn($q) => $q->where('id', 'like', '%' . $this->cari . '%')
                    ->orWhere('registrasi_id', 'like', '%' . $this->cari . '%')
                    ->orWhereHas('registrasi.pasien', fn($r) => $r
                        ->where('nama', 'like', '%' . $this->cari . '%')
                        ->orWhere('id', 'like', '%' . $this->cari . '%')))
                ->orderBy('id', 'desc')->paginate(10)
        ]);
    }
}
