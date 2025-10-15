# Implementasi Alpine.js - Form Tindakan

## Perubahan yang Dilakukan

### 1. File Blade (`resources/views/livewire/klinik/tindakan/form.blade.php`)

#### Perubahan Utama:
- **Root Element**: Ditambahkan `x-data="tindakanForm()"` dan `x-init="init()"`
- **Form Submit**: Ditambahkan `@submit.prevent="syncToLivewire()"`
- **Dynamic Rendering**: Menggunakan `<template x-for>` untuk render tindakan
- **Two-way Binding**: Menggunakan `x-model` untuk semua input
- **Conditional Rendering**: Menggunakan `<template x-if>` untuk dokter/perawat
- **Event Handling**: Menggunakan `@click` untuk button actions

#### Fitur Alpine.js:
```html
<!-- Dynamic list rendering -->
<template x-for="(row, index) in tindakan" :key="index">
    <!-- Form elements dengan x-model -->
    <select x-model="row.id" @change="updateTindakan(index)">
    <input x-model.number="row.qty">
    <textarea x-model="row.catatan">
    
    <!-- Conditional rendering -->
    <template x-if="row.biaya_jasa_dokter > 0">
        <select x-model="row.dokter_id">
    </template>
</template>
```

### 2. JavaScript Function (`tindakanForm()`)

#### Methods yang Tersedia:
- **`tambahTindakan()`**: Menambah tindakan baru ke array
- **`hapusTindakan(index)`**: Menghapus tindakan berdasarkan index
- **`updateTindakan(index)`**: Update data tindakan saat pilihan berubah
- **`syncToLivewire()`**: Sinkronisasi data ke Livewire sebelum submit
- **`init()`**: Inisialisasi form

#### Data Reactive:
```javascript
{
    tindakan: [], // Array tindakan dari Livewire
    dataTindakan: [], // Master data tindakan
    dataNakes: [], // Master data nakes
}
```

### 3. Livewire Controller (`app/Livewire/Klinik/Tindakan/Form.php`)

#### Perubahan:
- **Trait**: Ditambahkan `CustomValidationTrait`
- **Validation**: Menggunakan `validateWithCustomMessages()` dengan pesan bahasa Indonesia
- **Custom Messages**: Pesan error yang user-friendly

```php
$this->validateWithCustomMessages([
    'tindakan' => 'required|array',
    'tindakan.*.id' => 'required|distinct',
    'tindakan.*.qty' => 'required|min:1',
], [
    'tindakan.required' => 'Minimal satu tindakan harus dipilih.',
    'tindakan.*.id.required' => 'Tindakan wajib dipilih.',
    // ... pesan lainnya
]);
```

## Keuntungan Implementasi

### 1. **Performance**
- Tidak ada round-trip ke server untuk operasi UI sederhana
- Rendering yang lebih cepat untuk dynamic content
- Reduced server load

### 2. **User Experience**
- Real-time updates tanpa loading
- Smooth interactions
- Responsive interface

### 3. **Maintainability**
- Kode yang lebih terorganisir
- Separation of concerns
- Reusable components

### 4. **Validation**
- Pesan error berbahasa Indonesia
- User-friendly messages
- Consistent validation across app

## Cara Penggunaan

### Menambah Tindakan Baru:
```javascript
// Otomatis tersedia melalui Alpine.js
@click="tambahTindakan()"
```

### Menghapus Tindakan:
```javascript
// Berdasarkan index
@click="hapusTindakan(index)"
```

### Update Data Tindakan:
```javascript
// Otomatis saat select berubah
@change="updateTindakan(index)"
```

## Integrasi dengan Livewire

Data tetap disinkronkan dengan Livewire melalui:
1. **Initial Load**: Data dari Livewire ke Alpine.js via `@js()`
2. **Form Submit**: Data dari Alpine.js ke Livewire via `syncToLivewire()`
3. **Validation**: Handled by Livewire dengan pesan bahasa Indonesia

## Testing

Untuk testing, pastikan:
1. Form dapat menambah/menghapus tindakan
2. Select2 integration berfungsi
3. Conditional fields (dokter/perawat) muncul sesuai kondisi
4. Data tersinkronisasi dengan benar saat submit
5. Validation messages muncul dalam bahasa Indonesia

## Future Improvements

1. **Real-time Validation**: Validasi client-side sebelum submit
2. **Auto-save**: Simpan draft otomatis
3. **Better Error Handling**: Error handling yang lebih robust
4. **Animation**: Smooth transitions untuk add/remove items
