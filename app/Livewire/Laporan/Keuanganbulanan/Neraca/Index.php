<?php

namespace App\Livewire\Laporan\Keuanganbulanan\Neraca;

use Livewire\Component;
use App\Models\KeuanganTemplateLaporanKeuangan;
use App\Models\KeuanganSaldo;
use Livewire\Attributes\Url;

class Index extends Component
{
    #[Url]
    public $bulan;
    public $template;
    public $saldo;
    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m', strtotime('-1 month'));
        $this->getTemplate();
    }

    public function updatedBulan()
    {
        $this->getTemplate();
    }

    public function getTemplate()
    {
        $this->template = KeuanganTemplateLaporanKeuangan::where('jenis', 'Neraca')->orderBy('urutan')->get();
        $this->saldo = KeuanganSaldo::where('periode', date('Y-m-01', strtotime($this->bulan . '-01' . ' +1 month')))->get();
    }

    public function cetak()
    {
        $cetak = view('livewire.laporan.keuanganbulanan.neraca.cetak', [
            'cetak' => true,
            'dataAktiva' => $this->getDataAktiva(),
            'dataPasiva' => $this->getDataPasiva(),
            'bulan' => $this->bulan,
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function getDataAktiva()
    {
        $data = [];
        $detail = [];
        foreach ($this->template->where('kategori', 'Aktiva')->sortBy('urutan') as $item) {
            $nilai = '';
            if ($item['kode_akun']) {
                $debet = $this->saldo->whereIn('kode_akun_id', explode(';', $item['kode_akun']))->sum('debet');
                $kredit = $this->saldo->whereIn('kode_akun_id', explode(';', $item['kode_akun']))->sum('kredit');

                $nilai = $debet - $kredit;
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

    public function getDataPasiva()
    {
        $data = [];
        $detail = [];
        foreach ($this->template->where('kategori', '!=', 'Aktiva')->sortBy('urutan') as $item) {
            $nilai = '';
            if ($item['kode_akun']) {
                $debet = $this->saldo->whereIn('kode_akun_id', explode(';', $item['kode_akun']))->sum('debet');
                $kredit = $this->saldo->whereIn('kode_akun_id', explode(';', $item['kode_akun']))->sum('kredit');

                $nilai = $kredit - $debet;
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
        return view('livewire.laporan.keuanganbulanan.neraca.index', [
            'dataAktiva' => $this->getDataAktiva(),
            'dataPasiva' => $this->getDataPasiva(),
        ]);
    }
}
