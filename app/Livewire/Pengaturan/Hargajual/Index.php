<?php

namespace App\Livewire\Pengaturan\Hargajual;

use Livewire\Component;
use App\Models\BarangSatuan;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\Barang;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $barang_id;
    public $dataBarang = [];

    public function mount()
    {
        $this->dataBarang = Barang::all()->toArray();
    }

    public function updated()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        try {
            $data = BarangSatuan::findOrFail($id);
            if (!$data->rasio_dari_terkecil == 1) {
                $data->barang->forceDelete();
            }
            $data->forceDelete();
            session()->flash('success', 'Berhasil menghapus data');
        } catch (\Throwable $th) {
            session()->flash('danger', 'Gagal menghapus data');
        };
    }

    public function render()
    {
        return view('livewire.pengaturan.hargajual.index', [
            'data' => BarangSatuan::select('barang_satuan.*', 'barang.nama as barang_nama')
                ->with(['barang', 'pengguna', 'satuanKonversi'])
                ->when($this->barang_id, fn($q) => $q->where('barang_id', $this->barang_id))
                ->leftJoin('barang', 'barang_satuan.barang_id', '=', 'barang.id')
                ->orderBy('barang.nama')
                ->orderBy('barang_satuan.rasio_dari_terkecil', 'asc')
                ->paginate(10)
        ]);
    }
}
