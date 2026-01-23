<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Stokmasuk;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\StokMasuk;
use App\Models\PemesananPengadaan;
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

    public function updated()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        StokMasuk::findOrFail($id)->forceDelete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.stokmasuk.index', [
            'pending' => PemesananPengadaan::select(DB::raw('pemesanan_pengadaan.id id'), 'tanggal', 'supplier_id', 'uraian')->with('supplier')
                ->leftJoin('pemesanan_pengadaan_detail', 'pemesanan_pengadaan.id', '=', 'pemesanan_pengadaan_detail.pemesanan_pengadaan_id')
                ->groupBy('pemesanan_pengadaan.id', 'tanggal', 'supplier_id', 'uraian')
                ->havingRaw('SUM(pemesanan_pengadaan_detail.qty) > (SELECT ifnull(SUM(stok_masuk.qty), 0) FROM stok_masuk WHERE pemesanan_pengadaan_id = pemesanan_pengadaan.id )')
                ->get()->count(),
            'data' => StokMasuk::with(['pengguna.pegawai', 'barangSatuan.barang', 'pemesananPengadaan.supplier', 'keluar'])
                ->where('created_at', 'like', $this->bulan . '%')
                ->whereNotNull('pemesanan_pengadaan_id')
                ->whereHas('pemesananPengadaan', fn($q) => $q->where('jenis', 'Barang Dagang'))
                ->where(fn($q) => $q
                    ->whereHas('barangSatuan.barang', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
                    ->orWhereHas('pemesananPengadaan', fn($q) => $q->where('uraian', 'like', '%' . $this->cari . '%')))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}
