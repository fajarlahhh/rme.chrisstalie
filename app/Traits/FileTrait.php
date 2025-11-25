<?php

namespace App\Traits;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Models\File;
use App\Traits\CustomValidationTrait;

/**
 *
 */
trait FileTrait
{
    use CustomValidationTrait;
    public $fileDihapus = [], $fileDiupload = [];

    public function uploadFile($registrasi_id, $path)
    {
        $data = [];
        foreach (collect($this->fileDiupload)->where('id', null)->all() as $key => $row) {
            $extensi = $row['file']->getClientOriginalExtension();
            if (in_array($extensi, ['pdf', 'jpeg', 'png', 'jpg'])) {
                $namaFile = uniqid() . '~' . date('YmdHims') . time() . '~' . $key . '.' . $extensi;
                if (in_array($extensi, ['jpeg', 'png', 'jpg'])) {
                    $gambar = Image::make($row['file'])->encode('jpg', 0)->resize(300, null, function ($c) {
                        $c->aspectRatio();
                        $c->upsize();
                    });
                    Storage::put('public/' . strtolower($path) . '/' . $namaFile, $gambar->stream());
                }

                if (in_array($extensi, ['pdf'])) {
                    Storage::putFileAs('public/' . strtolower($path), $row['file'], $namaFile);
                }

                $data[] = [
                    'registrasi_id' => $registrasi_id,
                    'jenis' => $path,
                    'link' => strtolower($path) . '/' . $namaFile,
                    'judul' => $row['judul'],
                    'keterangan' => $row['keterangan'],
                    'extensi' =>  $extensi,
                ];
            }
        }
        File::insert($data);
    }

    public function hapusFile()
    {
        foreach ($this->fileDihapus as $row) {
            Storage::disk(config('constants.storage'))->delete($row);
        }
        File::whereIn('id', collect($this->fileDihapus)->keys()->all())->delete();
    }

    public function tambahFileDihapus($key, $value)
    {
        if (!collect($this->fileDihapus)->contains($value)) {
            $this->fileDihapus[$key] = $value;
        }
    }

    public function batalFileDihapus($key)
    {
        unset($this->fileDihapus[$key]);
    }

    public function tambahFileDiupload()
    {
        $this->fileDiupload[] = [
            'id' => null,
            'file' => null,
            'judul' => null,
            'link' => null,
            'keterangan' => null,
            'extensi' => null,
        ];
    }

    public function hapusFileDiupload($key)
    {
        unset($this->fileDiupload[$key]);
    }

    public function validateFile()
    {
        if (collect($this->fileDiupload)->where('id', null)->count() > 0) {
            $this->validateWithCustomMessages([
                'fileDiupload.*.file' => 'required',
                'fileDiupload.*.judul' => 'required',
            ]);
        }
    }

    public function cekFileUpload()
    {
        // if (File::whereIn('judul', collect($this->fileDiupload)->pluck('judul')->all())->count() > 0) {
        //     session()->flash('danger', 'Judul pada data detail sudah pernah diinputkan');
        //     return false;
        // }
        return true;
    }
}
