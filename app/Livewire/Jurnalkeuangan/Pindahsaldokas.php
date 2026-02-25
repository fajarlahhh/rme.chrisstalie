<?php

namespace App\Livewire\Jurnalkeuangan;

use Livewire\Component;
use App\Class\JurnalkeuanganClass;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;
use App\Models\KeuanganJurnal;
use App\Models\KodeAkun;
use App\Traits\KodeakuntransaksiTrait;

class Pindahsaldokas extends Component
{
    use CustomValidationTrait;
    use KodeakuntransaksiTrait;
    public KeuanganJurnal $data;
    public $tanggal, $uraian, $sumber_dana_id, $tujuan_dana_id, $nilai;
    public  $dataKodeAkun = [];

    public function mount(KeuanganJurnal $data)
    {
        $this->data = $data;
        if ($this->data->exists) {
            $this->fill($this->data->toArray());
            $this->sumber_dana_id = $this->data->keuanganJurnalDetail->firstWhere('kredit', '>', 0)->kode_akun_id;
            $this->tujuan_dana_id = $this->data->keuanganJurnalDetail->firstWhere('debet', '>', 0)->kode_akun_id;
            $this->nilai = $this->data->keuanganJurnalDetail->sum('kredit');
        }
        // $this->tanggal = date('Y-m-d');
        $this->dataKodeAkun = KodeAkun::detail()->whereIn('id', $this->getKodeAkunTransaksiByKategori(['Kas'])->pluck('kode_akun_id'))->get()->toArray();
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'tanggal' => 'required|date',
            'uraian' => 'required|string|max:255',
            'tujuan_dana_id' => 'required|exists:kode_akun,id',
            'sumber_dana_id' => 'required|exists:kode_akun,id',
            'nilai' => 'required|numeric|min:0',
        ]);
        if (JurnalkeuanganClass::tutupBuku(substr($this->tanggal, 0, 7) . '-01')) {
            session()->flash('danger', 'Pembukuan periode ini sudah ditutup');
            return;
        }
        DB::transaction(function () {
            JurnalkeuanganClass::insert(
                jenis: 'Koreksi',
                sub_jenis: 'Pindah Saldo Kas',
                tanggal: $this->tanggal,
                uraian: $this->uraian,
                system: 0,
                foreign_key: null,
                foreign_id: null,
                detail: [
                    [
                        'debet' => $this->nilai,
                        'kredit' => 0,
                        'kode_akun_id' => $this->tujuan_dana_id,
                    ],
                    [
                        'debet' => 0,
                        'kredit' => $this->nilai,
                        'kode_akun_id' => $this->sumber_dana_id,
                    ],
                ]
            );
            session()->flash('success', 'Berhasil menyimpan data');
        });
        return redirect()->to('jurnalkeuangan');
    }

    public function render()
    {
        return view('livewire.jurnalkeuangan.pindahsaldokas');
    }
}
