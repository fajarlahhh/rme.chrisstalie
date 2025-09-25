<?php

namespace App\Livewire\Klinik\Sitemarking;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\Registrasi;
use App\Models\SiteMarking;
use Illuminate\Support\Facades\DB;

class Form extends Component
{
    #[Url]
    public $id;
    public $registrasi_id, $marker, $catatan = [];
    public $dataRegistrasi = [], $data;

    public function mount(Registrasi $data)
    {
        $this->data = $data;
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
                'catatan' => array_key_exists($q->label, $this->catatan) ? $this->catatan[$q->label] : null,
                'pengguna_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();
            SiteMarking::insert($marker);
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/klinik/sitemarking');
    }

    public function render()
    {
        return view('livewire.klinik.sitemarking.form');
    }
}
