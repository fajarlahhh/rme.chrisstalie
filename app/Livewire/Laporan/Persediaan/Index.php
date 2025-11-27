<?php

namespace App\Livewire\Laporan\Persediaan;

use App\Models\Stok;
use App\Models\Barang;
use Livewire\Component;
use App\Models\KodeAkun;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    #[Url]
    public $cari, $persediaan, $kode_akun_id;
    public $cetak, $dataKodeAkun = [], $data, $dataStok;

    public function mount()
    {
        $this->dataKodeAkun = KodeAkun::detail()->where('parent_id', '11300')->get()->toArray();
        $this->getData();
    }

    public function updated()
    {
        $this->getData();
    }

    private function getData()
    {
        $this->data = Barang::with(['barangSatuanUtama', 'kodeAkun'])
            ->when($this->persediaan, fn($q) => $q->where('persediaan', $this->persediaan))
            ->when($this->kode_akun_id, function ($q) {
                $q->where('kode_akun_id', $this->kode_akun_id);
            })
            ->where(fn($q) => $q
                ->where('nama', 'like', '%' . $this->cari . '%'))
            ->orderBy('nama')
            ->get();
        $this->dataStok = Stok::whereNull('stok_keluar_id')->select(DB::raw('barang_id, tanggal_kedaluarsa, harga_beli, count(*) as stok'))->groupBy('barang_id', 'tanggal_kedaluarsa', 'harga_beli')->get();
    }

    public function print()
    {
        $this->getData();
        $cetak = view('livewire.laporan.persediaan.cetak', [
            'cetak' => true,
            'data' => $this->data,
            'dataStok' => $this->dataStok,
            'persediaan' => $this->persediaan,
            'kode_akun' => $this->kode_akun_id ? collect($this->dataKodeAkun)->where('id', $this->kode_akun_id)->first()?->nama : '',
            'cari' => $this->cari,
        ])->render();
        session()->flash('cetak', $cetak);
    }


    public function render()
    {
        return view('livewire.laporan.persediaan.index');
    }
}
