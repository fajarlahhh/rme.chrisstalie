<?php

namespace App\Livewire\Kepegawaian\Absensi;

use App\Models\Absensi;
use Livewire\Component;
use App\Models\Kehadiran;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $tanggal1, $tanggal2;

    public function mount()
    {
        $this->tanggal1 = $this->tanggal1 ?: date('Y-m-01');
        $this->tanggal2 = $this->tanggal2 ?: date('Y-m-d');
    }

    public function updatedCari()
    {
        $this->resetPage();
    }

    public function hapus($id)
    {
        Absensi::findOrFail($id)->delete();
    }

    private function parse($data, $p1, $p2)
    {
        $data = " " . $data;
        $hasil = "";
        $awal = strpos($data, $p1);
        if ($awal != "") {
            $akhir = strpos(strstr($data, $p1), $p2);
            if ($akhir != "") {
                $hasil = substr($data, $awal + strlen($p1), $akhir - strlen($p1));
            }
        }
        return $hasil;
    }

    public function download()
    {
        ini_set('max_execution_time', 300);
        try {
            $Connect = fsockopen(config('app.fingerprint_ip'), "80", $errno, $errstr, 30);
            if ($Connect) {

                $soap_request = "<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">" . config('app.fingerprint_key') . "</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg></GetAttLog>";
                $newLine = "\r\n";
                fputs($Connect, "POST /iWsService HTTP/1.0" . $newLine);
                fputs($Connect, "Content-Type: text/xml" . $newLine);
                fputs($Connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
                fputs($Connect, $soap_request . $newLine);
                $buffer = "";
                while ($Response = fgets($Connect, 1024)) {
                    $buffer = $buffer . $Response;
                }
            }

            $buffer = $this->parse($buffer, "<GetAttLogResponse>", "</GetAttLogResponse>");
            $buffer = explode("\r\n", $buffer);
            $dataKehadiran = [];
            for ($i = 0; $i < count($buffer); $i++) {
                $data = $this->parse($buffer[$i], "<Row>", "</Row>");;
                if ($data) {
                    array_push($dataKehadiran, [
                        'pegawai_id' => (int)$this->parse($data, "<PIN>", "</PIN>"),
                        'waktu' =>  substr($this->parse($data, "<DateTime>", "</DateTime>"), 11, 8),
                        'tanggal' =>  substr($this->parse($data, "<DateTime>", "</DateTime>"), 0, 10),
                        'kode' => $this->parse($data, "<Status>", "</Status>"),
                    ]);
                }
            }

            $dataAbsensi = collect($dataKehadiran)->groupBy('pegawai_id')->map(function ($q) {
                return [
                    'id' => $q->first()['tanggal'] . '-' . $q->first()['pegawai_id'],
                    'pegawai_id' => $q->first()['pegawai_id'],
                    'tanggal' => $q->first()['tanggal'],
                    'masuk' => $q->where('kode', '0')?->sortBy('waktu')->first()['waktu'] ?? null,
                    'pulang' => $q->where('kode', '1')?->sortByDesc('waktu')->first()['waktu'] ?? null,
                    'pengguna_id' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

            DB::transaction(function () use ($dataAbsensi, $dataKehadiran) {
                foreach ($dataKehadiran as $haidrhadi) {
                    Kehadiran::insertOrIgnore($kehadiran->toArray());
                }
                $absensi = collect($dataAbsensi)->chunk(1000);
                foreach ($absensi as $absen) {
                    Absensi::insertOrIgnore($absen->toArray());
                }
            });

            // $Connect1 = fsockopen(config('app.fingerprint_ip'), "80", $errno, $errstr, 30);
            // if ($Connect1) {
            //     $soap_request = "<ClearData><ArgComKey xsi:type=\"xsd:integer\">" . config('app.fingerprint_key') . "</ArgComKey><Arg><Value xsi:type=\"xsd:integer\">3</Value></Arg></ClearData>";
            //     $newLine = "\r\n";
            //     fputs($Connect1, "POST /iWsService HTTP/1.0" . $newLine);
            //     fputs($Connect1, "Content-Type: text/xml" . $newLine);
            //     fputs($Connect1, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            //     fputs($Connect1, $soap_request . $newLine);
            //     $buffer1 = "";
            //     while ($Response1 = fgets($Connect1, 1024)) {
            //         $buffer1 = $buffer1 . $Response1;
            //     }
            // }
            session()->flash('success', 'Berhasil mengambil data absensi');
        } catch (\Exception $e) {
            session()->flash('danger', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.kepegawaian.absensi.index', [
            'data' => Absensi::with(['pegawai'])
                ->whereBetween('tanggal', [$this->tanggal1, $this->tanggal2])
                ->when(
                    $this->cari,
                    fn($q) => $q->whereHas('pegawai', fn($r) => $r
                        ->where('nama', 'ilike', '%' . $this->cari . '%'))
                )
                ->orderBy('tanggal')->paginate(10)
        ]);
    }
}
