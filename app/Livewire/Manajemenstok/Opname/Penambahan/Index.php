<?php

namespace App\Livewire\Manajemenstok\Opname\Penambahan;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\StokMasuk;
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
    public function render()
    {
        return view('livewire.manajemenstok.opname.penambahan.index', [
            'data' => StokMasuk::with(['barang', 'barangSatuan'])->whereNull('pembelian_id')->whereBetween(DB::raw('DATE(created_at)'), [$this->tanggal1, $this->tanggal2])->get()
        ]);
    }
}
