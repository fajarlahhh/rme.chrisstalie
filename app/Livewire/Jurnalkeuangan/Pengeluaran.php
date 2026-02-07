<?php

namespace App\Livewire\Jurnalkeuangan;

use App\Models\KeuanganJurnal;
use Livewire\Component;
use App\Models\KodeAkun;
use App\Class\JurnalkeuanganClass;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Pengeluaran extends Component
{
    use CustomValidationTrait;
    public KeuanganJurnal $data;
    public $tanggal, $uraian, $jenis_pengeluaran_id, $sumber_dana_id, $nilai;
    public  $dataJenisPengeluaran = [], $dataSumberDana = [];

    public function mount(KeuanganJurnal $data)
    {
        $this->data = $data;
        if ($this->data->exists) {
            $this->fill($this->data->toArray());
            $this->sumber_dana_id = $this->data->keuanganJurnalDetail->firstWhere('kredit', '>', 0)->kode_akun_id;
            $this->jenis_pengeluaran_id = $this->data->keuanganJurnalDetail->firstWhere('debet', '>', 0)->kode_akun_id;
            $this->nilai = $this->data->keuanganJurnalDetail->sum('kredit');
        }
        // $this->tanggal = date('Y-m-d');
        $this->dataJenisPengeluaran = KodeAkun::detail()->where('id', '!=', '21110')->whereIn('kategori', ['Beban'])->get()->toArray();
        $this->dataSumberDana = KodeAkun::detail()->whereIn('parent_id', ['11100', '21200'])->get()->toArray();
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'tanggal' => 'required|date',
            'uraian' => 'required|string|max:255',
            'jenis_pengeluaran_id' => 'required|exists:kode_akun,id',
            'sumber_dana_id' => 'required|exists:kode_akun,id',
            'nilai' => 'required|numeric|min:0',
        ]);
        if (JurnalkeuanganClass::tutupBuku(substr($this->tanggal, 0, 7) . '-01')) {
            session()->flash('danger', 'Pembukuan periode ini sudah ditutup');
            return;
        }
        DB::transaction(function () {
            JurnalkeuanganClass::insert(
                jenis: 'Pengeluaran',
                sub_jenis: collect($this->dataJenisPengeluaran)->firstWhere('id', $this->jenis_pengeluaran_id)['nama'],
                tanggal: $this->tanggal,
                uraian: $this->uraian,
                system: 0,
                foreign_key: null,
                foreign_id: null,
                detail: [
                    [
                        'debet' => $this->nilai,
                        'kredit' => 0,
                        'kode_akun_id' => $this->jenis_pengeluaran_id,
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
        return view('livewire.jurnalkeuangan.pengeluaran');
    }
}
