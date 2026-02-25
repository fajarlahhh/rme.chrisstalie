<?php

namespace App\Livewire\Jurnalkeuangan;

use Livewire\Component;
use App\Models\KeuanganJurnal;
use App\Models\KodeAkun;
use App\Class\JurnalkeuanganClass;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;
use App\Traits\KodeakuntransaksiTrait;

class Hutang extends Component
{
    use CustomValidationTrait;
    use KodeakuntransaksiTrait;
    public KeuanganJurnal $data;
    public $tanggal, $uraian, $jenis_hutang_id, $kas_bank_id, $nilai;
    public  $dataJenisHutang = [], $dataKasBank = [];

    public function mount(KeuanganJurnal $data)
    {
        $this->data = $data;
        if ($this->data->exists) {
            $this->fill($this->data->toArray());
            $this->kas_bank_id = $this->data->keuanganJurnalDetail->firstWhere('kredit', '>', 0)->kode_akun_id;
            $this->jenis_hutang_id = $this->data->keuanganJurnalDetail->firstWhere('debet', '>', 0)->kode_akun_id;
            $this->nilai = $this->data->keuanganJurnalDetail->sum('kredit');
        }
        // $this->tanggal = date('Y-m-d');
        $this->dataJenisHutang = KodeAkun::detail()->whereIn('id', $this->getKodeAkunTransaksiByTransaksi(['Hutang Ke Pemegang Saham', 'Hutang Ke Bank'])->pluck('kode_akun_id'))->get()->toArray();
        $this->dataKasBank = KodeAkun::detail()->whereIn('id', $this->getKodeAkunTransaksiByTransaksi(['Pemasukan'])->pluck('kode_akun_id'))->orWhereIn(DB::raw('left(id, 1)'), ['6', '7'])->get()->toArray();
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'tanggal' => 'required|date',
            'uraian' => 'required|string|max:255',
            'jenis_hutang_id' => 'required|exists:kode_akun,id',
            'kas_bank_id' => 'required|exists:kode_akun,id',
            'nilai' => 'required|numeric|min:0',
        ]);
        if (JurnalkeuanganClass::tutupBuku(substr($this->tanggal, 0, 7) . '-01')) {
            session()->flash('danger', 'Pembukuan periode ini sudah ditutup');
            return;
        }
        DB::transaction(function () {
            JurnalkeuanganClass::insert(
                jenis: 'Hutang',
                sub_jenis: collect($this->dataJenisHutang)->firstWhere('id', $this->jenis_hutang_id)['nama'],
                tanggal: $this->tanggal,
                uraian: $this->uraian,
                system: 0,
                foreign_key: null,
                foreign_id: null,
                detail: [
                    [
                        'debet' => 0,
                        'kredit' => $this->nilai,
                        'kode_akun_id' => $this->jenis_hutang_id,
                    ],
                    [
                        'debet' => $this->nilai,
                        'kredit' => 0,
                        'kode_akun_id' => $this->kas_bank_id,
                    ],
                ]
            );
            session()->flash('success', 'Berhasil menyimpan data');
        });
        return redirect()->to('jurnalkeuangan');
    }

    public function render()
    {
        return view('livewire.jurnalkeuangan.hutang');
    }
}
