<?php

namespace App\Livewire\Laporan\Penerimaan;

use Livewire\Component;
use App\Models\Pengguna;
use App\Models\Pembayaran;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanpenerimaanExport;
use Illuminate\Support\Facades\DB;
use App\Models\MetodeBayar;

class Index extends Component
{
    #[Url]
    public $tanggal1, $tanggal2, $pengguna_id, $metode_bayar;
    public $dataMetodeBayar = [];

    public function mount()
    {
        $this->tanggal1 = $this->tanggal1 ?: date('Y-m-d');
        $this->tanggal2 = $this->tanggal2 ?: date('Y-m-d');
        $this->dataMetodeBayar = MetodeBayar::get()->toArray();
    }

    public function export()
    {
        return Excel::download(new LaporanpenerimaanExport(
            $this->getData(false),
            $this->tanggal1,
            $this->tanggal2,
            Pengguna::find($this->pengguna_id)?->nama,
            $this->metode_bayar
        ), 'penerimaan.xlsx');
    }

    private function getData($paginate = true)
    {
        $query = Pembayaran::with(['registrasi.pasien', 'pengguna'])->whereBetween(DB::raw('DATE(tanggal)'), [$this->tanggal1, $this->tanggal2]);
        if (!auth()->user()->hasRole(['administrator', 'supervisor'])) {
            $query->where('pengguna_id', auth()->id());
        }
        return $query->get();
    }

    public function render()
    {
        $data = $this->getData(true);
        return view('livewire.laporan.penerimaan.index', [
            'data' =>  $data->when($this->pengguna_id, fn($q) => $q->where('pengguna_id', $this->pengguna_id))->when($this->metode_bayar, fn($q) => $q->where('metode_bayar', $this->metode_bayar)),
            'dataPengguna' => auth()->user()->hasRole(['administrator', 'supervisor']) ? Pengguna::whereIn('id', $data->pluck('pengguna_id')->unique()->toArray())->get()->toArray() : Pengguna::where('id', auth()->id())->get()->toArray()
        ]);
    }
}
