<?php

namespace App\Livewire\Pengadaanbrgdagang\Stokmasuk;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\StokMasuk;
use App\Models\Pembelian;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $bulan;
    public $pending = 0;

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
        $pembelian = Pembelian::select(DB::raw('pembelian.id id'), 'tanggal', 'supplier_id', 'uraian')
        ->leftJoin('pembelian_detail', 'pembelian.id', '=', 'pembelian_detail.pembelian_id')
        ->groupBy('pembelian.id', 'tanggal', 'supplier_id', 'uraian')
        ->havingRaw('SUM(pembelian_detail.qty) > (SELECT ifnull(SUM(stok_masuk.qty), 0) FROM stok_masuk WHERE pembelian_id = pembelian.id )')
        ->with('supplier')->get();
        $this->pending = collect($pembelian)->count();
    }

    public function delete($id)
    {
        $data = StokMasuk::find($id);
        if ($data->keluar->count() == 0) {
            $data->delete();
        }
    }

    public function render()
    {
        return view('livewire.pengadaanbrgdagang.stokmasuk.index', [
            'data' => StokMasuk::with(['pengguna', 'barang', 'pembelian', 'keluar'])
                ->where('created_at', 'like', $this->bulan . '%')
                ->whereHas('barang', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
                ->whereHas('pembelian', fn($q) => $q->where('uraian', 'like', '%' . $this->cari . '%'))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}
