<?php

namespace App\Livewire\Jurnalkeuangan;

use App\Models\JurnalKeuangan;
use Livewire\Component;
use App\Models\KodeAkun;
use App\Class\JurnalkeuanganClass;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Jurnalumum extends Component
{
    use CustomValidationTrait;
    public JurnalKeuangan $data;
    public $tanggal, $uraian, $kode_akun_id, $nilai, $jenis;
    public  $dataJenis = ['Barang Masuk', 'Hutang', 'Koreksi', 'Modal', 'Pembelian', 'Pendapatan', 'Pengeluaran', 'Penyusutan', 'Piutang'], $dataKodeAkun = [], $detail = [];

    public function mount(JurnalKeuangan $data)
    {
        $this->data = $data;
        $this->tanggal = date('Y-m-d');
        $this->dataKodeAkun = KodeAkun::detail()->with('parent')->get()->toArray();
        $this->fill($this->data->toArray());
        $this->detail = $this->data->jurnalKeuanganDetail->map(fn($q) => [
            'id' => $q->kode_akun_id,
            'debet' => $q->debet,
            'kredit' => $q->kredit,
        ])->toArray();
    }

    public function tambahDetail()
    {
        $this->detail[] = [
            'kode_akun_id' => null,
            'debet' => 0,
            'kredit' => 0,
            'nilai' => 'dk',
            'urutan' => null,
        ];
    }

    public function hapusDetail($index)
    {
        unset($this->detail[$index]);
        $this->detail = array_values($this->detail);
    }

    public function submit()
    {
        $this->validate(
            [
                'tanggal' => 'required',
                'uraian' => 'required',
                'detail' => 'required|array|min:2',
                'detail.*.id' => 'required',
                'detail.*.debet' => 'required',
                'detail.*.kredit' => 'required'
            ]
        );

        if (collect($this->detail)->sum('kredit') != collect($this->detail)->sum('debet')) {
            $this->addError('detail', 'Kredit dan Debet tidak seimbang');
            return $this->render();
        }

        if (
            number_format(collect($this->detail)->sum(fn($q) => str_replace(',', '', $q['kredit'])), 2)
            != number_format(collect($this->detail)->sum(fn($q) => str_replace(',', '', $q['debet'])), 2)
        ) {
            $this->addError('detail', 'Debet and Kredit is unbalance');
            return $this->render();
        }

        DB::transaction(function () {
            if (!$this->data->exists) {
                $nomor = JurnalkeuanganClass::getNomor($this->tanggal);
                $this->data->id = str_replace('/', '', substr($nomor, 6, 14));
                $this->data->nomor = $nomor;
            }

            $this->data->jenis = $this->jenis;
            $this->data->sub_jenis = 'JurnalKeuangan Umum';
            $this->data->uraian = ucfirst($this->uraian);
            $this->data->tanggal = $this->tanggal;
            $this->data->system = 0;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();

            $this->data->jurnalKeuanganDetail()->delete();
            $this->data->jurnalKeuanganDetail()->insert(collect($this->detail)->map(fn($q) => [
                'jurnal_keuangan_id' => $this->data->id,
                'debet' => $q['debet'],
                'kredit' => $q['kredit'],
                'kode_akun_id' => $q['id'],
            ])->toArray());
            session()->flash('success', 'Berhasil menyimpan data');
        });
        return $this->redirect('/jurnalkeuangan');
    }

    public function render()
    {
        return view('livewire.jurnalkeuangan.jurnalumum');
    }
}
