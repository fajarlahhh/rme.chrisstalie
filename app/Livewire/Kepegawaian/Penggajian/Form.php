<?php

namespace App\Livewire\Kepegawaian\Penggajian;

use App\Models\Jurnal;
use App\Models\Pegawai;
use Livewire\Component;
use App\Models\KodeAkun;
use App\Class\JurnalClass;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $dataPegawai = [], $pegawai_id, $pegawai, $unsurGaji = [], $dataKodeAkun = [], $metode_bayar;
    public $tanggal;

    public function updatedPegawaiId($value)
    {
        $this->pegawai = Pegawai::find($value);
        $this->dataKodeAkun = KodeAkun::detail()->where('parent_id', '11100')->get()->toArray();
        $this->unsurGaji = $this->pegawai->pegawaiUnsurGaji->map(fn($q) => [
            'kode_akun_id' => $q->unsur_gaji_kode_akun_id,
            'unsur_gaji_sifat' => $q->unsur_gaji_sifat,
            'unsur_gaji_nama' => $q->unsur_gaji_nama,
            'nilai' => $q->nilai,
        ])->toArray();
    }

    public function mount()
    {
        $this->dataPegawai = Pegawai::orderBy('nama')->aktif()->get()->toArray();
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'pegawai_id' => 'required',
            'tanggal' => 'required',
        ]);

        DB::transaction(function () {
            Jurnal::where('referensi_id', 'gaji' . $this->pegawai_id . substr($this->tanggal, 0, 7))->delete();

            $detail = collect($this->unsurGaji)->map(fn($q) => [
                'debet' => $q['nilai'],
                'kredit' => 0,
                'kode_akun_id' => $q['kode_akun_id'],
            ])->toArray();
            $detail[] = [
                'debet' => 0,
                'kredit' => collect($this->unsurGaji)->sum('nilai'),
                'kode_akun_id' => $this->metode_bayar,
            ];
            JurnalClass::insert([
                'jenis' => 'Gaji',
                'system' => 1,
                'tanggal' => $this->tanggal,
                'uraian' => 'Gaji ' . $this->pegawai['nama'] . ' bulan ' . substr($this->tanggal, 0, 7),
                'referensi_id' => 'gaji' . $this->pegawai_id . substr($this->tanggal, 0, 7),
            ], $detail);

            session()->flash('success', 'Berhasil menyimpan data');
        });
        return redirect()->to('kepegawaian/penggajian');
    }

    public function render()
    {
        return view('livewire.kepegawaian.penggajian.form');
    }
}
