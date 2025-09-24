<?php

namespace App\Livewire\Klinik\Sitemarking;

use Livewire\Component;
use App\Models\Registrasi;
use App\Models\SiteMarking;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    #[Url]
    public $id;
    public $registrasi_id, $marker, $catatan = [];
    public $dataRegistrasi = [], $data;

    public function mount()
    {
        if ($this->id) {
            $this->data = Registrasi::find($this->id);
        }
        $this->dataRegistrasi = Registrasi::whereHas('informedConsentDenganFile')
            ->whereDoesntHave('siteMarking')
            ->whereDoesntHave('pembayaran')
            ->get();
        if ($this->data) {
            if ($this->data->siteMarking) {
                $this->marker = json_encode($this->data->siteMarking->map(fn($q) => [
                    'canvasId' => 'imgCanvas',
                    'label' => $q->label,
                    'x' => $q->x,
                    'y' => $q->y,
                    'catatan' => $q->catatan,
                ])->toArray());
                foreach ($this->data->siteMarking as $key => $value) {
                    $this->catatan[$value->label] = $value->catatan;
                }
            }
        }
    }

    public function submit()
    {
        $this->validate([
            'marker' => 'required',
            'catatan' => 'required|array',
            'catatan.*' => 'required',
        ]);

        DB::transaction(function () {
            SiteMarking::where('id', $this->data->id)->delete();
            $marker = collect(json_decode($this->marker))->map(fn($q) => [
                'id' => $this->data->id,
                'pasien_id' => $this->data->pasien_id,
                'label' => $q->label,
                'x' => $q->x,
                'y' => $q->y,
                'catatan' => $this->catatan[$q->label],
                'pengguna_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();
            SiteMarking::insert($marker);
            session()->flash('success', 'Berhasil menyimpan data');
        });
    }

    public function updatedRegistrasiId($id)
    {
        $this->redirect('/klinik/sitemarking?id=' . $id);
    }

    public function render()
    {
        return view('livewire.klinik.sitemarking.index');
    }
}
