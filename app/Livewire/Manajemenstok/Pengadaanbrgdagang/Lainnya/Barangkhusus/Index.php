<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Lainnya\Barangkhusus;

use Livewire\Component;
use App\Models\Pembelian;
use App\Models\StokMasuk;
use App\Models\Jurnal;
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

    public function updated()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        Pembelian::find($id)->delete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.lainnya.barangkhusus.index', [
            'data' => Pembelian::where('jenis', 'Barang Khusus')->with(['pembelianDetail.barangSatuan.barang', 'pengguna.pegawai', 'supplier', 'stokKeluar', 'pelunasanPembelian.kodeAkunPembayaran', 'kodeAkun'])->where('created_at', 'like', $this->bulan . '%')
                ->paginate(10)
        ]);
    }
}
