<?php

namespace App\Livewire\Rekapitulasibulanan;

use Carbon\Carbon;
use App\Models\Aset;
use App\Models\Barang;
use Livewire\Component;
use App\Models\KodeAkun;
use App\Models\StokAwal;
use Livewire\Attributes\Url;
use App\Models\KeuanganSaldo;
use App\Models\AsetPenyusutan;
use App\Models\KeuanganJurnal;
use App\Class\JurnalkeuanganClass;
use Illuminate\Support\Facades\DB;
use App\Traits\KodeakuntransaksiTrait;

class Index extends Component
{
    use KodeakuntransaksiTrait;
    #[Url]
    public $bulan, $tutup_buku = 0;

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m', strtotime('-1 month'));
    }

    public function stok($periode)
    {
        $periodeSekarang = $periode->copy();
        $periodeSelanjutnya = $periodeSekarang->copy()->addMonth();
        $data = Barang::with(['stokAwal' => fn($q) => $q->where('tanggal', $periodeSekarang)])
            ->with(['stokMasuk' => fn($q) => $q->where('tanggal', 'like',  substr($periodeSekarang, 0, 7) . '%')])
            ->with(['stokKeluar' => fn($q) => $q->where('tanggal', 'like',  substr($periodeSekarang, 0, 7) . '%')])
            ->with('barangSatuanUtama')
            ->get();

        StokAwal::where('tanggal', $periodeSelanjutnya->format('Y-m-01'))->delete();
        StokAwal::insert($data->map(
            fn($row) =>
            [
                'barang_id' => $row->id,
                'tanggal' =>  $periodeSelanjutnya->format('Y-m-01'),
                'qty' => $row->stokAwal->sum('qty') +
                    $row->stokMasuk
                    ->map(
                        fn($q) => [
                            'qty' => $q->qty * $q->rasio_dari_terkecil,
                        ],
                    )
                    ->sum('qty') -
                    $row->stokKeluar
                    ->map(
                        fn($q) => [
                            'qty' => $q->qty * $q->rasio_dari_terkecil,
                        ],
                    )
                    ->sum('qty'),
                'pengguna_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        )->toArray());
    }

    public function keuangan($periode)
    {
        $periodeSekarang = $periode->copy();
        $periodeSelanjutnya = $periodeSekarang->copy()->addMonth();
        $saldo = [];

        KeuanganJurnal::where('tanggal', '>=', $periode->format('Y-m-01'))->update([
            'waktu_tutup_buku' => null,
        ]);
        KeuanganSaldo::where('periode', '>=', $periodeSelanjutnya->format('Y-m-01'))->delete();

        $dataAkun = KodeAkun::with([
            'keuanganSaldo' => fn($q) => $q->selectRaw("kode_akun_id, debet, kredit")
                ->where("periode", $periodeSekarang->format('Y-m-01'))
        ])
            ->with([
                'keuanganJurnalDetail' => fn($q) => $q->withoutGlobalScopes()->selectRaw("kode_akun_id, sum(debet) debet, sum(kredit) kredit")
                    ->whereHas(
                        'keuanganJurnal',
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
                fn($q) => ($q->keuanganJurnalDetail->sum('kredit')) - ($q->keuanganJurnalDetail->sum('debet'))
            ) - $dataAkun->filter(fn($q) => $q['kategori'] == 'Beban')->sum(
                fn($q) => ($q->keuanganJurnalDetail->sum('debet')) - ($q->keuanganJurnalDetail->sum('kredit'))
            );

            foreach ($dataAkun as $key => $row) {
                $debetJurnal = $row->keuanganJurnalDetail->sum('debet');
                $kreditJurnal = $row->keuanganJurnalDetail->sum('kredit');

                $saldoDebet = sizeof($row->keuanganSaldo) > 0 ? $row->keuanganSaldo->sum('debet') : 0;
                $saldoKredit = sizeof($row->keuanganSaldo) > 0 ? $row->keuanganSaldo->sum('kredit') : 0;

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
                    'tutup_buku' => $this->tutup_buku,
                    'pengguna_id' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        $ds = collect($saldo)->chunk(2000);
        foreach ($ds as $sal) {
            KeuanganSaldo::insert($sal->toArray());
        }
    }

    public function penyusutan($periode)
    {
        $periodeSekarang = $periode->format('Y-m-01');
        $dataAset = Aset::select('nilai_penyusutan', 'id', 'kode_akun_penyusutan_id')
            ->where('tanggal_perolehan', '<=', $periode->format('Y-m-t'))
            ->where('metode_penyusutan', 'Garis Lurus')
            ->where('tanggal_terminasi', '>=', $periode->format('Y-m-01'))
            ->get();
        $detail = $dataAset->groupBy('kode_akun_penyusutan_id')->map(function ($aset) {
            return [
                'debet' => 0,
                'kredit' => $aset->sum('nilai_penyusutan'),
                'kode_akun_id' => $aset->first()->kode_akun_penyusutan_id
            ];
        });
        $detail[] = [
            'debet' => collect($detail)->sum('kredit'),
            'kredit' => 0,
            'kode_akun_id' => $this->getKodeAkunTransaksiByTransaksi(['Biaya Penyusutan Aset'])->kode_akun_id
        ];

        KeuanganJurnal::where('jenis', 'Penyusutan')->where('tanggal', '>=', $periodeSekarang)->delete();

        $keuanganJurnal = JurnalkeuanganClass::insert(
            jenis: 'Penyusutan',
            sub_jenis: 'Penyusutan Aset',
            tanggal: $periodeSekarang,
            uraian: 'Penyusutan Aset',
            system: 1,
            foreign_key: null,
            foreign_id: null,
            detail: $detail
        );
        $asetPenyusutan = [];
        foreach ($dataAset as $aset) {
            $asetPenyusutan[] = [
                'aset_id' => $aset->id,
                'nilai' => $aset->nilai_penyusutan,
                'keuangan_jurnal_id' => $keuanganJurnal->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        AsetPenyusutan::insert($asetPenyusutan);
    }

    public function submit()
    {
        set_time_limit(0);
        $periode = Carbon::parse($this->bulan . '-01');
        // $diff = Carbon::parse($this->bulan . '-01')->diffInMonths(date('Y-m-01'));
        // if ($diff > 12) {
        //     $diff = 24;
        // }

        // for ($i = 0; $i < $diff; $i++) {
        DB::transaction(function () use ($periode) {
            $this->penyusutan($periode);
            $this->stok($periode);
            $this->keuangan($periode);
            if ($this->tutup_buku) {
                KeuanganJurnal::where('tanggal', 'like', $periode->format('Y-m') . '%')->update([
                    'waktu_tutup_buku' => now(),
                ]);
            }
        });
        // $periode->addMonth();
        // }
        session()->flash('success', 'Berhasil menyimpan data');
    }

    public function render()
    {
        return view('livewire.rekapitulasibulanan.index');
    }
}
