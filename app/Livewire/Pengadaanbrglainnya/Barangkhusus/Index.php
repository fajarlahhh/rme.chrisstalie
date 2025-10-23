<?php

namespace App\Livewire\Pengadaanbrglainnya\Barangkhusus;

use Livewire\Component;
use App\Models\Pembelian;
use App\Models\StokMasuk;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
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
        DB::transaction(function () use ($id) {
            $data = StokMasuk::find($id);
            if ($data->keluar->count() == 0) {
                $data->jurnal->delete();
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
        return view('livewire.pengadaanbrglainnya.barangkhusus.index', [
            'data' => StokMasuk::with(['pengguna', 'barangSatuan.barang', 'pembelian'])
                ->where('created_at', 'like', $this->bulan . '%')
                ->whereHas('pembelian', fn($q) => $q->where('jenis', 'Barang Khusus'))
                ->where(fn($q) => $q
                    ->whereHas('barangSatuan.barang', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
                    ->orWhereHas('pembelian', fn($q) => $q->where('uraian', 'like', '%' . $this->cari . '%')))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}
