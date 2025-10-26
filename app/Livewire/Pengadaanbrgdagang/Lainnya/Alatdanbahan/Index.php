<?php

namespace App\Livewire\Pengadaanbrgdagang\Lainnya\Alatdanbahan;

use Livewire\Component;
use App\Models\StokMasuk;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\DB;
use App\Models\Pembelian;

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
        DB::transaction(function () use ($id) {
            $data = StokMasuk::find($id);
            if ($data->keluar->count() == 0) {
                $data->jurnalAlatDanBahan->delete();
                $data->delete();

                if (StokMasuk::where('pembelian_id', $data->pembelian_id)->count() == 0) {
                    Pembelian::find($data->pembelian_id)->delete();
                }
                session()->flash('success', 'Berhasil menghapus data');
            }
        });
    }

    public function render()
    {
        return view('livewire.pengadaanbrgdagang.lainnya.alatdanbahan.index', [
            'data' => StokMasuk::with(['pengguna', 'barangSatuan.barang', 'pembelian'])
                ->where('created_at', 'like', $this->bulan . '%')
                ->whereHas('pembelian', fn($q) => $q->where('jenis', 'Alat dan Bahan'))
                ->where(fn($q) => $q
                    ->whereHas('barangSatuan.barang', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
                    ->orWhereHas('pembelian', fn($q) => $q->where('uraian', 'like', '%' . $this->cari . '%')))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}
