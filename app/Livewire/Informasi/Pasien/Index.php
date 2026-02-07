<?php

namespace App\Livewire\Informasi\Pasien;

use Livewire\Component;
use App\Models\Pasien;
use Livewire\Attributes\Url;

class Index extends Component
{
    #[Url]
    public $noRm;

    public $dataPasien;
    public $rekamMedis;

    public function updatedNoRm()
    {
        $this->dataPasien = $this->getRekamMedis($this->noRm);
    }

    private function getRekamMedis($id)
    {        
        return Pasien::with(
            'rekamMedis.nakes',
            'rekamMedis.tug',
            'rekamMedis.pengguna',
            'rekamMedis.pemeriksaanAwal.pengguna',
            'rekamMedis.diagnosis.pengguna',
            'rekamMedis.tindakan.pengguna',
            'rekamMedis.tindakan.tarifTindakan',
            'rekamMedis.tindakan.dokter',
            'rekamMedis.tindakan.perawat',
            'rekamMedis.tindakan.barangSatuan',
            'rekamMedis.tindakan.barangSatuan.barang',
            'rekamMedis.siteMarking.pengguna',
            'rekamMedis.resepObat.pengguna',
            'rekamMedis.resepObat.barangSatuan',
            'rekamMedis.resepObat.barangSatuan.barang',
            'rekamMedis.resepObat.barangSatuan.barang.kodeAkun',
            'rekamMedis.pembayaran.pengguna'
        )->find($id);
    }

    public function mount()
    {
        if ($this->noRm) {
            $this->dataPasien = $this->getRekamMedis($this->noRm);
        } else {
            $this->dataPasien = null;
        }
    }

    public function render()
    {
        return view('livewire.informasi.pasien.index');
    }
}
