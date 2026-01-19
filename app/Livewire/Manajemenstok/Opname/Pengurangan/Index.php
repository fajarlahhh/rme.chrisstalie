<?php

namespace App\Livewire\Manajemenstok\Opname\Pengurangan;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\StokKeluar;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    #[Url]
    public $tanggal1, $tanggal2, $cari;

    public function mount()
    {
        $this->tanggal1 = $this->tanggal1 ?: date('Y-m-01');
        $this->tanggal2 = $this->tanggal2 ?: date('Y-m-d');
    }


    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            StokKeluar::find($id)->delete();
        });
        session()->flash('success', 'Berhasil menghapus data');
    }
    
    public function render()
    {
        $query = StokKeluar::with(['barang', 'barangSatuan'])->whereNull('pembayaran_id');

        if ($this->tanggal1) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$this->tanggal1, $this->tanggal2]);
        }

        if ($this->cari) {
            $query->where(function ($q) {
                $q->whereHas('barang', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'));
            });
        }

        $data = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('livewire.manajemenstok.opname.pengurangan.index', [
            'data' => $data
        ]);
    }
}
