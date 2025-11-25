<?php

namespace App\Livewire\Klinik\Upload;

use Livewire\Component;
use App\Models\Registrasi;
use App\Traits\CustomValidationTrait;
use App\Traits\FileTrait;
use Livewire\WithFileUploads;
use Livewire\Attributes\Url;

class Form extends Component
{
    use FileTrait, WithFileUploads, CustomValidationTrait;

    #[Url]
    public $registrasi_id;
    
    public $fileInformedConsent;
    public $data;
    public $fileDiupload = [];

    public function mount(Registrasi $data)
    {
        $this->data = $data;
        if ($this->registrasi_id) {
            $this->data = Registrasi::with(['pasien'])->find($this->registrasi_id);
        }
        if ($this->data->file) {
            $this->fileInformedConsent = $this->data->file->where('jenis', 'Informed Consent')->map(function ($q) {
                return [
                    'id' => $q['id'],
                    'file' => $q['link'],
                    'judul' => $q['judul'],
                    'keterangan' => $q['keterangan'],
                    'extensi' => $q['extensi'],
                ];
            })->first();
        }
    }

    public function updatedRegistrasiId($id)
    {
        $this->data = Registrasi::with(['pasien'])->find($id);
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'fileInformedConsent' => 'required|file|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $this->fileDiupload[] = [
            'id' => null,
            'file' => $this->fileInformedConsent,
            'judul' => 'Informed Consent',
            'link' => null,
            'keterangan' => null,
            'extensi' => null,
        ];
        
        $this->hapusFile();
        $this->uploadFile($this->data->id, 'Informed Consent');
        session()->flash('success', 'Berhasil menyimpan data');
        $this->redirect('/klinik/upload/form?registrasi_id=' . $this->data->id);
    }

    public function render()
    {
        return view('livewire.klinik.upload.form');
    }
}
