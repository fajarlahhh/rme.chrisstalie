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
    public $unit_bisnis;

    public function tambahUnsurGaji()
    {
        array_push($this->unsurGaji, [
            'nama' => null,
            'sifat' => '+',
            'kode_akun_id' => null,
        ]);
    }

    public function hapusUnsurGaji($key)
    {
        unset($this->unsurGaji[$key]);
        $this->unsurGaji = array_merge($this->unsurGaji);
    }

    public function submit()
    {
        $this->validate([
            'unit_bisnis' => 'required',
            'unsurGaji' => 'required|array',
            'unsurGaji.*.nama' => 'required',
            'unsurGaji.*.sifat' => 'required',
            'unsurGaji.*.kode_akun_id' => 'required',
        ]);

        DB::transaction(function () {
            UnsurGaji::where('unit_bisnis', $this->unit_bisnis)->delete();
            UnsurGaji::insert(collect($this->unsurGaji)->map(fn($q) => [
                'nama' => $q['nama'],
                'sifat' => $q['sifat'],
                'unit_bisnis' => $this->unit_bisnis,
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
        $this->dataKodeAkun = KodeAkun::where('detail', 1)->where('kategori', 'Beban')->get()->toArray();
    }

    public function updatedKantor($value)
    {
        $this->dataKodeAkun = KodeAkun::where('detail', 1)->where('kategori', 'Beban')->get()->toArray();
        $this->unsurGaji = UnsurGaji::where('unit_bisnis', $value)->get()->toArray();
    }
    
    public function render()
    {
        return view('livewire.datamaster.unsurgaji.index');
    }
}
