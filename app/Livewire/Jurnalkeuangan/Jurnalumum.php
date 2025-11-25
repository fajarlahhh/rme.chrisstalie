<?php

namespace App\Livewire\Jurnalkeuangan;

use App\Models\Jurnal;
use Livewire\Component;
use App\Models\KodeAkun;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Jurnalumum extends Component
{
    use CustomValidationTrait;
    public Jurnal $data;
    public $tanggal, $uraian, $kode_akun_id, $nilai;
    public  $dataKodeAkun = [], $detail = [];

    public function mount(Jurnal $data)
    {
        $this->data = $data;
        $this->tanggal = date('Y-m-d');
        $this->dataKodeAkun = KodeAkun::detail()->with('parent')->get()->toArray();
        $this->fill($this->data->toArray());
        $this->detail = $this->data->jurnalDetail->map(fn($q) => [
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
                'detail.*.id' => 'required|distinct',
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

                $terakhir = Jurnal::where('tanggal', 'like', substr($this->tanggal, 0, 7) . '%')
                    ->orderBy('id', 'desc')
                    ->first();
                $nomorTerakhir = $terakhir ? (int)substr($terakhir->id, 15, 5) : 0;
                $nomor = 'JURNAL/' . str_replace('-', '/', substr($this->tanggal, 0, 7)) . '/' . sprintf('%05d', $nomorTerakhir + 1);
                $this->data->id = str_replace('/', '', $nomor);
                $this->data->nomor = $nomor;
            }

            $this->data->jenis = 'Jurnal Umum';
            $this->data->sub_jenis = null;
            $this->data->uraian = ucfirst($this->uraian);
            $this->data->tanggal = $this->tanggal;
            $this->data->system = 0;
            $this->data->save();

            $this->data->jurnalDetail()->delete();
            $this->data->jurnalDetail()->insert(collect($this->detail)->map(fn($q) => [
                'jurnal_id' => $this->data->id,
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
