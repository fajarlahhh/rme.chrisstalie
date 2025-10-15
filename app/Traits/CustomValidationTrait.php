<?php

namespace App\Traits;

trait CustomValidationTrait
{
    /**
     * Mendapatkan pesan validasi kustom berbahasa Indonesia
     */
    protected function getCustomValidationMessages()
    {
        return [
            // Pesan umum
            'required' => 'Field :attribute wajib diisi.',
            'string' => 'Field :attribute harus berupa teks.',
            'numeric' => 'Field :attribute harus berupa angka.',
            'email' => 'Field :attribute harus berupa alamat email yang valid.',
            'unique' => 'Field :attribute sudah ada yang menggunakan.',
            'min' => [
                'numeric' => 'Field :attribute minimal :min.',
                'string' => 'Field :attribute minimal :min karakter.',
            ],
            'max' => [
                'numeric' => 'Field :attribute maksimal :max.',
                'string' => 'Field :attribute maksimal :max karakter.',
            ],
            'confirmed' => 'Konfirmasi :attribute tidak cocok.',
            'date' => 'Field :attribute harus berupa tanggal yang valid.',
            'array' => 'Field :attribute harus berupa array.',
            'distinct' => 'Field :attribute memiliki nilai yang duplikat.',
            'required_if' => 'Field :attribute wajib diisi.',
            'boolean' => 'Field :attribute harus berupa boolean.',
            'integer' => 'Field :attribute harus berupa angka bulat.',
            'float' => 'Field :attribute harus berupa angka desimal.',
            'email' => 'Field :attribute harus berupa alamat email yang valid.',
            'email' => 'Field :attribute harus berupa alamat email yang valid.',
        ];
    }

    /**
     * Validasi dengan pesan kustom berbahasa Indonesia (tanpa custom attribute)
     */
    protected function validateWithCustomMessages(array $rules, array $customMessages = [])
    {
        $messages = array_merge($this->getCustomValidationMessages(), $customMessages);
        return $this->validate($rules, $messages);
    }
}
