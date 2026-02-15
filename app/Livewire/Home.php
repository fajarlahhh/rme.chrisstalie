<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\KeuanganJurnal;
use App\Models\KepegawaianAbsensi;
use Livewire\Component;
use App\Models\KodeAkun;
use App\Models\PengadaanPemesanan;
use App\Models\Pembayaran;
use App\Models\KeuanganJurnalDetail;
use Livewire\Attributes\Url;

class Home extends Component
{
    #[Url]
    public $bulanShift;

    public function mount()
    {
        $this->bulanShift = $this->bulanShift ?: date('Y-m');
    }

    public function getDataJadwalShiftPegawai()
    {
        $detail = [];
        $kepegawaianAbsensi = KepegawaianAbsensi::where('kepegawaian_pegawai_id', auth()->user()->kepegawaian_pegawai_id)->where('tanggal', 'like', $this->bulanShift . '%')->get();
        if (!$this->bulanShift || !preg_match('/^\d{4}-\d{2}$/', $this->bulanShift)) {
            $this->bulanShift = date('Y-m');
        }

        $startDate = Carbon::createFromFormat('Y-m-d', $this->bulanShift . '-01');
        $daysInMonth = $startDate->daysInMonth;

        for ($i = 0; $i < $daysInMonth; $i++) {
            $tanggal = $startDate->copy()->addDays($i)->format('Y-m-d');

            $data = $kepegawaianAbsensi->firstWhere('tanggal', $tanggal);
            $detail[] = $data ? [
                'jam_masuk' => $data->jam_masuk,
                'jam_pulang' => $data->jam_pulang,
                'tanggal' => $data->tanggal,
                'shift_id' => $data->shift_id,
                'masuk' => $data->masuk,
                'pulang' => $data->pulang,
                'absen' => $data->shift_id ? true : false,
            ] : [
                'jam_masuk' => null,
                'jam_pulang' => null,
                'tanggal' => $tanggal,
                'shift_id' => null,
                'masuk' => null,
                'pulang' => null,
                'absen' => false,
            ];
        }
        return $detail;
    }
    public function getDataPembayaranBulanIni()
    {
        return Pembayaran::where('tanggal', 'like', date('Y-m') . '%')->get();
    }

    public function getDataPengadaanBarangJatuhTempo()
    {
        return PengadaanPemesanan::with('supplier', 'pengadaanPemesananDetail')
            ->whereNotNull('jatuh_tempo')
            ->where(fn($q) => $q->where('jatuh_tempo', '<=', date('Y-m-d', strtotime('+2 days')))
                ->whereDoesntHave('pengadaanPelunasanPemesanan'))
            ->get();
    }

    public function getDataPengeluaranBulanIni()
    {
        return KeuanganJurnal::with('keuanganJurnalDetail.kodeAkun', 'pengguna')
            ->whereHas('keuanganJurnalDetail', function ($query) {
                $query->whereIn('kode_akun_id', KodeAkun::where('parent_id', '11100')->get()->pluck('id'));
            })
            ->whereIn('jenis', ['Pembelian', 'Pengeluaran'])
            ->where('tanggal', 'like', date('Y-m') . '%')->get();
    }

    public function render()
    {
        return view(
            'livewire.home',
            [
                'dataJadwalShiftPegawai' => auth()->user()->kepegawaian_pegawai_id ? $this->getDataJadwalShiftPegawai() : [],
                'dataPembayaranBulanIni' => $this->getDataPembayaranBulanIni(),
                'dataPengeluaranBulanIni' => $this->getDataPengeluaranBulanIni(),
                'dataPengadaanBarangJatuhTempo' => $this->getDataPengadaanBarangJatuhTempo(),
            ]
        );
    }
}
