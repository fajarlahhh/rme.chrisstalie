<?php

namespace App\Livewire\Klinik\Diagnosis;

use Livewire\Component;
use App\Models\Registrasi;
use App\Models\Icd10;
use Illuminate\Support\Facades\DB;
use App\Models\Diagnosis;
use App\Traits\FileTrait;
use Livewire\WithFileUploads;

class Form extends Component
{
    use FileTrait, WithFileUploads;

    public $data;
    public $dataIcd10 = [];
    public $diagnosis_banding;
    public $rencana_pemeriksaan;
    public $rencana_terapi;
    public $diagnosis = [];

    public function tambahDiagnosis()
    {
        $this->diagnosis[] = ['icd10' => null];
    }

    public function hapusDiagnosis($index)
    {
        unset($this->diagnosis[$index]);
        $this->diagnosis = array_merge($this->diagnosis);
    }

    public function mount(Registrasi $data)
    {
        $this->data = $data;
        if ($data->diagnosis) {
            $this->fill($data->diagnosis->toArray());
            $this->diagnosis = json_decode($data->diagnosis->icd10, true);

            if ($this->data->diagnosis->file) {
                $this->fileDiupload = $this->data->diagnosis->file->map(fn($q) => [
                    'id' => $q['id'],
                    'file' => $q['link'],
                    'link' => $q['link'],
                    'judul' => $q['judul'],
                    'extensi' => $q['extensi'],
                    'keterangan' => $q['keterangan']
                ])->all();
            }
        } else {
            $this->diagnosis[] = ['icd10' => null];
        }
        $this->dataIcd10 = Icd10::orderBy('uraian')->get()->toArray();
    }

    public function submit()
    {
        $this->validate([
            'diagnosis' => 'required|array',
            'diagnosis.*.icd10' => 'required',
            'diagnosis_banding' => 'required',
        ]);
        DB::transaction(function () {

            Diagnosis::where('id', $this->data->id)->delete();

            $data = new Diagnosis();
            $data->id = $this->data->id;
            $data->pasien_id = $this->data->pasien_id;
            $data->pengguna_id = auth()->id();
            $data->icd10 = json_encode($this->diagnosis);
            $data->diagnosis_banding = $this->diagnosis_banding;
            $data->rencana_terapi = $this->rencana_terapi;
            $data->rencana_pemeriksaan = $this->rencana_pemeriksaan;
            $data->save();

            $this->hapusFile();
            $this->uploadFile($this->data->id, 'Diagnosis');
        });
        session()->flash('success', 'Berhasil menyimpan data Diagnosis');
        $this->redirect('/klinik/diagnosis/form/' . $this->data->id);
    }

    public function render()
    {
        return view('livewire.klinik.diagnosis.form');
    }
}
