<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Controller;
use Illuminate\Support\Facades\Validator;

class ExampleValidationController extends Controller
{
    /**
     * Contoh penggunaan custom validation messages
     */
    public function store(Request $request)
    {
        // Menggunakan Validator facade dengan pesan kustom
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'no_hp' => 'required|string|min:10',
            'alamat' => 'required|string',
        ], [
            // Custom messages berbahasa Indonesia
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama maksimal 255 karakter.',
            
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar, gunakan email lain.',
            
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.string' => 'Nomor HP harus berupa teks.',
            'no_hp.min' => 'Nomor HP minimal 10 karakter.',
            
            'alamat.required' => 'Alamat lengkap wajib diisi.',
            'alamat.string' => 'Alamat harus berupa teks.',
        ], [
            // Custom attributes
            'nama' => 'nama lengkap',
            'email' => 'alamat email',
            'password' => 'kata sandi',
            'no_hp' => 'nomor HP',
            'alamat' => 'alamat lengkap',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Proses penyimpanan data
        // ...

        return redirect()->back()->with('success', 'Data berhasil disimpan.');
    }

    /**
     * Contoh untuk API dengan response JSON
     */
    public function storeApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Proses penyimpanan
        // ...

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan'
        ]);
    }
}
