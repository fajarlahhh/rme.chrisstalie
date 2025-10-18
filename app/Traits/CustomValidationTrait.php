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
            'required' => ':ATTRIBUTE wajib diisi.',
            'string' => ':ATTRIBUTE harus berupa teks.',
            'numeric' => ':ATTRIBUTE harus berupa angka.',
            'email' => ':ATTRIBUTE harus berupa alamat email yang valid.',
            'unique' => ':ATTRIBUTE sudah ada yang menggunakan.',
            'min' => [
                'numeric' => ':ATTRIBUTE minimal :min.',
                'string' => ':ATTRIBUTE minimal :min karakter.',
            ],
            'max' => [
                'numeric' => ':ATTRIBUTE maksimal :max.',
                'string' => ':ATTRIBUTE maksimal :max karakter.',
            ],
            'confirmed' => 'Konfirmasi :ATTRIBUTE tidak cocok.',
            'date' => ':ATTRIBUTE harus berupa tanggal yang valid.',
            'array' => ':ATTRIBUTE harus berupa array.',
            'distinct' => ':ATTRIBUTE memiliki nilai yang duplikat.',
            'required_if' => ':ATTRIBUTE wajib diisi.',
            'boolean' => ':ATTRIBUTE harus berupa boolean.',
            'integer' => ':ATTRIBUTE harus berupa angka bulat.',
            'float' => ':ATTRIBUTE harus berupa angka desimal.',
            'email' => ':ATTRIBUTE harus berupa alamat email yang valid.',
            'email' => ':ATTRIBUTE harus berupa alamat email yang valid.',
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
