<?php

namespace App\Livewire\Laporan\Laporanhariankas;

use App\Models\Sale;
use App\Models\Kasir;
use App\Models\JurnalKeuangan;
use Livewire\Component;
use App\Models\KodeAkun;
use App\Models\Pengguna;
use App\Models\Pembayaran;
use App\Models\Expenditure;
use App\Models\MetodeBayar;
use App\Models\JurnalKeuanganDetail;
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
        $cetak = view('livewire.laporan.laporanhariankas.cetak', [
            'cetak' => true,
            'tanggal' => $this->tanggal,
            'dataMetodeBayar' => $this->dataMetodeBayar,
            'dataKodeAkun' => $this->dataKodeAkun,
            'pengguna' => $this->pengguna_id ? Pengguna::find($this->pengguna_id)?->pegawai?->nama ?? Pengguna::find($this->pengguna_id)?->nama : 'Semua Pengguna',
            'data' => $this->getData(),
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function getPendapatan()
    {
        return Pembayaran::with(['pengguna'])
            ->where('tanggal', 'like', $this->tanggal . '%')->get();
    }

    public function getPengeluaran()
    {
        return JurnalKeuangan::leftJoin('jurnal_keuangan_detail', 'jurnalKeuangan.id', '=', 'jurnal_keuangan_detail.jurnal_keuangan_id')
        ->leftJoin('kode_akun', 'jurnal_keuangan_detail.kode_akun_id', '=', 'kode_akun.id')->select(
            'jurnalKeuangan.*',
            'jurnal_keuangan_detail.kode_akun_id as kode_akun_id',
            'jurnal_keuangan_detail.debet as debet',
            'jurnal_keuangan_detail.kredit as kredit',
            'kode_akun.nama as kode_akun_nama'
        )->with(['pengguna'])->where('sub_jenis', 'Pengeluaran')
            ->where('tanggal', $this->tanggal)->get();
    }

    public function render()
    {
        $dataPendapatan = $this->getPendapatan();
        $dataPengeluaran = $this->getPengeluaran();
        return view('livewire.laporan.laporanhariankas.index', [
            'dataPendapatan' =>  $this->getPendapatan()->when($this->pengguna_id, fn($q) => $q->where('pengguna_id', $this->pengguna_id)),
            'dataPengeluaran' =>  $this->getPengeluaran()->when($this->pengguna_id, fn($q) => $q->where('pengguna_id', $this->pengguna_id)),
            'dataPengguna' => Pengguna::whereIn('id', (array_merge($dataPendapatan->pluck('pengguna_id')->unique()->toArray(), $dataPengeluaran->pluck('pengguna_id')->unique()->toArray())))->get()
        ]);
    }
}
