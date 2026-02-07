<?php

namespace App\Livewire\Manajemenstok\Opname\Pengurangan;

use Livewire\Component;
use App\Models\StokKeluar;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Class\JurnalkeuanganClass;
use Illuminate\Support\Facades\DB;

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
            $data = StokKeluar::find($id);
            if (JurnalkeuanganClass::tutupBuku(substr($data->tanggal, 0, 7) . '-01')) {
                session()->flash('danger', 'Pembukuan periode ini sudah ditutup');
                return $this->render();
            }
            $data->delete();
        });
        session()->flash('success', 'Berhasil menghapus data');
    }
    
    public function render()
    {
        $query = StokKeluar::with(['barang', 'barangSatuan', 'pengguna', 'keuanganJurnal'])->whereNull('pembayaran_id')->where('created_at', 'like', $this->bulan . '%');

        if ($this->cari) {
            $query->where(function ($q) {
                $q->whereHas('barang', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'));
            });
        }

        $data = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.manajemenstok.opname.pengurangan.index', [
            'data' => $data
        ]);
    }
}
