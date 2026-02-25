<?php

namespace App\Livewire\Klinik\Registrasi;

use Livewire\Component;
use App\Models\Pasien;
use App\Models\Nakes;
use App\Models\Registrasi;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Index extends Component
{
    use CustomValidationTrait;

    public $dataNakes = [];
    public $pasien;
    public $tanggal;
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
    public $pasien_description;
    public $keluhan_awal;
    public $ketemu_dokter = 0;

    public function mount()
    {
        $this->tanggal = $this->tanggal ?: date('Y-m-d');
        $this->dataNakes = Nakes::dokter()
            ->with('kepegawaianPegawai')
            ->orderBy('nama')
            ->get()
            ->map(fn($q) => [
                'id' => $q->id,
                'nama' => $q->nama ?: ($q->kepegawaianPegawai->nama ?? ''),
            ])
            ->toArray();
    }

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
            'catatan',
            'pasien_id',
            'keluhan_awal'
        ]);
    }
    
    public function submit()
    {
        $rules = [
            'tanggal' => 'required',
            'nakes_id' => 'required_if:ketemu_dokter,1',
            'keluhan_awal' => 'required',
        ];

        if ($this->pasien_id) {
            $rules['alamat'] = 'required';
            $rules['rm'] = [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = Registrasi::where('pasien_id', $value)
                        ->where('tanggal', $this->tanggal)->whereDoesntHave('pembayaran')
                        ->exists();
                    if ($exists) {
                        $fail('Pasien dengan RM ini sudah terdaftar pada tanggal tersebut.');
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
                $pasien->pengguna_id = auth()->id();
            } else {
                $pasien = Pasien::find($this->pasien_id);
            }

            $pasien->nik = $this->nik;
            $pasien->nama = $this->nama;
            $pasien->alamat = $this->alamat;
            $pasien->jenis_kelamin = $this->jenis_kelamin;
            $pasien->tanggal_lahir = $this->tanggal_lahir;
            $pasien->no_hp = $this->no_hp;
            $pasien->tanggal_daftar = $this->tanggal;
            $pasien->save();

            $registrasi = new Registrasi();
            if (!$this->pasien_id) {
                $registrasi->baru = 1;
            }
            $registrasi->tanggal = $this->tanggal;
            $registrasi->id = str_replace(['/', ':', '-', ' '], '', $this->tanggal . date(' H:i:s'));
            $registrasi->keluhan_awal = $this->keluhan_awal;
            $registrasi->nakes_id = $this->ketemu_dokter == 1 ? $this->nakes_id : null;
            $registrasi->pasien_id = $this->pasien_id ?: $pasien->id;
            $registrasi->ketemu_dokter = $this->ketemu_dokter == 1 ? 1 : 0;
            $registrasi->pengguna_id = auth()->id();
            $registrasi->save();

            session()->flash('success', 'Berhasil menyimpan data');
        });

        return redirect('/klinik/registrasi');
    }

    public function render()
    {
        return view('livewire.klinik.registrasi.index');
    }
}
