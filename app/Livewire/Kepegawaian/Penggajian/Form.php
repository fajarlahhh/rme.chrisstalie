<?php

namespace App\Livewire\Kepegawaian\KepegawaianPenggajian;

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
    public $tanggal, $periode, $detail = [];

    public function mount()
    {
        $this->dataKodeAkun = KodeAkun::detail()->whereIn('parent_id', ['11100'])->get()->toArray();
        $this->tanggal = date('Y-m-01');
        $this->periode = date('Y-m');
        if (!KepegawaianPenggajian::where('periode', $this->periode . '-01')->exists()) {
            $this->dataUnsurGaji = KodeAkun::detail()->whereIn('id', KepegawaianPegawaiUnsurGaji::pluck('kode_akun_id'))->get()->toArray();

            foreach (KepegawaianPegawai::with('kepegawaianPegawaiUnsurGaji')->aktif()->get()->toArray() as $kepegawaianPegawai) {
                $unsurGaji = [];
                foreach ($this->dataUnsurGaji as $item) {
                    $unsurGaji[] = [
                        'kepegawaian_pegawai_id' => $kepegawaianPegawai['id'],
                        'nilai' => collect($kepegawaianPegawai['pegawai_unsur_gaji'])->where('kode_akun_id', $item['id'])->first()['nilai'] ?? 0,
                        'kode_akun_id' => $item['id'],
                        'kode_akun_nama' => $item['nama'],
                        'sifat' => collect($kepegawaianPegawai['pegawai_unsur_gaji'])->where('kode_akun_id', $item['id'])->first()['sifat'] ?? null,
                    ];
                }
                $this->detail[] = [
                    'kepegawaian_pegawai_id' => $kepegawaianPegawai['id'],
                    'nama' => $kepegawaianPegawai['nama'],
                    'pegawai_unsur_gaji' => $unsurGaji,
                ];
            }
        }
    }

    public function updatedPeriode($value)
    {
        $this->detail = [];
        if (!KepegawaianPenggajian::where('periode', $value . '-01')->exists()) {
            $this->dataUnsurGaji = KodeAkun::detail()->whereIn('id', KepegawaianPegawaiUnsurGaji::pluck('kode_akun_id'))->get()->toArray();

            foreach (KepegawaianPegawai::with('kepegawaianPegawaiUnsurGaji')->aktif()->get()->toArray() as $kepegawaianPegawai) {
                $unsurGaji = [];
                foreach ($this->dataUnsurGaji as $item) {
                    $unsurGaji[] = [
                        'kepegawaian_pegawai_id' => $kepegawaianPegawai['id'],
                        'nilai' => collect($kepegawaianPegawai['pegawai_unsur_gaji'])->where('kode_akun_id', $item['id'])->first()['nilai'] ?? 0,
                        'kode_akun_id' => $item['id'],
                        'kode_akun_nama' => $item['nama'],
                        'sifat' => collect($kepegawaianPegawai['pegawai_unsur_gaji'])->where('kode_akun_id', $item['id'])->first()['sifat'] ?? null,
                    ];
                }
                $this->detail[] = [
                    'kepegawaian_pegawai_id' => $kepegawaianPegawai['id'],
                    'nama' => $kepegawaianPegawai['nama'],
                    'pegawai_unsur_gaji' => $unsurGaji,
                ];
            }
        }
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'periode' => 'required',
            'tanggal' => 'required',
        ]);

        DB::transaction(function () {
            $penggajian = new KepegawaianPenggajian();
            $penggajian->tanggal = $this->tanggal;
            $penggajian->periode = $this->periode . '-01';
            $penggajian->detail = $this->detail;
            $penggajian->kode_akun_pembayaran_id = $this->metode_bayar;
            $penggajian->pengguna_id = auth()->id();
            $penggajian->save();

            $keuanganJurnalDetail = collect($this->detail)->pluck('pegawai_unsur_gaji')->flatten(1)->groupBy('kode_akun_id')->map(fn($q) => [
                'debet' => $q->sum('nilai'),
                'kredit' => 0,
                'kode_akun_id' => $q->first()['kode_akun_id'],
            ])->toArray();
            $keuanganJurnalDetail[] = [
                'debet' => 0,
                'kredit' => collect($this->detail)->pluck('pegawai_unsur_gaji')->flatten(1)->sum('nilai'),
                'kode_akun_id' => $this->metode_bayar,
            ];
            
            JurnalkeuanganClass::insert(
                jenis: 'Gaji',
                sub_jenis: 'Pengeluaran',
                tanggal: $this->tanggal,
                uraian: 'Gaji Bulan ' . $this->periode,
                system: 1,
                foreign_key: 'penggajian_id',
                foreign_id: $penggajian->id,
                detail: collect($keuanganJurnalDetail)->values()->toArray()
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
