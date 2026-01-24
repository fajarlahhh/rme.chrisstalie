<?php

namespace App\Livewire\Rekapitulasibulanan;

use Carbon\Carbon;
use App\Models\Barang;
use App\Models\KodeAkun;
use App\Models\KodeAkunNeraca;
use App\Models\StokAwal;
use Livewire\Component;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    #[Url]
    public $bulan;

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m', strtotime('-1 month'));
    }

    public function submit()
    {
        DB::transaction(function () {
            $bulanSelanjutnya = Carbon::parse($this->bulan . '-01')->addMonths(1)->format('Y-m-01');

            $data = Barang::with(['stokAwal' => fn($q) => $q->where('tanggal', $this->bulan . '-01')])
                ->with(['stokMasuk' => fn($q) => $q->where('tanggal', 'like',  $this->bulan . '%')])
                ->with(['stokKeluar' => fn($q) => $q->where('tanggal', 'like',  $this->bulan . '%')])
                ->get();

            StokAwal::where('tanggal', $bulanSelanjutnya)->delete();
            StokAwal::insert($data->map(
                fn($q) =>
                [
                    'barang_id' => $q->id,
                    'tanggal' =>  $bulanSelanjutnya,
                    'qty' => $q->stokAwal->sum('qty') + $q->stokMasuk->sum(fn($q) => $q['qty'] * $q['rasio_dari_terkecil']) - $q->stokKeluar->sum(fn($q) => $q['qty'] * $q['rasio_dari_terkecil']),
                    'pengguna_id' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            )->toArray());



            $periode = Carbon::parse($this->bulan . '-01');
            $periodeSelanjutnya = Carbon::parse($this->bulan . '-01')->addMonths(1);
            $periodeSekarang = Carbon::now();

            $diff = $periode->diffInMonths($periodeSekarang);
            if ($diff > 12) {
                $diff = 24;
            }
            $now = now();
            for ($i = 0; $i < $diff; $i++) {
                $saldo = [];

                KodeAkunNeraca::where('periode', $periodeSelanjutnya->format('Y-m-01'))->delete();

                $dataAkun = KodeAkun::with([
                    'kodeAkunNeraca' => fn($q) => $q->selectRaw("kode_akun_id, debet, kredit")
                        ->where("periode", $periode->format('Y-m-01'))
                ])
                    ->with([
                        'jurnalKeuanganDetail' => fn($q) => $q->withoutGlobalScopes()->selectRaw("kode_akun_id, sum(debet) debet, sum(kredit) kredit")
                            ->whereHas(
                                'jurnalKeuangan',
                                fn($r) =>
                                $r->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$periode->format('Y-m')])
                            )
                            ->groupBy('kode_akun_id')
                    ])
                    ->detail()
                    ->orderBy('id', 'asc')
                    ->get();

                if ($dataAkun) {
                    $labaRugi = $dataAkun->filter(fn($q) => $q['kategori'] == 'Pendapatan')->sum(
                        fn($q) => ($q->jurnalKeuanganDetail->sum('kredit')) - ($q->jurnalKeuanganDetail->sum('debet'))
                    ) - $dataAkun->filter(fn($q) => $q['kategori'] == 'Beban')->sum(
                        fn($q) => ($q->jurnalKeuanganDetail->sum('debet')) - ($q->jurnalKeuanganDetail->sum('kredit'))
                    );

                    foreach ($dataAkun as $key => $row) {
                        $debetJurnal = $row->jurnalKeuanganDetail->sum('debet');
                        $kreditJurnal = $row->jurnalKeuanganDetail->sum('kredit');

                        $saldoDebet = sizeof($row->kodeAkunNeraca) > 0 ? $row->kodeAkunNeraca->sum('debet') : 0;
                        $saldoKredit = sizeof($row->kodeAkunNeraca) > 0 ? $row->kodeAkunNeraca->sum('kredit') : 0;

                        $debetNeraca = 0;
                        $kreditNeraca = 0;
                        if ($row->kategori == 'Aktiva') {
                            $debetNeraca = ($saldoDebet - $saldoKredit) + ($debetJurnal - $kreditJurnal);
                        } else if ($row->kategori == 'Kewajiban' || $row->kategori == 'Ekuitas') {
                            if ($row->laba_rugi == 1) {
                                $kreditNeraca = ($saldoKredit - $saldoDebet) + ($kreditJurnal - $debetJurnal) + $labaRugi;
                            } else {
                                $kreditNeraca = ($saldoKredit - $saldoDebet) + ($kreditJurnal - $debetJurnal);
                            }
                        }

                        array_push($saldo, [
                            'id' => str_replace('.', '', $row->id) . $periodeSelanjutnya->format('Ym01') . '1',
                            'periode' => $periodeSelanjutnya->format('Y-m-01'),
                            'kode_akun_id' => $row->id,
                            'debet_jurnal' => $debetJurnal,
                            'kredit_jurnal' => $kreditJurnal,
                            'kredit' => $kreditNeraca,
                            'debet' => $debetNeraca,
                            'pengguna_id' => auth()->id(),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
                $ds = collect($saldo)->chunk(2000);
                foreach ($ds as $sal) {
                    KodeAkunNeraca::insert($sal->toArray());
                }

                $periode->addMonths(1);
                $periodeSelanjutnya->addMonths(1);
            }
            session()->flash('success', 'Berhasil menyimpan data');
        });
    }

    public function render()
    {
        return view('livewire.rekapitulasibulanan.index');
    }
}
