<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Custom Messages Language Lines
    |--------------------------------------------------------------------------
    |
    | Pesan kustom untuk aplikasi klinik yang lebih spesifik dan user-friendly
    |
    */

    // Pesan Umum
    'data_saved_successfully' => 'Data berhasil disimpan.',
    'data_updated_successfully' => 'Data berhasil diperbarui.',
    'data_deleted_successfully' => 'Data berhasil dihapus.',
    'action_failed' => 'Tindakan gagal dilakukan.',
    'access_denied' => 'Akses ditolak.',
    'invalid_data' => 'Data yang dimasukkan tidak valid.',

    // Pesan Pasien
    'patient_already_registered_today' => 'Pasien dengan RM ini sudah terdaftar pada tanggal tersebut.',
    'patient_not_found' => 'Data pasien tidak ditemukan.',
    'patient_nik_exists' => 'NIK pasien sudah terdaftar dalam sistem.',
    'patient_rm_exists' => 'Nomor rekam medis sudah digunakan.',

    // Pesan Stok/Barang
    'stock_insufficient' => 'Stok :item tidak mencukupi. Tersisa :available :unit.',
    'stock_not_available' => 'Stok barang :item tidak tersedia.',
    'item_not_found' => 'Barang tidak ditemukan.',
    'item_already_exists' => 'Barang dengan nama tersebut sudah ada.',

    // Pesan Penjualan/Transaksi
    'transaction_success' => 'Transaksi berhasil disimpan.',
    'transaction_failed' => 'Transaksi gagal diproses.',
    'payment_amount_insufficient' => 'Jumlah pembayaran tidak mencukupi. Minimal Rp :amount',
    'no_items_selected' => 'Tidak ada barang yang dipilih.',
    'invalid_payment_method' => 'Metode pembayaran tidak valid.',

    // Pesan Authentication
    'login_success' => 'Berhasil masuk ke sistem.',
    'login_failed' => 'Email atau password tidak valid.',
    'logout_success' => 'Berhasil keluar dari sistem.',
    'password_changed_success' => 'Password berhasil diubah.',
    'password_mismatch' => 'Password lama tidak sesuai.',

    // Pesan Registrasi
    'registration_success' => 'Registrasi pasien berhasil.',
    'registration_failed' => 'Registrasi pasien gagal.',
    'queue_number_assigned' => 'Nomor antrian :number telah diberikan.',

    // Pesan Pemeriksaan
    'examination_saved' => 'Data pemeriksaan berhasil disimpan.',
    'diagnosis_required' => 'Diagnosis harus diisi.',
    'vital_signs_required' => 'Tanda-tanda vital harus diisi.',

    // Pesan File/Upload
    'file_uploaded_success' => 'File berhasil diunggah.',
    'file_upload_failed' => 'Gagal mengunggah file.',
    'file_too_large' => 'Ukuran file terlalu besar. Maksimal :size.',
    'invalid_file_type' => 'Tipe file tidak didukung. Gunakan: :types.',

    // Pesan Jadwal
    'schedule_conflict' => 'Jadwal bertabrakan dengan jadwal yang sudah ada.',
    'schedule_saved' => 'Jadwal berhasil disimpan.',
    'appointment_booked' => 'Janji temu berhasil dibuat.',

    // Pesan Laporan
    'report_generated' => 'Laporan berhasil dibuat.',
    'no_data_found' => 'Tidak ada data untuk periode yang dipilih.',
    'export_success' => 'Data berhasil diekspor.',

    // Pesan Supplier
    'supplier_saved' => 'Data supplier berhasil disimpan.',
    'supplier_has_transactions' => 'Supplier tidak dapat dihapus karena memiliki transaksi.',

    // Pesan Pegawai
    'employee_saved' => 'Data pegawai berhasil disimpan.',
    'employee_not_found' => 'Data pegawai tidak ditemukan.',
    'employee_already_exists' => 'Pegawai dengan NIK tersebut sudah terdaftar.',

    // Pesan Tindakan
    'action_completed' => 'Tindakan berhasil diselesaikan.',
    'action_cancelled' => 'Tindakan dibatalkan.',
    'tariff_not_set' => 'Tarif untuk tindakan ini belum ditetapkan.',

    // Pesan Keuangan
    'journal_posted' => 'Jurnal berhasil diposting.',
    'payment_recorded' => 'Pembayaran berhasil dicatat.',
    'balance_insufficient' => 'Saldo tidak mencukupi.',

    // Pesan Validasi Khusus
    'nik_format' => 'NIK harus terdiri dari 16 digit angka.',
    'phone_format' => 'Format nomor HP tidak valid.',
    'date_future' => 'Tanggal tidak boleh lebih dari hari ini.',
    'age_minimum' => 'Umur minimal :min tahun.',
    'age_maximum' => 'Umur maksimal :max tahun.',

    // Pesan Sistem
    'system_maintenance' => 'Sistem sedang dalam pemeliharaan.',
    'feature_not_available' => 'Fitur ini belum tersedia.',
    'session_expired' => 'Sesi Anda telah berakhir. Silakan login kembali.',

    // Pesan Backup/Restore
    'backup_success' => 'Backup data berhasil dibuat.',
    'restore_success' => 'Data berhasil dikembalikan.',
    'backup_failed' => 'Gagal membuat backup data.',

    // Pesan Notifikasi
    'notification_sent' => 'Notifikasi berhasil dikirim.',
    'email_sent' => 'Email berhasil dikirim.',
    'sms_sent' => 'SMS berhasil dikirim.',

    // Pesan Konfirmasi
    'confirm_delete' => 'Apakah Anda yakin ingin menghapus data ini?',
    'confirm_action' => 'Apakah Anda yakin ingin melakukan tindakan ini?',
    'action_irreversible' => 'Tindakan ini tidak dapat dibatalkan.',

    // Pesan Error Khusus
    'database_error' => 'Terjadi kesalahan pada database.',
    'connection_error' => 'Koneksi ke server bermasalah.',
    'timeout_error' => 'Waktu tunggu habis. Silakan coba lagi.',
    'server_error' => 'Terjadi kesalahan pada server.',

];
