<?php

namespace App\Livewire\Datamaster\Unsurgaji;

use Livewire\Component;
use App\Models\KodeAkun;
use App\Models\UnsurGaji;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Index extends Component
{
    use CustomValidationTrait;
    public $dataKodeAkun = [];
    public $unsurGaji = [];

    public function submit()
    {   
        $this->validateWithCustomMessages([
            'unsurGaji' => 'required|array',
            'unsurGaji.*.nama' => 'required',
            'unsurGaji.*.sifat' => 'required',
            'unsurGaji.*.kode_akun_id' => 'required',
        ]);

        UnsurGaji::truncate();
        DB::transaction(function () {
            UnsurGaji::insert(collect($this->unsurGaji)->map(fn($q) => [
                'nama' => $q['nama'],
                'sifat' => $q['sifat'],
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
