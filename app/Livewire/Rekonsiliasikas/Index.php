<?php

namespace App\Livewire\Rekonsiliasikas;

use Livewire\Component;
use App\Models\Pembayaran;
use App\Models\MetodeBayar;

class Index extends Component
{
    public $tanggal;
    public $dataMetodeBayar = [];

    public function mount()
    {
        $this->dataMetodeBayar = MetodeBayar::get()->toArray();
    }

    public function render()
    {
        return view('livewire.rekonsiliasikas.index', [
            'data' => Pembayaran::whereNull('rekonsiliasi_at')->with('metodeBayar')->where('pengguna_id', auth()->user()->id)->get()->map(fn($q)=>[
                'tanggal' => $q->created_at->format('Y-m-d'),
                'kode_akun_id' => $q->metodeBayar->kode_akun_id,
                'metode_bayar' => $q->metode_bayar,
                'total_harga_barang' => $q->total_harga_barang,
                'total_resep' => $q->total_resep,
                'total_tindakan' => $q->total_tindakan,
                'total_tagihan' => $q->total_tagihan,
            ]),
        ]);
    }
}
