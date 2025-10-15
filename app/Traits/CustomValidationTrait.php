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

            // Pesan khusus untuk aplikasi klinik
            'nik.required' => 'NIK wajib diisi.',
            'nik.unique' => 'NIK sudah terdaftar dalam sistem.',
            'nik.digits' => 'NIK harus terdiri dari 16 digit.',
            
            'rm.required' => 'Nomor rekam medis wajib diisi.',
            'rm.unique' => 'Nomor rekam medis sudah digunakan.',
            
            'nama.required' => 'Nama wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama maksimal 255 karakter.',
            
            'alamat.required' => 'Alamat wajib diisi.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            
            'metode_bayar.required' => 'Metode pembayaran wajib dipilih.',
            'cash.required' => 'Jumlah uang tunai wajib diisi.',
            'cash.numeric' => 'Jumlah uang tunai harus berupa angka.',
            
            'barang.required' => 'Barang wajib dipilih.',
            'qty.required' => 'Jumlah barang wajib diisi.',
            'qty.numeric' => 'Jumlah barang harus berupa angka.',
            'qty.min' => 'Jumlah barang minimal 1.',
            
            'harga.required' => 'Harga wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
            'catatan.max' => 'Catatan maksimal 1000 karakter.',
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
