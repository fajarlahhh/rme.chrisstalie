<?php

namespace App\Livewire\Laporan\Jasaperawat;

use Livewire\Component;
use App\Models\Tindakan;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\DB;
use App\Exports\LaporanjasanpelayananExport;

class Index extends Component
{
    #[Url]
    public $tanggal1, $tanggal2;

    public function mount()
    {
        $this->tanggal1 = $this->tanggal1 ?: date('Y-m-01');
        $this->tanggal2 = $this->tanggal2 ?: date('Y-m-d');
    }

    public function export()
    {
        return (new LaporanjasanpelayananExport($this->getData(), $this->tanggal1, $this->tanggal2, 'perawat'))->download('pembagianjasaperawat' . $this->tanggal1 . '-' . $this->tanggal2 . '.xls');
    }

    public function getData()
    {
        return Tindakan::with('registrasi.pasien', 'pembayaran', 'perawat', 'tarifTindakan', 'perawat.pegawai')
            ->where('biaya_jasa_perawat', '>', 0)
            ->whereHas('pembayaran', fn($r) => $r
                ->whereBetween(DB::raw('DATE(created_at)'), [$this->tanggal1, $this->tanggal2]))
            ->get()->map(fn($row) => [
                'perawat_id' => $row->perawat_id,
                'no_nota' => $row->pembayaran->id,
                'tanggal' => substr($row->pembayaran->created_at, 0, 10),
                'nama_pasien' => $row->registrasi->pasien->nama,
                'nama_tindakan' => $row->tarifTindakan->nama,
                'nama_petugas' => $row->perawat?->nama,
                'biaya' => $row->biaya_jasa_perawat,
            ])->toArray();
    }

    public function render()
    {
        return view('livewire.laporan.jasaperawat.index', [
            'data' => $this->getData(),
        ]);
    }
}
