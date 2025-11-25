<?php

namespace App\Livewire\Klinik\Diagnosis;

use Livewire\Component;
use App\Models\Registrasi;
use App\Models\Icd10;
use Illuminate\Support\Facades\DB;
use App\Models\Diagnosis;
use App\Traits\FileTrait;
use Livewire\WithFileUploads;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use FileTrait, WithFileUploads, CustomValidationTrait;

    public $data;
    public $dataIcd10 = [];
    public $diagnosis_banding;
    public $icd10 = [];
    public $fileDiupload = [];

    public function mount(Registrasi $data)
    {
        $this->data = $data;
        if ($data->diagnosis) {
            $this->fill($data->diagnosis->toArray());
            $this->icd10 = $data->diagnosis->icd10 ? collect($data->diagnosis->icd10)->map(fn($q) => ['id' => $q])->toArray() : [['id' => null]];
            if ($data->diagnosis->file && method_exists($data->diagnosis->file, 'map')) {
                $this->fileDiupload = $data->file->where('jenis', 'Diagnosis')->map(function ($q) {
                    return [
                        'id' => $q['id'] ?? null,
                        'file' => $q['link'] ?? null,
                        'link' => $q['link'] ?? null,
                        'judul' => $q['judul'] ?? null,
                        'extensi' => $q['extensi'] ?? null,
                        'keterangan' => $q['keterangan'] ?? null
                    ];
                })->all();
            }
        } else {
            $this->icd10 = [['id' => null]];
        }
        $this->dataIcd10 = Icd10::orderBy('uraian')->get()->toArray();
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'icd10' => 'required|array|min:1',
            'icd10.*.id' => 'required',
            'icd10' => 'required',
        ]);

        DB::transaction(function () {
            Diagnosis::where('id', $this->data->id)->delete();

            $diagnosis = new Diagnosis();
            $diagnosis->id = $this->data->id;
            $diagnosis->pasien_id = $this->data->pasien_id;
            $diagnosis->pengguna_id = auth()->id();
            $diagnosis->icd10 = collect($this->icd10)->pluck('id')->toArray();
            $diagnosis->diagnosis_banding = $this->diagnosis_banding;
            $diagnosis->save();

            $this->hapusFile();
            $this->uploadFile($this->data->id, 'Diagnosis');
        });

        session()->flash('success', 'Berhasil menyimpan data Diagnosis');
        return redirect()->to('/klinik/diagnosis/form/' . $this->data->id);
    }

    public function render()
    {
        return view('livewire.klinik.diagnosis.form');
    }
}
