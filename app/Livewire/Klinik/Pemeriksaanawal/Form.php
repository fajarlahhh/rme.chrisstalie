<?php

namespace App\Livewire\Klinik\Pemeriksaanawal;

use Livewire\Component;
use App\Models\Registrasi;
use Illuminate\Support\Facades\DB;
use App\Models\PemeriksaanAwal;
use App\Models\Tug;

class Form extends Component
{
    public $data;
    public $waktu_tes_detik, $observasi = [], $risiko_jatuh, $catatan;
    public $keluhan_utama, $riwayat_sekarang, $riwayat_dahulu, $riwayat_alergi;
    public $tekanan_darah = '120/80', $nadi = '70', $pernapasan = '12', $suhu = '36.5', $saturasi_o2 = '98', $berat_badan = '65.5', $tinggi_badan = '170';
    public $kesadaran = 'Compos Mentis', $kesan_sakit = 'Tidak Tampak Sakit', $status_gizi = 'Baik';
    public $kepala_normal = false, $kepala_temuan;
    public $jantung_normal = false, $jantung_temuan;
    public $paru_normal = false, $paru_temuan;
    public $abdomen_normal = false, $abdomen_temuan;
    public $ekstremitas_normal = false, $ekstremitas_temuan;
    public $diagnosis_kerja, $rencana_awal;

    public function mount(Registrasi $data)
    {
        $this->data = $data;
        $this->fill($data->toArray());

        if ($data->pemeriksaanAwal) {
            $this->fill($data->pemeriksaanAwal->toArray());
            $this->kepala_normal = $data->pemeriksaanAwal->kepala_normal == 1 ? true : false;
            $this->jantung_normal = $data->pemeriksaanAwal->jantung_normal == 1 ? true : false;
            $this->paru_normal = $data->pemeriksaanAwal->paru_normal == 1 ? true : false;
            $this->abdomen_normal = $data->pemeriksaanAwal->abdomen_normal == 1 ? true : false;
            $this->ekstremitas_normal = $data->pemeriksaanAwal->ekstremitas_normal == 1 ? true : false;
        }
        if ($data->tug) {
            $this->fill($data->tug->toArray());
            $this->observasi = json_decode($data->tug->observasi_kualitatif, true) ?? [];
        }
    }

    public function submitPemeriksaanAwal()
    {
        $headToToeFields = [
            'kepala',
            'jantung',
            'paru',
            'abdomen',
            'ekstremitas'
        ];

        $rules = [
            'keluhan_utama'    => 'required',
            'riwayat_sekarang' => 'required',
            'riwayat_dahulu'   => 'required',
            'riwayat_alergi'   => 'required',
            'tekanan_darah'    => 'required',
            'nadi'             => 'required',
            'pernapasan'       => 'required',
            'suhu'             => 'required',
            'saturasi_o2'      => 'required',
            'berat_badan'      => 'required',
            'tinggi_badan'     => 'required',
            'kesadaran'        => 'required',
            'kesan_sakit'      => 'required',
            'status_gizi'      => 'required',
            'waktu_tes_detik'  => 'required',
            'observasi'        => 'array|nullable',
            'risiko_jatuh'     => 'required',
            'catatan'          => 'required',
            'diagnosis_kerja'  => 'required',
            'rencana_awal'     => 'required',
        ];

        foreach ($headToToeFields as $field) {
            $rules["{$field}_normal"] = 'required';
            $rules["{$field}_temuan"] = "required_if:{$field}_normal,false";
        }

        $this->validate($rules);

        DB::transaction(function () {
            // Delete existing records
            PemeriksaanAwal::where('id', $this->data->id)->delete();

            // Create PemeriksaanAwal
            $pemeriksaanAwal = new PemeriksaanAwal();
            $pemeriksaanAwal->id = $this->data->id;
            $pemeriksaanAwal->pasien_id = $this->data->pasien_id;
            $pemeriksaanAwal->pengguna_id = auth()->id();
            $pemeriksaanAwal->keluhan_utama = $this->keluhan_utama;
            $pemeriksaanAwal->riwayat_sekarang = $this->riwayat_sekarang;
            $pemeriksaanAwal->riwayat_dahulu = $this->riwayat_dahulu;
            $pemeriksaanAwal->riwayat_alergi = $this->riwayat_alergi;
            $pemeriksaanAwal->tekanan_darah = $this->tekanan_darah;
            $pemeriksaanAwal->nadi = $this->nadi;
            $pemeriksaanAwal->pernapasan = $this->pernapasan;
            $pemeriksaanAwal->suhu = $this->suhu;
            $pemeriksaanAwal->saturasi_o2 = $this->saturasi_o2;
            $pemeriksaanAwal->berat_badan = $this->berat_badan;
            $pemeriksaanAwal->tinggi_badan = $this->tinggi_badan;
            $pemeriksaanAwal->kesadaran = $this->kesadaran;
            $pemeriksaanAwal->kesan_sakit = $this->kesan_sakit;
            $pemeriksaanAwal->status_gizi = $this->status_gizi;
            $pemeriksaanAwal->kepala_normal = $this->kepala_normal ? 1 : 0;
            $pemeriksaanAwal->kepala_temuan = !$this->kepala_normal ? $this->kepala_temuan : null;
            $pemeriksaanAwal->jantung_normal = $this->jantung_normal ? 1 : 0;
            $pemeriksaanAwal->jantung_temuan = !$this->jantung_normal ? $this->jantung_temuan : null;
            $pemeriksaanAwal->paru_normal = $this->paru_normal ? 1 : 0;
            $pemeriksaanAwal->paru_temuan = !$this->paru_normal ? $this->paru_temuan : null;
            $pemeriksaanAwal->abdomen_normal = $this->abdomen_normal ? 1 : 0;
            $pemeriksaanAwal->abdomen_temuan = !$this->abdomen_normal ? $this->abdomen_temuan : null;
            $pemeriksaanAwal->ekstremitas_normal = $this->ekstremitas_normal ? 1 : 0;
            $pemeriksaanAwal->ekstremitas_temuan = !$this->ekstremitas_normal ? $this->ekstremitas_temuan : null;
            $pemeriksaanAwal->diagnosis_kerja = $this->diagnosis_kerja;
            $pemeriksaanAwal->rencana_awal = $this->rencana_awal;
            $pemeriksaanAwal->save();

            session()->flash('success', 'Berhasil menyimpan data Pemeriksaan Awal');
        });
    }

    public function submitTug()
    {
        $rules = [
            'waktu_tes_detik'    => 'required',
            'observasi' => 'required',
            'risiko_jatuh'   => 'required',
            'catatan'   => 'required',
        ];

        $this->validate($rules);

        DB::transaction(function () {
            Tug::where('id', $this->data->id)->delete();
            // Create Tug
            $tug = new Tug();
            $tug->id = $this->data->id;
            $tug->waktu_tes_detik = $this->waktu_tes_detik;
            $tug->observasi_kualitatif = is_array($this->observasi) ? json_encode($this->observasi) : $this->observasi;
            $tug->risiko_jatuh = is_array($this->risiko_jatuh) ? json_encode($this->risiko_jatuh) : $this->risiko_jatuh;
            $tug->catatan = $this->catatan;
            $tug->pengguna_id = auth()->id();
            $tug->save();

            session()->flash('success', 'Berhasil menyimpan data TUG');
        });
    }

    public function render()
    {
        return view('livewire.klinik.pemeriksaanawal.form');
    }
}
