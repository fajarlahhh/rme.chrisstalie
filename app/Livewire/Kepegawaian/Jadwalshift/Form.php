<?php

namespace App\Livewire\Kepegawaian\Jadwalshift;

use App\Models\JadwalShift;
use App\Models\Pegawai;
use App\Models\Absensi;
use Livewire\Component;
use Illuminate\Support\Carbon;
use App\Models\Shift;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $data, $dataPegawai = [], $detail = [], $dataShift = [];
    public $pegawai_id, $bulan, $keterangan;

    public function mount(JadwalShift $data)
    {
        $this->bulan = $this->bulan ?: date('Y-m');
        $this->dataPegawai = Pegawai::orderBy('nama')->get()->toArray();
        $this->dataShift = Shift::orderBy('nama')->get()->toArray();
        $this->data = $data;
        $this->fill($this->data->toArray());
        $this->bulan = $this->data->tahun . '-' . $this->data->bulan;
        if ($this->data->exists) {
            $this->detail = $this->data->jadwalShiftDetail ? $this->data->jadwalShiftDetail->map(fn($q) => [
                'masuk' => $q['masuk'] == 1 ? true : false,
                'jam_masuk' =>  $q['jam_masuk'],
                'jam_pulang' =>  $q['jam_pulang'],
                'tanggal' => $q['tanggal'],
            ])->toArray() : [];
        } else {
            $this->setData();
        }
    }

    public function updatedBulan()
    {
        $this->setData();
    }

    private function setData()
    {
        $this->reset('detail');

        if (!$this->bulan || !preg_match('/^\d{4}-\d{2}$/', $this->bulan)) {
            $this->bulan = date('Y-m');
        }

        $startDate = Carbon::createFromFormat('Y-m-d', $this->bulan . '-01');
        $daysInMonth = $startDate->daysInMonth;

        for ($i = 0; $i < $daysInMonth; $i++) {
            $tanggal = $startDate->copy()->addDays($i)->format('Y-m-d');
            $this->detail[] = [
                'masuk' => false,
                'jam_masuk' => null,
                'jam_pulang' => null,
                'tanggal' => $tanggal
            ];
        }
    }

    public function submit()
    {
        $this->validateWithCustomMessages(
            [
                'pegawai_id' => 'required',
                'bulan' => 'required',
            ]
        );
        
        foreach (
            collect($this->detail)->where('masuk', true)->map(function ($q) {
                $shift = collect($this->dataShift)->where('id', $q['shift_id'])->first();
                return [
                    'id' => $q['tanggal'] . '-' . $this->pegawai_id,
                    'pegawai_id' => $this->pegawai_id,
                    'tanggal' => $q['tanggal'],
                    'jam_masuk' => $shift['jam_masuk'],
                    'jam_pulang' => $shift['jam_pulang'],
                    'shift' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray() as $q
        ) {
            if (Absensi::where('id', $q['id'])->exists()) {
                Absensi::where('id', $q['id'])->update([
                    'jam_masuk' => $q['jam_masuk'],
                    'jam_pulang' => $q['jam_pulang'],
                    'shift' => 1
                ]);
            } else {
                Absensi::insert($q);
            }
        }
        session()->flash('success', 'Berhasil menyimpan data');
        $this->redirect('/kepegawaian/jadwalshift');
    }

    public function render()
    {
        return view('livewire.kepegawaian.jadwalshift.form');
    }
}
