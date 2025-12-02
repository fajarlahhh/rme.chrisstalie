<?php

namespace App\Livewire\Informasi\Pasien;

use Livewire\Component;
use App\Models\Pasien;
use Livewire\Attributes\Url;

class Index extends Component
{
    #[Url]
    public $pasien, $pasienId;

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
            'rekamMedis.nakes',
            'rekamMedis.pengguna',
            'rekamMedis.pemeriksaanAwal.pengguna',
            'rekamMedis.diagnosis.pengguna',
            'rekamMedis.tindakan.pengguna',
            'rekamMedis.tindakan.tarifTindakan',
            'rekamMedis.tindakan.dokter.pegawai',
            'rekamMedis.tindakan.perawat.pegawai',
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
        if ($this->pasien) {
            $this->dataPasien = $this->getRekamMedis($this->pasien);
        } else {
            $this->dataPasien = null;
        }
    }

    public function render()
    {
        return view('livewire.informasi.pasien.index');
    }
}
