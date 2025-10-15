# Custom Validation Error Messages - Bahasa Indonesia

Dokumentasi ini menjelaskan cara menggunakan custom validation error messages berbahasa Indonesia dalam aplikasi klinik.

## File yang Dibuat

### 1. `lang/id/validation.php`
File utama untuk pesan validasi Laravel berbahasa Indonesia. Berisi:
- Pesan validasi standar Laravel yang sudah diterjemahkan
- Atribut field yang sudah disesuaikan dengan konteks aplikasi klinik
- Custom validation rules untuk kebutuhan spesifik

### 2. `lang/id/messages.php`
File untuk pesan kustom aplikasi yang berisi:
- Pesan sukses/error untuk berbagai operasi
- Pesan khusus untuk modul klinik (pasien, stok, penjualan, dll)
- Pesan konfirmasi dan notifikasi

### 3. `app/Traits/CustomValidationTrait.php`
Trait untuk memudahkan penggunaan validation messages di Livewire components.

## Konfigurasi

### Mengubah Locale Default
Di `config/app.php`, locale sudah diubah ke:
```php
'locale' => env('APP_LOCALE', 'id'),
```

## Cara Penggunaan

### 1. Menggunakan Trait (Recommended)

```php
<?php

namespace App\Livewire\Example;

use Livewire\Component;
use App\Traits\CustomValidationTrait;

class ExampleForm extends Component
{
    use CustomValidationTrait;
    
    public $nama, $email, $password;
    
    public function submit()
    {
        // Menggunakan validation dengan pesan kustom
        $this->validateWithCustomMessages([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);
        
        // Atau dengan pesan tambahan
        $this->validateWithCustomMessages([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar, gunakan email lain.',
        ]);
    }
}
```

### 2. Menggunakan Method Manual

```php
public function submit()
{
    $this->validate(
        [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
        ],
        [
            'nama.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
        ],
        [
            'nama' => 'nama lengkap',
            'email' => 'alamat email',
        ]
    );
}
```

### 3. Menggunakan Custom Messages di Blade

```php
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
```

## Contoh Pesan Error

### Sebelum (Bahasa Inggris)
- "The nama field is required."
- "The email field must be a valid email address."
- "The password field must be at least 8 characters."

### Sesudah (Bahasa Indonesia)
- "Field nama wajib diisi."
- "Field email harus berupa alamat email yang valid."
- "Field password harus berisi setidaknya 8 karakter."

## Pesan Khusus Aplikasi Klinik

### Pasien
- NIK sudah terdaftar dalam sistem
- Pasien dengan RM ini sudah terdaftar pada tanggal tersebut
- Data pasien tidak ditemukan

### Stok/Barang
- Stok [nama barang] tidak mencukupi. Tersisa [jumlah] [satuan]
- Barang dengan nama tersebut sudah ada
- Stok barang tidak tersedia

### Penjualan
- Tidak ada barang yang dipilih
- Jumlah pembayaran tidak mencukupi
- Metode pembayaran tidak valid

## Menambahkan Pesan Baru

### 1. Tambahkan di `lang/id/validation.php`
```php
'custom' => [
    'field_name' => [
        'rule_name' => 'Pesan error kustom untuk field_name dengan rule_name',
    ],
],

'attributes' => [
    'field_name' => 'Nama Field Yang User-Friendly',
],
```

### 2. Tambahkan di `lang/id/messages.php`
```php
'new_message_key' => 'Pesan baru untuk aplikasi',
```

### 3. Tambahkan di Trait
```php
protected function getCustomValidationMessages()
{
    return [
        'field.rule' => 'Pesan error kustom',
        // ... pesan lainnya
    ];
}
```

## Tips Penggunaan

1. **Konsistensi**: Gunakan format pesan yang konsisten di seluruh aplikasi
2. **User-Friendly**: Gunakan bahasa yang mudah dipahami pengguna
3. **Konteks**: Sesuaikan pesan dengan konteks aplikasi klinik
4. **Spesifik**: Berikan pesan yang spesifik dan actionable

## Maintenance

- File validation perlu diupdate ketika ada field baru
- Pesan kustom perlu disesuaikan dengan perubahan business logic
- Trait dapat diperluas sesuai kebutuhan modul baru
