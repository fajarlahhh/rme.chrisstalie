<?php

namespace App\Livewire\Kepegawaian\Penggajian;

use App\Models\KepegawaianPegawai;
use Livewire\Component;
use App\Models\KodeAkun;
use App\Class\JurnalkeuanganClass;
use App\Models\KepegawaianPenggajian;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;
use App\Traits\KodeakuntransaksiTrait;

class Form extends Component
{
    use CustomValidationTrait;
    use KodeakuntransaksiTrait;
    public $dataPegawai = [], $dataUnsurGaji = [], $dataKodeAkun = [], $metode_bayar;
    public $tanggal, $periode, $detail = [], $pegawai_id;

    public function mount()
    {
        $this->dataKodeAkun = KodeAkun::detail()->whereIn('id', $this->getKodeAkunTransaksiByTransaksi(['Pembayaran'])->pluck('kode_akun_id'))->get()->toArray();
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
    }

    public function updatedPeriode($value)
    {
        $this->detail = [];
        $this->dataPegawai = KepegawaianPegawai::with('kepegawaianPegawaiUnsurGaji.kodeAkun')->orderBy('nama', 'asc')->whereNotIn('id', KepegawaianPenggajian::where('periode', $value . '-01')->get()->pluck('kepegawaian_pegawai_id'))
        ->where('tanggal_masuk', '<', \Carbon\Carbon::parse($value . '-01')->format('Y-m-t'))
        ->where(fn($q) => $q->where('tanggal_keluar', '>', \Carbon\Carbon::parse($value . '-01')->format('Y-m-01'))->orWhereNull('tanggal_keluar'))  
        ->get()->toArray();
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'periode' => 'required',
            'tanggal' => 'required',
            'pegawai_id' => 'required',
        ]);

        if (JurnalkeuanganClass::tutupBuku(substr($this->tanggal, 0, 7) . '-01')) {
            session()->flash('danger', 'Pembukuan periode ini sudah ditutup');
            return;
        }

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
            $this->jurnalKeuangan($penggajian, $this->detail);
            session()->flash('success', 'Berhasil menyimpan data');
        });
        return redirect()->to('kepegawaian/penggajian');
    }


    private function jurnalKeuangan($penggajian, $detail)
    {
        JurnalkeuanganClass::insert(
            jenis: 'Pengeluaran',
            sub_jenis: 'Pengeluaran Gaji Pegawai',
            tanggal: $this->tanggal,
            uraian: 'Gaji Bulan ' . $this->periode,
            system: 1,
            foreign_key: 'kepegawaian_penggajian_id',
            foreign_id: $penggajian->id,
            detail: collect($detail)->map(fn($q) => [
                'debet' => $q['debet'],
                'kredit' => $q['kredit'],
                'kode_akun_id' => $q['kode_akun_id'],
            ])->toArray()
        );
    }
    public function render()
    {
        return view('livewire.kepegawaian.penggajian.form');
    }
}
