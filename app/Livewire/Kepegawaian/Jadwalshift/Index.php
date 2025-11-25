<?php

namespace App\Livewire\Kepegawaian\Jadwalshift;

use Livewire\Component;
use App\Models\Absensi;
use Livewire\Attributes\Url;
use App\Models\Pegawai;
use Illuminate\Support\Carbon;
use App\Models\Shift;

class Index extends Component
{
    #[Url]
    public $cari, $bulan, $pegawai_id;
    public $dataPegawai = [], $dataShift = [], $detail = [];

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
        $this->dataShift = Shift::orderBy('nama')->get()->toArray();
        $this->dataPegawai = Pegawai::orderBy('nama')->get()->toArray();
        $this->pegawai_id = $this->pegawai_id ?: $this->dataPegawai[0]['id'];
        $this->getDetail($this->bulan, $this->pegawai_id);
    }

    public function updatedBulan()
    {
        $this->getDetail();
    }

    public function updatedPegawaiId()
    {
        $this->getDetail();
    }

    public function getDetail()
    {
        $this->reset('detail');

        $absensi = Absensi::where('pegawai_id', $this->pegawai_id)->where('tanggal', 'like', $this->bulan . '%')->get();
        if (!$this->bulan || !preg_match('/^\d{4}-\d{2}$/', $this->bulan)) {
            $this->bulan = date('Y-m');
        }

        $startDate = Carbon::createFromFormat('Y-m-d', $this->bulan . '-01');
        $daysInMonth = $startDate->daysInMonth;

        for ($i = 0; $i < $daysInMonth; $i++) {
            $tanggal = $startDate->copy()->addDays($i)->format('Y-m-d');

            $data = $absensi->firstWhere('tanggal', $tanggal);
            $this->detail[] = $data ? [
                'masuk' =>true,
                'jam_masuk' => $data->jam_masuk,
                'jam_pulang' => $data->jam_pulang,
                'tanggal' => $data->tanggal,
                'shift_id' => $data->shift_id,
            ] : [
                'masuk' => false,
                'jam_masuk' => null,
                'jam_pulang' => null,
                'tanggal' => $tanggal,
                'shift_id' => null,
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
                    'shift_id' => $this->shift_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray() as $q
        ) {
            if (Absensi::where('id', $q['id'])->exists()) {
                Absensi::where('id', $q['id'])->update([
                    'jam_masuk' => $q['jam_masuk'],
                    'jam_pulang' => $q['jam_pulang'],
                    'shift_id' => $q['shift_id'],
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
        return view('livewire.kepegawaian.jadwalshift.index', [
            'data' => Pegawai::where('nama', 'like', '%' . $this->cari . '%')->with('absensi')->whereHas('absensi', function ($query) {
                $query->where('tanggal', 'like', $this->bulan . '%');
            })->get()
        ]);
    }
}
