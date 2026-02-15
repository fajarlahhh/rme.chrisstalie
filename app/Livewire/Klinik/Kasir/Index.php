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
    public $cari, $tanggal1 , $tanggal2, $status = 1;

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
                Registrasi::with('pasien', 'nakes', 'pengguna', 'tindakan', 'resepObat', 'peracikanResepObat')
                ->whereDoesntHave('pembayaran')
                // jika ada resepObat maka juga harus ada peracikanResepObat
                ->where(function($query) {
                    $query->whereDoesntHave('resepObat')
                        ->orWhere(function($query2) {
                            $query2->whereHas('resepObat')
                                ->whereHas('peracikanResepObat');
                        });
                })
                ->where(fn($q) => $q->where('id', 'like', '%' . $this->cari . '%')
                    ->orWhereHas('pasien', fn($r) => $r
                        ->where('nama', 'like', '%' . $this->cari . '%')
                        ->orWhere('id', 'like', '%' . $this->cari . '%')))
                ->orderBy('id', 'asc')->paginate(10) :
                Pembayaran::with('registrasi.pasien', 'registrasi.nakes', 'pengguna', 'registrasi.tindakan', 'registrasi.resepObat', 'registrasi.peracikanResepObat')
                ->whereNotNull('registrasi_id')
                ->where('tanggal', '>=', $this->tanggal1 . ' 00:00:00')
                ->where('tanggal', '<=', $this->tanggal2 . ' 23:59:59')
                ->where(fn($q) => $q->where('id', 'like', '%' . $this->cari . '%')
                    ->orWhere('registrasi_id', 'like', '%' . $this->cari . '%')
                    ->orWhereHas('registrasi.pasien', fn($r) => $r
                        ->where('nama', 'like', '%' . $this->cari . '%')
                        ->orWhere('id', 'like', '%' . $this->cari . '%')))
                ->orderBy('id', 'asc')->paginate(10)
        ]);
    }
}
