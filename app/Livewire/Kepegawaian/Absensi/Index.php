<?php

namespace App\Livewire\Kepegawaian\KepegawaianAbsensi;

use App\Models\KepegawaianAbsensi;
use Livewire\Component;
use App\Models\KehadiranPegawai;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\KepegawaianPegawai;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $tanggal1, $tanggal2, $kepegawaian_pegawai_id;
    public $dataPegawai = [];

    public function mount()
    {
        $this->tanggal1 = $this->tanggal1 ?: date('Y-m-01');
        $this->tanggal2 = $this->tanggal2 ?: date('Y-m-d');
        $this->dataPegawai = KepegawaianPegawai::orderBy('nama')->get()->toArray();
    }

    public function updatedCari()
    {
        $this->resetPage();
    }

    public function hapus($id)
    {
        KepegawaianAbsensi::findOrFail($id)->delete();
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

    public function posting()
    {
        DB::transaction(function () {
            $dataKepegawaianAbsensi = KepegawaianAbsensi::with(['kepegawaianPegawai.kepegawaianKehadiran'])
                ->whereBetween('tanggal', [$this->tanggal1, $this->tanggal2])
                ->when(
                    $this->cari,
                    fn($q) => $q->whereHas('kepegawaianPegawai', fn($r) => $r
                        ->where('nama', 'ilike', '%' . $this->cari . '%'))
                )
                ->orderBy('tanggal')->get()->map(function ($q) {
                    $kepegawaianKehadiran = $q->kepegawaianPegawai->kepegawaianKehadiran->where('tanggal', $q->tanggal);
                    $masuk = $kepegawaianKehadiran->first()?->waktu;
                    $pulang = $kepegawaianKehadiran->last()?->waktu;
                    return [
                        'id' => $q->id,
                        'masuk' => $masuk,
                        'pulang' => $pulang,
                    ];
                });
            foreach ($dataKepegawaianAbsensi as $kepegawaianAbsensi) {
                KepegawaianAbsensi::where('id', $kepegawaianAbsensi['id'])->update([
                    'masuk' => $kepegawaianAbsensi['masuk'],
                    'pulang' => $kepegawaianAbsensi['pulang'],
                ]);
            }

            session()->flash('success', 'Berhasil mengambil data kepegawaianAbsensi');
        });
    }

    public function download()
    {
        ini_set('max_execution_time', 300);
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
                    'id' => $this->parse($data, "<DateTime>", "</DateTime>") . '-' . (int)$this->parse($data, "<PIN>", "</PIN>"),
                    'kepegawaian_pegawai_id' => (int)$this->parse($data, "<PIN>", "</PIN>"),
                    'waktu' =>  substr($this->parse($data, "<DateTime>", "</DateTime>"), 11, 8),
                    'tanggal' =>  substr($this->parse($data, "<DateTime>", "</DateTime>"), 0, 10),
                    'kode' => $this->parse($data, "<Status>", "</Status>"),
                    'masuk' => $this->parse($data, "<Status>", "</Status>") == '0' ? $this->parse($data, "<DateTime>", "</DateTime>") : null,
                    'pulang' => $this->parse($data, "<Status>", "</Status>") == '1' ? $this->parse($data, "<DateTime>", "</DateTime>") : null,
                ]);
            }
        }
        DB::transaction(function () use ($dataKehadiran) {
            foreach ($dataKehadiran as $kepegawaianKehadiran) {
                KehadiranPegawai::insertOrIgnore($kepegawaianKehadiran);
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
        session()->flash('success', 'Berhasil mengambil data kepegawaianAbsensi');
    }

    public function render()
    {
        return view('livewire.kepegawaian.absensi.index', [
            'data' => KepegawaianAbsensi::with(['kepegawaianPegawai.kepegawaianKehadiran'])
                ->when($this->kepegawaian_pegawai_id, fn($q) => $q->where('kepegawaian_pegawai_id', $this->kepegawaian_pegawai_id))
                ->whereBetween('tanggal', [$this->tanggal1, $this->tanggal2])
                ->when(
                    $this->cari,
                    fn($q) => $q->whereHas('kepegawaianPegawai', fn($r) => $r
                        ->where('nama', 'ilike', '%' . $this->cari . '%'))
                )
                ->orderBy('tanggal')->paginate(10)
        ]);
    }
}
