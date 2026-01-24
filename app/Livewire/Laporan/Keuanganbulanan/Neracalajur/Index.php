<?php

namespace App\Livewire\Laporan\Keuanganbulanan\Neracalajur;

use App\Exports\LaporanneracalajurExport;
use App\Models\JurnalKeuanganDetail;
use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\KodeAkun;

class Index extends Component
{
    #[Url]
    public $bulan;

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
    }

    public function export()
    {
        return (new LaporanneracalajurExport($this->getData(), $this->bulan))->download('neracalajur' . $this->bulan . '.xls');
    }

    public function getData()
    {
        $data = [];
        $bulanIni = $this->bulan;
        $dataKodeAkun = KodeAkun::with([
            'kodeAkunNeraca' => function ($q) use ($bulanIni) {
                $q->where('periode', $bulanIni . '-01');
            }
        ])
            ->with(['jurnalKeuanganDetail' => function ($q) use ($bulanIni) {
                $q->whereHas('jurnalKeuangan', function ($q) use ($bulanIni) {
                    $q->where('tanggal', 'like', $bulanIni . '%');
                });
            }])->where('detail', 1)->get()->map(function ($q) {
                return [
                    'id' => $q->id,
                    'deskripsi' => $q->nama,
                    'kategori' => $q->kategori,
                    'laba_rugi' => $q->laba_rugi,
                    'saldo_debet' => $q->kodeAkunNeraca->sum('debet') ?? 0,
                    'saldo_kredit' => $q->kodeAkunNeraca->sum('kredit') ?? 0,
                    'jurnal_debet' => $q->jurnalKeuanganDetail->sum('debet') ?? 0,
                    'jurnal_kredit' => $q->jurnalKeuanganDetail->sum('kredit') ?? 0,
                ];
            });
        $labaRugi = $dataKodeAkun->filter(fn($q) => $q['kategori'] == 'Pendapatan')->sum(fn($q) => $q['jurnal_kredit'] - $q['jurnal_debet']) - $dataKodeAkun->filter(fn($q) => $q['kategori'] == 'Beban')->sum(fn($q) => $q['jurnal_debet'] - $q['jurnal_kredit']);

        $sumDebetLabaRugi = 0;
        $sumKreditLabaRugi = 0;
        $sumDebetSaldoAkhir = 0;
        $sumKreditSaldoAkhir = 0;

        foreach ($dataKodeAkun->toArray() as $key => $row) {
            $debetSaldo = $row['saldo_debet'];
            $kreditSaldo = $row['saldo_kredit'];
            $debetJurnal = $row['jurnal_debet'];
            $kreditJurnal = $row['jurnal_kredit'];

            $debetLabaRugi = 0;
            $kreditLabaRugi = 0;
            if ($row['laba_rugi'] == 1) {
                // if ($labaRugi >= 0) {
                //     $debetLabaRugi = $labaRugi;
                //     $kreditLabaRugi = 0;
                // } else {
                //     $debetLabaRugi = 0;
                //     $kreditLabaRugi = $labaRugi * -1;
                // }
            } else {
                $debetLabaRugi = ($row['kategori'] == 'Beban' ? $debetJurnal - $kreditJurnal : 0);
                $kreditLabaRugi = ($row['kategori'] == 'Pendapatan' ? $kreditJurnal - $debetJurnal : 0);
            }
            $sumDebetLabaRugi += $debetLabaRugi;
            $sumKreditLabaRugi += $kreditLabaRugi;

            $debetSaldoAkhir = 0;
            $kreditSaldoAkhir = 0;
            if ($row['kategori'] == 'Aktiva') {
                $debetSaldoAkhir = ($debetSaldo - $kreditSaldo) + ($debetJurnal - $kreditJurnal);
            } elseif ($row['kategori'] == 'Kewajiban' || $row['kategori'] == 'Ekuitas') {
                if ($row['laba_rugi'] == 1) {
                    $kreditSaldoAkhir = ($kreditSaldo - $debetSaldo) + ($kreditJurnal - $debetJurnal) + $labaRugi;
                } else {
                    $kreditSaldoAkhir = ($kreditSaldo - $debetSaldo) + ($kreditJurnal - $debetJurnal);
                }
            }
            $sumDebetSaldoAkhir += $debetSaldoAkhir;
            $sumKreditSaldoAkhir += $kreditSaldoAkhir;
            array_push($data, [
                'id' => $row['id'] . " - " . $row['deskripsi'],
                'saldo_debet' => $debetSaldo,
                'saldo_kredit' => $kreditSaldo,
                'jurnal_debet' => $debetJurnal,
                'jurnal_kredit' => $kreditJurnal,
                'laba_rugi_debet' => $debetLabaRugi,
                'laba_rugi_kredit' => $kreditLabaRugi,
                'necara_debet' => $debetSaldoAkhir,
                'necara_kredit' => $kreditSaldoAkhir
            ]);
        }

        return $data;
    }

    public function render()
    {
        return view('livewire.laporan.keuanganbulanan.neracalajur.index', [
            'data' => ($this->getData())
        ]);
    }
}
