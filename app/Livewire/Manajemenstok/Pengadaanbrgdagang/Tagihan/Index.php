<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Tagihan;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\PengadaanTagihan;
use App\Class\JurnalkeuanganClass;

class Index extends Component
{
    #[Url]
    public $bulan, $cari;

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
    }

    public function delete($id)
    {
        if (JurnalkeuanganClass::tutupBuku($this->bulan . '-01')) {
            session()->flash('danger', 'Pembukuan periode ini sudah ditutup');
            return;
        }
        $data = PengadaanTagihan::find($id);
        $data->delete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.tagihan.index', [
            'data' => PengadaanTagihan::with(['pengadaanPemesanan.pengadaanPemesananDetail.barangSatuan.barang', 'pengadaanPemesanan.supplier', 'keuanganJurnal'])
                ->where('tanggal', 'like', $this->bulan . '%')
                ->where(fn($q) => $q->where('no_faktur', 'like', '%' . $this->cari . '%')
                    ->orWhere('catatan', 'like', '%' . $this->cari . '%')
                    ->orWhereHas('pengadaanPemesanan', fn($q) => $q->where('uraian', 'like', '%' . $this->cari . '%'))
                    ->orWhereHas('supplier', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%')))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }
}
