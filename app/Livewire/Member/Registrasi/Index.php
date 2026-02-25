<?php

namespace App\Livewire\Member\Registrasi;

use App\Models\Member;
use App\Models\Pasien;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Index extends Component
{
    use CustomValidationTrait;

    public $pasien;
    public $pasien_id;
    public $rm;
    public $catatan;
    public $nakes_id;
    public $nik;
    public $nama;
    public $alamat;
    public $jenis_kelamin;
    public $tanggal_lahir;
    public $no_hp;
    public $email;

    public function updatedPasienId($id)
    {
        $this->pasien_id = $id;
        $this->pasien = Pasien::find($id);

        if ($this->pasien) {
            $this->rm = $this->pasien->id;
            $this->nik = $this->pasien->nik;
            $this->nama = $this->pasien->nama;
            $this->alamat = $this->pasien->alamat;
            $this->jenis_kelamin = $this->pasien->jenis_kelamin;
            $this->tanggal_lahir = $this->pasien->tanggal_lahir ? $this->pasien->tanggal_lahir->format('Y-m-d') : null;
            $this->no_hp = $this->pasien->no_hp;
        } else {
            $this->resetPasien();
        }
    }

    public function resetPasien()
    {
        $this->reset([
            'nik',
            'rm',
            'nama',
            'alamat',
            'jenis_kelamin',
            'tanggal_lahir',
            'no_hp',
            'pasien_id',
        ]);
    }

    public function submit()
    {
        $rules = [
            'email' => 'required|email|unique:member,email',
        ];

        if ($this->pasien_id) {
            $rules['alamat'] = 'required';
            $rules['rm'] = [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = Member::where('id', $value)
                        ->exists();
                    if ($exists) {
                        $fail('Member dengan ID ini sudah terdaftar.');
                    }
                }
            ];
        } else {
            $rules = array_merge($rules, [
                'nik' => 'required|unique:pasien,nik',
                'nama' => 'required',
                'alamat' => 'required',
                'jenis_kelamin' => 'required',
                'tanggal_lahir' => 'required',
                'no_hp' => 'required',
            ]);
        }

        $this->validateWithCustomMessages($rules);

        DB::transaction(function () {
            if (!$this->pasien_id) {
                $pasien = new Pasien();
                $last = Pasien::where('created_at', 'like', date('Y-m') . '%')
                    ->orderBy('created_at', 'desc')
                    ->first();

                $lastRm = $last ? (int)substr($last->id, 6, 4) : 0;
                $pasien->id = date('y.m.') . sprintf('%04d', $lastRm + 1);
                $pasien->nik = $this->nik;
                $pasien->nama = $this->nama;
                $pasien->alamat = $this->alamat;
                $pasien->jenis_kelamin = $this->jenis_kelamin;
                $pasien->tanggal_lahir = $this->tanggal_lahir;
                $pasien->no_hp = $this->no_hp;
                $pasien->tanggal_daftar = date('Y-m-d');
                $pasien->pengguna_id = auth()->id();
                $pasien->save();
            } else {
                $pasien = Pasien::find($this->pasien_id);
            }

            $member = new Member();
            $member->id = $pasien->id;
            $member->email = $this->email;
            $member->pengguna_id = auth()->id();
            $member->save();

            session()->flash('success', 'Berhasil menyimpan data');
        });

        return redirect('/member/registrasi');
    }

    public function render()
    {
        return view('livewire.member.registrasi.index');
    }
}
