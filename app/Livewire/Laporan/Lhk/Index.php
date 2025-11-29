<?php

namespace App\Livewire\Laporan\Lhk;

use App\Models\Sale;
use App\Models\Kasir;
use App\Models\Jurnal;
use Livewire\Component;
use App\Models\Pengguna;
use App\Models\Expenditure;
use App\Models\JurnalDetail;
use App\Models\KodeAkun;
use App\Models\MetodeBayar;
use Livewire\Attributes\Url;

class Index extends Component
{
    #[Url]
    public $tanggal, $pengguna_id;

    public $dataKodeAkun = [], $dataMetodeBayar = [];

    public function mount()
    {
        $this->tanggal = $this->tanggal ?: date('Y-m-d');
        $this->dataKodeAkun = KodeAkun::all()->toArray();
        $this->dataMetodeBayar = MetodeBayar::all()->toArray();
    }

    public function print()
    {
        $cetak = view('livewire.laporan.lhk.cetak', [
            'cetak' => true,
            'tanggal' => $this->tanggal,
            'dataMetodeBayar' => $this->dataMetodeBayar,
            'dataKodeAkun' => $this->dataKodeAkun,
            'pengguna' => $this->pengguna_id ? Pengguna::find($this->pengguna_id)?->pegawai?->nama ?? Pengguna::find($this->pengguna_id)?->nama : 'Semua Pengguna',
            'data' => $this->getData(),
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function getData()
    {
        return Jurnal::with(['pengguna'])
            ->select('jurnal.*', 'jurnal_detail.kode_akun_id as kode_akun_id', 'jurnal_detail.debet as debet', 'jurnal_detail.kredit as kredit', 'pembayaran.metode_bayar as metode_bayar', 'jurnal.pengguna_id as pengguna_id')
            ->rightJoin('jurnal_detail', 'jurnal.id', '=', 'jurnal_detail.jurnal_id')
            ->leftJoin('pembayaran', 'jurnal.pembayaran_id', '=', 'pembayaran.id')
            ->whereIn('jurnal.id', JurnalDetail::whereIn('kode_akun_id', (collect($this->dataKodeAkun)->whereIn('parent_id', ['43000', '42000', '41000'])->pluck('id')))->pluck('jurnal_id'))
            ->where('tanggal', $this->tanggal)->get();
    }

    public function render()
    {
        $data = $this->getData();
        return view('livewire.laporan.lhk.index', [
            'data' =>  $data->when($this->pengguna_id, fn($q) => $q->where('pengguna_id', $this->pengguna_id)),
            'dataPengguna' => Pengguna::whereIn('id', $data->pluck('pengguna_id'))->get()
        ]);
    }
}
