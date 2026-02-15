<?php

namespace App\Livewire\Penjualan;

use Livewire\Component;
use App\Models\Pembayaran;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\DB;

class Data extends Component
{
    #[Url]
    public $tanggal1, $tanggal2, $cari;

    public function mount()
    {
        $this->tanggal1 = $this->tanggal1 ?: date('Y-m-d');
        $this->tanggal2 = $this->tanggal2 ?: date('Y-m-d');
    }

    public function print($id)
    {
        $data = Pembayaran::with(['stokKeluar.barang', 'stokKeluar.barangSatuan'])->findOrFail($id);
        $cetak = view('livewire.penjualan.cetak', [
            'cetak' => true,
            'data' => $data,
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function delete($id)
    {
        Pembayaran::findOrFail($id)->forceDelete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function render()
    {
        $query = Pembayaran::where('bebas', 1)->with(['stokKeluar.barang', 'stokKeluar.barangSatuan', 'pengguna']);

        if ($this->tanggal1) {
            $query->whereBetween(DB::raw('DATE(tanggal)'), [$this->tanggal1, $this->tanggal2]);
        }

        if ($this->cari) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->cari . '%');
            });
        }

        $data = $query->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('livewire.penjualan.data', [
            'data' => $data
        ]);
    }
}
