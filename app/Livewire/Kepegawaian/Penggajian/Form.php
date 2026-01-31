<?php

namespace App\Livewire\Kepegawaian\Penggajian;

use App\Models\KeuanganJurnal;
use App\Models\KepegawaianPegawai;
use Livewire\Component;
use App\Models\KodeAkun;
use App\Class\JurnalkeuanganClass;
use App\Models\KepegawaianPegawaiUnsurGaji;
use App\Models\KepegawaianPenggajian;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $dataPegawai = [], $dataUnsurGaji = [], $dataKodeAkun = [], $metode_bayar;
    public $tanggal, $periode, $detail = [], $pegawai_id;

    public function mount()
    {
        $this->dataKodeAkun = KodeAkun::detail()->get()->toArray();
        $this->tanggal = date('Y-m-01');
        $this->periode = date('Y-m');
        $this->updatedPeriode($this->periode);
    }

    public function updatedPegawaiId($value)
    {
        $this->detail = [];
        $this->detail = collect(collect($this->dataPegawai)->where('id', $value)->first()['kepegawaian_pegawai_unsur_gaji'])->map(fn($q) => [
            'kode_akun_id' => $q['kode_akun_id'],
            'kode_akun_nama' => $q['kode_akun']['nama'],
            'debet' => $q['nilai'],
            'kredit' => 0,
        ])->toArray();
        $this->detail[] = [
            'kode_akun_id' => null,
            'kode_akun_nama' => null,
            'debet' => 0,
            'kredit' => 0,
        ];
    }

    public function updatedPeriode($value)
    {
        $this->detail = [];
        $this->dataPegawai = KepegawaianPegawai::with('kepegawaianPegawaiUnsurGaji.kodeAkun')->whereNotIn('id', KepegawaianPenggajian::where('periode', $value . '-01')->get()->pluck('kepegawaian_pegawai_id'))->aktif()->get()->toArray();
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'periode' => 'required',
            'tanggal' => 'required',
            'pegawai_id' => 'required',
        ]);

        DB::transaction(function () {
            $penggajian = new KepegawaianPenggajian();
            $penggajian->tanggal = $this->tanggal;
            $penggajian->periode = $this->periode . '-01';
            $penggajian->detail = $this->detail;
            $penggajian->kepegawaian_pegawai_id = $this->pegawai_id;
            $penggajian->kode_akun_pembayaran_id = $this->metode_bayar;
            $penggajian->pengguna_id = auth()->id();
            $penggajian->save();
            $this->detail[] = [
                'kode_akun_id' => $this->metode_bayar,
                'kode_akun_nama' => null,
                'debet' => 0,
                'kredit' => collect($this->detail)->sum('debet'),
            ];

            JurnalkeuanganClass::insert(
                jenis: 'Gaji',
                sub_jenis: 'Pengeluaran',
                tanggal: $this->tanggal,
                uraian: 'Gaji Bulan ' . $this->periode,
                system: 1,
                foreign_key: 'penggajian_id',
                foreign_id: $penggajian->id,
                detail: collect($this->detail)->map(fn($q) => [
                    'debet' => $q['debet'],
                    'kredit' => $q['kredit'],
                    'kode_akun_id' => $q['kode_akun_id'],
                ])->toArray()
            );

            session()->flash('success', 'Berhasil menyimpan data');
        });
        return redirect()->to('kepegawaian/penggajian');
    }

    public function render()
    {
        return view('livewire.kepegawaian.penggajian.form');
    }
}
