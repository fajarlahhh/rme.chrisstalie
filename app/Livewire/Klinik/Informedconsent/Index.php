<?php

namespace App\Livewire\Klinik\Informedconsent;

use Livewire\Component;
use App\Models\Registrasi;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\DB;
use App\Models\InformedConsent;
use App\Traits\CustomValidationTrait;

class Index extends Component
{
    use CustomValidationTrait;
    #[Url]
    public $id;
    public $registrasi_id, $marker, $catatan = [], $ttd_pasien, $ttd_saksi;
    public $dataRegistrasi = [], $data, $status;

    public function mount()
    {
        if ($this->id) {
            $this->data = Registrasi::find($this->id);
            if ($this->data->informedConsent) {
                $this->fill($this->data->informedConsent);
            }
        }
        $this->dataRegistrasi = Registrasi::whereHas('pemeriksaanAwal')
            ->whereDoesntHave('informedConsent')->whereHas('tindakanDenganInformConsent')
            ->where('tanggal', date('Y-m-d'))
            ->get();
    }


    public function submit()
    {
        $this->validateWithCustomMessages([
            'status' => 'required',
        ]);

        if ($this->status == 'menyetujui') {
            $this->validateWithCustomMessages([
                'ttd_pasien' => 'required',
                'ttd_saksi' => 'required',
            ]);
        }

        DB::transaction(function () {
            InformedConsent::where('id', $this->data->id)->delete();
            InformedConsent::insert([
                'id' => $this->data->id,
                'pasien_id' => $this->data->pasien_id,
                'pengguna_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
                'status' => $this->status == 'menyetujui' ? 1 : 0,
                'ttd_pasien' => $this->ttd_pasien,
                'ttd_saksi' => $this->ttd_saksi,
            ]);
            if ($this->status == 'menyetujui') {
                $cetak = view('livewire.klinik.informedconsent.cetak', [
                    'cetak' => true,
                    'data' => Registrasi::findOrFail($this->data->id),
                ])->render();
                session()->flash('cetak', $cetak);
            }
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/klinik/informedconsent/data');
    }

    public function updatedRegistrasiId($id)
    {
        $this->redirect('/klinik/informedconsent?id=' . $id);
    }

    public function render()
    {
        return view('livewire.klinik.informedconsent.index');
    }
}
