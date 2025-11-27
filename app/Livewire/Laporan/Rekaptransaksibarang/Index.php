<?php

namespace App\Livewire\Laporan\Rekaptransaksibarang;

use App\Models\Barang;
use Livewire\Component;
use App\Models\KodeAkun;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\Stok;

class Index extends Component
{
    #[Url]
    public $cari, $persediaan, $kode_akun_id, $bulan;
    public $cetak, $dataKodeAkun = [];

    public function mount()
    {
        $this->dataKodeAkun = KodeAkun::detail()->where('parent_id', '11300')->get()->toArray();
        $this->bulan = $this->bulan ?: date('Y-m');
    }

    public function print()
    {
        $cetak = view('livewire.laporan.rekaptransaksibarang.cetak', [
            'cetak' => true,
            'bulan' => $this->bulan,
            'persediaan' => $this->persediaan,
            'kode_akun' => $this->kode_akun_id ? collect($this->dataKodeAkun)->where('id', $this->kode_akun_id)->first()?->nama : '',
            'cari' => $this->cari,
            'data' => $this->getData(),
        ])->render();
        session()->flash('cetak', $cetak);
    }

    private function getData()
    {
        return Barang::with([
            'barangSatuanUtama',
            'kodeAkun',
            'stokAwal' => function ($q) {
                $q->where('tanggal', 'like', $this->bulan . '%');
            },
            'stokMasuk' => function ($q) {
                $q->where('tanggal', 'like', $this->bulan . '%');
            },
            'stokKeluar' => function ($q) {
                $q->where('tanggal', 'like', $this->bulan . '%');
            },
        ])
            ->when($this->persediaan, fn($q) => $q->where('persediaan', $this->persediaan))
            ->when($this->kode_akun_id, function ($q) {
                $q->where('kode_akun_id', $this->kode_akun_id);
            })
            ->where(fn($q) => $q
                ->where('nama', 'like', '%' . $this->cari . '%'))
            ->orderBy('nama')
            ->get();
    }

    public function render()
    {
        return view('livewire.laporan.rekaptransaksibarang.index', [
            'data' => $this->getData(),
        ]);
    }
}
