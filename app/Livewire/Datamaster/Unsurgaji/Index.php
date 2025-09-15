<?php

namespace App\Livewire\Datamaster\Unsurgaji;

use Livewire\Component;
use App\Models\KodeAkun;
use App\Models\UnsurGaji;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    public $dataKodeAkun = [];
    public $unsurGaji = [];

    public function tambahUnsurGaji($unit_bisnis)
    {
        array_push($this->unsurGaji, [
            'nama' => null,
            'sifat' => '+',
            'kode_akun_id' => null,
            'unit_bisnis' => $unit_bisnis,
        ]);
    }

    public function hapusUnsurGaji($key)
    {
        unset($this->unsurGaji[$key]);
        $this->unsurGaji = array_merge($this->unsurGaji);
    }

    public function submit($unit_bisnis)
    {
        $this->validate([
            'unsurGaji' => 'required|array',
            'unsurGaji.*.nama' => 'required',
            'unsurGaji.*.sifat' => 'required',
            'unsurGaji.*.kode_akun_id' => 'required',
        ]);

        DB::transaction(function () use ($unit_bisnis) {
            UnsurGaji::where('unit_bisnis', $unit_bisnis)->delete();
            UnsurGaji::insert(collect($this->unsurGaji)->where('unit_bisnis', $unit_bisnis)->map(fn($q) => [
                'nama' => $q['nama'],
                'sifat' => $q['sifat'],
                'unit_bisnis' => $unit_bisnis,
                'kode_akun_id' => $q['kode_akun_id'],
                'pengguna_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray());
        });

        session()->flash('success', 'Berhasil menyimpan data');
    }

    public function mount()
    {
        $this->dataKodeAkun = KodeAkun::detail()->where('parent_id', '61000')->get()->toArray();
        $this->unsurGaji = UnsurGaji::all()->toArray();
    }

    public function render()
    {
        return view('livewire.datamaster.unsurgaji.index');
    }
}
