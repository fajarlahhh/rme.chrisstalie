<?php

namespace App\Livewire\Laporan\Pengeluaran;

use App\Models\JurnalKeuangan;
use Livewire\Component;
use App\Models\KodeAkun;
use App\Models\Pengguna;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanpengeluaranExport;

class Index extends Component
{
    #[Url]
    public $tanggal1, $tanggal2, $pengguna_id, $metode_bayar;

    public $dataKodeAkun = [];

    public function mount()
    {
        $this->tanggal1 = $this->tanggal1 ?: date('Y-m-d');
        $this->tanggal2 = $this->tanggal2 ?: date('Y-m-d');
        $this->dataKodeAkun = KodeAkun::where('parent_id', '11100')->get()->toArray();
    }

    public function export()
    {
        return Excel::download(new LaporanpengeluaranExport(
            $this->getData(false),
            $this->tanggal1,
            $this->tanggal2,
            Pengguna::find($this->pengguna_id)?->nama,
            collect($this->dataKodeAkun)->where('id', $this->metode_bayar)->first()?->nama,
            $this->dataKodeAkun
        ), 'pengeluaran.xlsx');
    }

    public function getData()
    {
        $query = JurnalKeuangan::with('jurnalDetail.kodeAkun', 'pengguna.pegawai')
            ->whereHas('jurnalDetail', function ($query) {
                $query->whereIn('kode_akun_id', collect($this->dataKodeAkun)->pluck('id'));
            })
            ->whereIn('jenis', ['Pembelian', 'Pengeluaran'])
            ->whereBetween('tanggal', [$this->tanggal1, $this->tanggal2]);

        if (!auth()->user()->hasRole(['administrator', 'supervisor'])) {
            $query->where('pengguna_id', auth()->id());
        }

        return $query->get();
    }

    public function render()
    {
        $data = $this->getData(true);
        return view('livewire.laporan.pengeluaran.index', [
            'data' => collect($data)
                ->when($this->pengguna_id, function ($collection) {
                    return $collection->where('pengguna_id', $this->pengguna_id);
                })
                ->when($this->metode_bayar, function ($collection) {
                    return $collection->filter(function ($item) {
                        return $item->jurnalDetail->contains(function ($detail) {
                            // $this->metode_bayar may be string or array, handle both
                            if (is_array($this->metode_bayar)) {
                                return in_array($detail->kode_akun_id, $this->metode_bayar);
                            }
                            return $detail->kode_akun_id == $this->metode_bayar;
                        });
                    });
                }),
            'dataPengguna' => auth()->user()->hasRole(['administrator', 'supervisor']) ?
                Pengguna::whereIn('id', $data->pluck('pengguna_id')->unique()->toArray())->with('pegawai')->get()->toArray() :
                Pengguna::where('id', auth()->id())->with('pegawai')->get()->toArray()
        ]);
    }
}
