<?php

namespace App\Livewire\Laporan\Jasadokter;

use App\Exports\LaporanjasanpelayananExport;
use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Tindakan;
use Illuminate\Support\Facades\DB;

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
        return (new LaporanjasanpelayananExport($this->getData(), $this->tanggal1, $this->tanggal2, 'dokter'))->download('pembagianjasadokter' . $this->tanggal1 . '-' . $this->tanggal2 . '.xls');
    }

    public function getData()
    {
        return Tindakan::with('registrasi.pasien', 'pembayaran', 'tarifTindakan', 'dokter')
            ->where('biaya_jasa_dokter', '>', 0)->whereNotNull('dokter_id')
            ->whereHas('pembayaran', fn($r) => $r
                ->whereBetween(DB::raw('DATE(created_at)'), [$this->tanggal1, $this->tanggal2]))
            ->get()->map(fn($row) => [
                'perawat_id' => $row->dokter_id,
                'no_nota' => $row->pembayaran->id,
                'tanggal' => substr($row->pembayaran->created_at, 0, 10),
                'nama_pasien' => $row->registrasi->pasien->nama,
                'nama_tindakan' => $row->tarifTindakan->nama,
                'nama_petugas' => $row->dokter?->nama,
                'biaya' => $row->biaya_jasa_dokter,
            ])->toArray();
    }

    public function render()
    {
        return view('livewire.laporan.jasadokter.index', [
            'data' => $this->getData(),
        ]);
    }
}
