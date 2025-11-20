<?php

namespace App\Livewire\Klinik\Sitemarking;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Registrasi;
use App\Models\SiteMarking;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    #[Url]
    public $marker, $siteMarking = [];
    public $data;

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
                    $this->siteMarking[$key] = $value->catatan;
                }
            }
        }
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'marker' => 'required',
            'siteMarking' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    if (is_array($value)) {
                        foreach ($value as $key => $val) {
                            if (is_null($val) || trim($val) === '') {
                                $fail("The catatan " . ($key + 1) . " required");
                            }
                        }
                    }
                },
            ],
        ]);

        DB::transaction(function () {
            SiteMarking::where('id', $this->data->id)->delete();
            $marker = collect(json_decode($this->marker))->map(fn($q) => [
                'id' => $this->data->id,
                'pasien_id' => $this->data->pasien_id,
                'label' => $q->label,
                'x' => $q->x,
                'y' => $q->y,
                'catatan' => array_key_exists($q->label, $this->siteMarking) ? $this->siteMarking[$q->label] : null,
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
