<?php

namespace App\Livewire\Jurnalkeuangan;

use Livewire\Component;
use App\Class\JurnalClass;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;
use App\Models\Jurnal;
use App\Models\KodeAkun;

class Pindahsaldokas extends Component
{
    use CustomValidationTrait;
    public Jurnal $data;
    public $tanggal, $uraian, $sumber_dana_id, $tujuan_dana_id, $nilai;
    public  $dataKodeAkun = [];

    public function mount(Jurnal $data)
    {
        $this->data = $data;
        if ($this->data->exists) {
            $this->fill($this->data->toArray());
            $this->sumber_dana_id = $this->data->jurnalDetail->firstWhere('kredit', '>', 0)->kode_akun_id;
            $this->tujuan_dana_id = $this->data->jurnalDetail->firstWhere('debet', '>', 0)->kode_akun_id;
            $this->nilai = $this->data->jurnalDetail->sum('kredit');
        }
        $this->tanggal = date('Y-m-d');
        $this->dataKodeAkun = KodeAkun::detail()->whereIn('parent_id', ['11100'])->get()->toArray();
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
        DB::transaction(function () {
            JurnalClass::insert(
                jenis: 'Koreksi',
                sub_jenis: 'Pindah Saldo Kas',
                tanggal: $this->tanggal,
                uraian: $this->uraian,
                system: 0,
                aset_id: null,
                pemesanan_pengadaan_id: null,
                stok_masuk_id: null,
                pembayaran_id: null,
                penggajian_id: null,
                pelunasan_pemesanan_pengadaan_id: null,
                stok_keluar_id: null,
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
