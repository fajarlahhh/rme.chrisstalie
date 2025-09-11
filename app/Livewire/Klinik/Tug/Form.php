<?php

namespace App\Livewire\Klinik\Tug;


use Livewire\Component;
use App\Models\Registrasi;
use Illuminate\Support\Facades\DB;
use App\Models\Tug;

class Form extends Component
{
    public $data, $waktu_tes_detik, $observasi = [], $risiko_jatuh, $catatan;

    public function mount(Registrasi $data)
    {
        $this->data = $data;
        if ($this->data->tug) {
            $this->fill($this->data->tug->toArray());
            $this->observasi = json_decode($this->data->tug->observasi_kualitatif, true);   
        }
    }

    public function submit()
    {
        $this->validate([
            'waktu_tes_detik' => 'required',
            'observasi' => 'array|nullable',
        ]);

        DB::transaction(function () {
            Tug::where('id', $this->data->id)->delete();

            $tug = new Tug();
            $tug->id = $this->data->id;
            $tug->waktu_tes_detik = $this->waktu_tes_detik;
            $tug->observasi_kualitatif = is_array($this->observasi) ? json_encode($this->observasi) : $this->observasi;
            $tug->risiko_jatuh = is_array($this->risiko_jatuh) ? json_encode($this->risiko_jatuh) : $this->risiko_jatuh;
            $tug->catatan = $this->catatan;
            $tug->pengguna_id = auth()->id();
            $tug->save();

            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/klinik/tug');
    }

    public function render()
    {
        return view('livewire.klinik.tug.form');
    }
}
