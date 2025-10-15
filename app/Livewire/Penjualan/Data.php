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
        DB::transaction(function () use ($id) {
            $data = Pembayaran::findOrFail($id);
            $data->jurnalPenjualanBarangBebas()->delete();
            $data->delete();
            session()->flash('success', 'Berhasil menghapus data');
        });
    }

    public function render()
    {
        $query = Pembayaran::with(['stokKeluar.barang', 'stokKeluar.barangSatuan', 'pengguna']);

        if ($this->tanggal1) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$this->tanggal1, $this->tanggal2]);
        }

        if ($this->cari) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->cari . '%');
            });
        }

        $data = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('livewire.penjualan.data', [
            'data' => $data
        ]);
    }
}
