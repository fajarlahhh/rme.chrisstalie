<?php

namespace App\Livewire\Klinik\Peracikanresepobat;

use App\Models\PeracikanResepObat;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Registrasi;
use App\Models\ResepObat;
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

    public function print($id)
    {
        $data = Registrasi::with('resepObat.barang', 'resepObat.barangSatuan')->findOrFail($id);
        $cetak = view('livewire.klinik.peracikanresepobat.cetak', [
            'cetak' => true,
            'data' => $data,
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function mount()
    {
        $this->tanggal = $this->tanggal ?: date('Y-m-d');
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            ResepObat::where('registrasi_id', $id)->whereNull('deleted_at')->forceDelete();
            ResepObat::where('registrasi_id', $id)->withTrashed()->restore();
            
            PeracikanResepObat::where('id', $id)->delete();
            session()->flash('success', 'Berhasil menghapus data');
        });
    }

    public function render()
    {
        return view('livewire.klinik.peracikanresepobat.index', [
            
            'data' => Registrasi::with('pasien')->with('nakes')->with('pengguna')->with('peracikanResepObat')->with('resepObat.pengguna')->with('pembayaran')
                ->when($this->status == 2, fn($q) => $q->whereHas('peracikanResepObat', fn($q) => $q->where('created_at', 'like', $this->tanggal . '%')))
                ->when($this->status == 1, fn($q) => $q->whereDoesntHave('peracikanResepObat')->whereDoesntHave('pembayaran'))
                ->where(fn($q) => $q->where('id', 'like', '%' . $this->cari . '%')
                    ->orWhereHas('pasien', fn($r) => $r
                        ->where('nama', 'like', '%' . $this->cari . '%')
                        ->orWhere('id', 'like', '%' . $this->cari . '%')))
                ->orderBy('id', 'asc')->paginate(10)
        ]);
    }
}
