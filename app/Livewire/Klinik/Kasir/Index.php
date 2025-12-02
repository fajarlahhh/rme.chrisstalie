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
        $data = Pembayaran::findOrFail($id);
        $cetak = view('livewire.klinik.kasir.cetak', [
            'cetak' => true,
            'data' => $data,
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function render()
    {
        return view('livewire.klinik.kasir.index', [
            'data' => $this->status == 1 ?
                Registrasi::with('pasien')->with('nakes')->with('pengguna')
                ->whereDoesntHave('pembayaran')
                ->where(fn($q) => $q->where('id', 'like', '%' . $this->cari . '%')
                    ->orWhereHas('pasien', fn($r) => $r
                        ->where('nama', 'like', '%' . $this->cari . '%')
                        ->orWhere('id', 'like', '%' . $this->cari . '%')))
                ->orderBy('id', 'asc')->paginate(10) : (Pembayaran::with('registrasi.pasien', 'registrasi.nakes', 'pengguna')
                    ->whereNotNull('registrasi_id')
                    ->where('created_at', 'like', $this->tanggal . '%')
                    ->where(fn($q) => $q->where('id', 'like', '%' . $this->cari . '%')
                        ->orWhere('registrasi_id', 'like', '%' . $this->cari . '%')
                        ->orWhereHas('registrasi.pasien', fn($r) => $r
                            ->where('nama', 'like', '%' . $this->cari . '%')
                            ->orWhere('id', 'like', '%' . $this->cari . '%')))
                    ->orderBy('id', 'asc')->paginate(10))
        ]);
    }
}
