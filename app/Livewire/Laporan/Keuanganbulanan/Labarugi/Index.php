<?php

namespace App\Livewire\Laporan\Keuanganbulanan\Labarugi;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Exports\LaporankeuanganExport;
use App\Models\KeuanganLaporanBulanan;
use App\Models\KeuanganSaldo;
use App\Models\KeuanganTemplateLaporanKeuangan;

class Index extends Component
{
    #[Url]
    public $bulan;

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m', strtotime('-1 month'));
    }

    public function cetak()
    {
        $cetak = view('livewire.laporan.keuanganbulanan.labarugi.cetak', [
            'cetak' => true,
            'data' => $this->getData(),
            'bulan' => $this->bulan,
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function getData()
    {
        // $data = KeuanganLaporanBulanan::where('Laba Rugi')->where('periode', date('Y-m-01', strtotime($this->bulan . '-01' . ' +1 month')))->get();
        $saldo = KeuanganSaldo::where('periode', date('Y-m-01', strtotime($this->bulan . '-01' . ' +1 month')))->get();

        $data = [];
        $detail = [];
        $template = KeuanganTemplateLaporanKeuangan::where('jenis', 'Laba Rugi')->orderBy('urutan')->get();
        foreach ($template as $item) {
            $nilai = '';
            if ($item['kode_akun']) {
                $debet = $saldo->whereIn('kode_akun_id', explode(';', $item['kode_akun']))->sum('debet_jurnal');
                $kredit = $saldo->whereIn('kode_akun_id', explode(';', $item['kode_akun']))->sum('kredit_jurnal');

                $nilai = $item['kategori'] == 'Pendapatan' ? $kredit - $debet : $debet - $kredit;
                $detail[] = [
                    'key' => $item['urutan'],
                    'nilai' => $nilai,
                ];
            }

            if ($item['rumus']) {
                if (preg_match('/sum\((\d+):(\d+)\)/', $item['rumus'], $matches)) {
                    $start = intval($matches[1]);
                    $end = intval($matches[2]);
                    $nilai = array_sum(
                        array_column(
                            array_filter($detail, function ($det) use ($start, $end) {
                                return $det['key'] >= $start && $det['key'] <= $end;
                            }),
                            'nilai'
                        )
                    );
                }
                // Cek jika rumus memiliki format 'sum(x:y) - sum(a:b)'
                // Parsing rumus yang lebih dinamis: bisa support operasi penjumlahan dan pengurangan bertingkat pada rumus, contoh: sum(62:63) - sum(65:66) + sum(30:57)
                if (preg_match_all('/([+-]?)\s*sum\((\d+):(\d+)\)/', $item['rumus'], $matches, PREG_SET_ORDER)) {
                    $nilai = 0;
                    foreach ($matches as $match) {
                        $operator = $match[1] ?: '+';
                        $start = intval($match[2]);
                        $end = intval($match[3]);

                        $sum = array_sum(
                            array_column(
                                array_filter($detail, function ($det) use ($start, $end) {
                                    return $det['key'] >= $start && $det['key'] <= $end;
                                }),
                                'nilai'
                            )
                        );

                        if ($operator === '-') {
                            $nilai -= $sum;
                        } else {
                            $nilai += $sum;
                        }
                    }
                }
            }

            $data[] = [
                'nomor' => $item->nomor,
                'kode_akun' => $item->kode_akun,
                'uraian' => $item->uraian,
                'nilai' => $nilai == '' ? '' : number_format($nilai, 2),
            ];
        }
        return $data;
    }

    public function render()
    {
        return view(
            'livewire.laporan.keuanganbulanan.labarugi.index',
            [
                'data' => $this->getData()
            ]
        );
    }
}
