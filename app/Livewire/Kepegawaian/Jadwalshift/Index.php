<?php

namespace App\Livewire\Kepegawaian\Jadwalshift;

use App\Models\Shift;
use App\Models\KepegawaianAbsensi;
use App\Models\KepegawaianPegawai;
use Livewire\Component;
use Livewire\Attributes\Url;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Index extends Component
{
    use CustomValidationTrait;
    #[Url]
    public $cari, $bulan, $kepegawaian_pegawai_id;
    public $dataPegawai = [], $dataShift = [], $detail = [];

    public function mount()
    {
        $this->bulan = $this->bulan ?: date('Y-m');
        $this->dataShift = Shift::orderBy('nama')->get()->toArray();
        $this->dataPegawai = KepegawaianPegawai::orderBy('nama')->get()->toArray();
        $this->kepegawaian_pegawai_id = $this->kepegawaian_pegawai_id ?: $this->dataPegawai[0]['id'];
        $this->getDetail($this->bulan, $this->kepegawaian_pegawai_id);
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

        $kepegawaianAbsensi = KepegawaianAbsensi::where('kepegawaian_pegawai_id', $this->kepegawaian_pegawai_id)->where('tanggal', 'like', $this->bulan . '%')->get();
        if (!$this->bulan || !preg_match('/^\d{4}-\d{2}$/', $this->bulan)) {
            $this->bulan = date('Y-m');
        }

        $startDate = Carbon::createFromFormat('Y-m-d', $this->bulan . '-01');
        $daysInMonth = $startDate->daysInMonth;

        for ($i = 0; $i < $daysInMonth; $i++) {
            $tanggal = $startDate->copy()->addDays($i)->format('Y-m-d');

            $data = $kepegawaianAbsensi->firstWhere('tanggal', $tanggal);
            $this->detail[] = $data ? [
                'jam_masuk' => $data->jam_masuk,
                'jam_pulang' => $data->jam_pulang,
                'tanggal' => $data->tanggal,
                'shift_id' => $data->shift_id,
                'absen' => $data->shift_id ? true : false,
            ] : [
                'jam_masuk' => null,
                'jam_pulang' => null,
                'tanggal' => $tanggal,
                'shift_id' => null,
                'absen' => false,
            ];
        }
    }

    public function submit()
    {
        $this->validateWithCustomMessages(
            [
                'kepegawaian_pegawai_id' => 'required',
                'bulan' => 'required',
                'detail.*.shift_id' => 'required_if:detail.*.absen,true',
            ]
        );

        DB::transaction(function () {
            foreach (
                collect($this->detail)->map(function ($q) {
                    $shift = collect($this->dataShift)->where('id', $q['shift_id'])->first();
                    return [
                        'id' => $q['tanggal'] . '-' . $this->kepegawaian_pegawai_id,
                        'kepegawaian_pegawai_id' => $this->kepegawaian_pegawai_id,
                        'tanggal' => $q['tanggal'],
                        'jam_masuk' => $q['absen'] === false ? null : $shift['jam_masuk'],
                        'jam_pulang' => $q['absen'] === false ? null : $shift['jam_pulang'],
                        'shift_id' => $q['absen'] === false ? null : $q['shift_id'],
                        'absen' => $q['absen'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray() as $q
            ) {
                KepegawaianAbsensi::where('id', $q['id'])->restore();
                if (KepegawaianAbsensi::where('id', $q['id'])->exists()) {
                    if ($q['absen'] === false) {
                        KepegawaianAbsensi::where('id', $q['id'])->delete();
                    } else {
                        KepegawaianAbsensi::where('id', $q['id'])->update([
                            'jam_masuk' => $q['jam_masuk'],
                            'jam_pulang' => $q['jam_pulang'],
                            'shift_id' => $q['shift_id'],
                        ]);
                    }
                } else {
                    if ($q['absen'] === true) {
                        KepegawaianAbsensi::insert([
                            'id' => $q['id'],
                            'kepegawaian_pegawai_id' => $q['kepegawaian_pegawai_id'],
                            'tanggal' => $q['tanggal'],
                            'jam_masuk' => $q['jam_masuk'],
                            'jam_pulang' => $q['jam_pulang'],
                            'shift_id' => $q['shift_id'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        });
        session()->flash('success', 'Berhasil menyimpan data');
    }

    public function render()
    {
        return view('livewire.kepegawaian.jadwalshift.index');
    }
}
