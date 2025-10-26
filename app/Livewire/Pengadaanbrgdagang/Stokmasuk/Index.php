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

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
    }

    public function delete($id)
    {
        $data = StokMasuk::find($id);
        if ($data->keluar->count() == 0) {
            $data->jurnalBarangDagang->delete();
            $data->delete();
            session()->flash('success', 'Berhasil menghapus data');
        }
    }

    public function render()
    {
        return view('livewire.pengadaanbrgdagang.stokmasuk.index', [
            'pending' => Pembelian::select(DB::raw('pembelian.id id'), 'tanggal', 'supplier_id', 'uraian')
                ->leftJoin('pembelian_detail', 'pembelian.id', '=', 'pembelian_detail.pembelian_id')
                ->groupBy('pembelian.id', 'tanggal', 'supplier_id', 'uraian')
                ->havingRaw('SUM(pembelian_detail.qty) > (SELECT ifnull(SUM(stok_masuk.qty), 0) FROM stok_masuk WHERE pembelian_id = pembelian.id )')
                ->get()->count(),
            'data' => StokMasuk::with(['pengguna', 'barangSatuan.barang', 'pembelian'])
                ->where('created_at', 'like', $this->bulan . '%')
                ->whereHas('pembelian', fn($q) => $q->where('jenis', 'Barang Dagang'))
                ->where(fn($q) => $q
                    ->whereHas('barangSatuan.barang', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
                    ->orWhereHas('pembelian', fn($q) => $q->where('uraian', 'like', '%' . $this->cari . '%')))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}
