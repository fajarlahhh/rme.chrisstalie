<?php

namespace App\Livewire\Manajemenstok\Opname\Penambahan;

use Livewire\Component;
use App\Models\StokMasuk;
use App\Class\BarangClass;
use Livewire\Attributes\Url;
use App\Class\JurnalkeuanganClass;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    #[Url]
    public $bulan, $cari;

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $data = StokMasuk::find($id);
            if (JurnalkeuanganClass::tutupBuku(substr($data->tanggal, 0, 7) . '-01')) {
                session()->flash('danger', 'Pembukuan periode ini sudah ditutup');
                return $this->render();
            }

            if (BarangClass::hapusStok($data->barang_id, $data->qty, $data->id)) {
                session()->flash('success', 'Berhasil menghapus data');
                $data->forceDelete();
            }
        });
    }

    public function render()
    {
        return view('livewire.manajemenstok.opname.penambahan.index', [
            'data' => StokMasuk::with(['barang', 'barangSatuan', 'keluar', 'pengguna', 'keuanganJurnal'])->whereNull('pengadaan_pemesanan_id')->where('created_at', 'like', $this->bulan . '%')->paginate(10)
        ]);
    }
}
