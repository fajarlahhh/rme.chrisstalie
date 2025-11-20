<?php

namespace App\Livewire\Informasipasien;

use Livewire\Component;
use App\Models\Pasien;
use Livewire\Attributes\Url;

class Index extends Component
{
    #[Url]
    public $pasien;

    public $dataPasien;
    public $rekamMedis;

    public function updatedPasienId($id)
    {
        $this->pasien = $id;
        $this->dataPasien = $this->getRekamMedis($id);
    }

    private function getRekamMedis($id)
    {        
        return Pasien::with(
            'rekamMedis',
            'rekamMedis.pengguna',
            'rekamMedis.pemeriksaanAwal.pengguna',
            'rekamMedis.diagnosis.pengguna',
            'rekamMedis.tindakan.pengguna',
            'rekamMedis.tindakan.dokter',
            'rekamMedis.tindakan.perawat',
            'rekamMedis.tindakan.tarifTindakan',
            'rekamMedis.tindakan.dokter',
            'rekamMedis.tindakan.perawat',
            'rekamMedis.tindakan.barangSatuan',
            'rekamMedis.tindakan.barangSatuan.barang',
            'rekamMedis.siteMarking',
            'rekamMedis.siteMarking.pengguna',
            'rekamMedis.resepObat.pengguna',
            'rekamMedis.resepObat.barangSatuan',
            'rekamMedis.resepObat.barangSatuan.barang',
            'rekamMedis.resepObat.barangSatuan.barang.kodeAkun'
        )->find($id);
    }

    public function mount()
    {
        if ($this->pasien) {
            $this->dataPasien = $this->getRekamMedis($this->pasien);
        } else {
            $this->dataPasien = null;
        }
    }

    public function render()
    {
        return view('livewire.informasipasien.index');
    }
}
