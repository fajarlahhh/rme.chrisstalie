<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Lainnya\Alatdanbahan;

use Livewire\Component;
use App\Models\StokMasuk;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Jurnal;
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
        return view('livewire.manajemenstok.pengadaanbrgdagang.lainnya.alatdanbahan.index', [
            'data' => Pembelian::where('jenis', 'Alat dan Bahan')->with(['pembelianDetail.barangSatuan.barang', 'pengguna.pegawai', 'stokKeluar', 'pelunasanPembelian.kodeAkunPembayaran', 'kodeAkun'])->where('created_at', 'like', $this->bulan . '%')
                ->paginate(10)
        ]);
    }
}
