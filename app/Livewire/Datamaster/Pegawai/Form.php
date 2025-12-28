<?php

namespace App\Livewire\Datamaster\Pegawai;

use Livewire\Component;
use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;
use App\Models\UnsurGaji;
use App\Traits\CustomValidationTrait;
use App\Models\KodeAkun;

class Form extends Component
{
    use CustomValidationTrait;
    public $data, $unsurGaji = [];
    public $dataKodeAkun = [];
    public $nama, $alamat, $no_hp, $tanggal_masuk, $tanggal_lahir, $jenis_kelamin, $nik, $npwp, $no_bpjs, $gaji, $tunjangan, $tunjangan_transport, $tunjangan_bpjs, $office, $satuan_tugas, $status, $upload = false, $panggilan;

    public function upload($pegawai)
    {
        try {
            $buffer = [];
            $response = [];
            $i = 0;
            $Connect = fsockopen(config('app.fingerprint_ip'), "80", $errno, $errstr, 30);
            if ($Connect) {
                $soap_request = "<SetUserInfo><ArgComKey Xsi:type=\"xsd:integer\">" . config('app.fingerprint_key') . "</ArgComKey><Arg><PIN>" . $pegawai->id . "</PIN><Name>" . $pegawai->panggilan . "</Name><Password>" . $pegawai->id . "</Password><Privilege>19</Privilege></Arg></SetUserInfo>";
                $newLine = "\r\n";
                fputs($Connect, "POST /iWsService HTTP/1.0" . $newLine);
                fputs($Connect, "Content-Type: text/xml" . $newLine);
                fputs($Connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
                fputs($Connect, $soap_request . $newLine);
                $buffer[$i] = "";
                while ($response[$i] = fgets($Connect, 1024)) {
                    $buffer[$i] = $buffer[$i] . $response[$i];
                }
                Pegawai::where('id', $pegawai->id)->update(['upload' => 1]);
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
    }


    public function submit()
    {
        $this->validateWithCustomMessages([
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'jenis_kelamin' => 'required',
            'tanggal_lahir' => 'required|date',
            'tanggal_masuk' => 'required|date',
            'nik' => 'required|numeric|digits:16',
            'no_bpjs' => 'required',
        ]);

        DB::transaction(function () {
            $this->data->nama = $this->nama;
            $this->data->alamat = $this->alamat;
            $this->data->no_hp = $this->no_hp;
            $this->data->tanggal_lahir = $this->tanggal_lahir;
            $this->data->tanggal_masuk = $this->tanggal_masuk;
            $this->data->jenis_kelamin = $this->jenis_kelamin;
            $this->data->nik = $this->nik;
            $this->data->npwp = $this->npwp;
            $this->data->no_bpjs = $this->no_bpjs;
            $this->data->satuan_tugas = $this->satuan_tugas;
            $this->data->panggilan = $this->panggilan;
            if ($this->data->exists) {
                $this->data->status = $this->status == 'Aktif' ? 'Aktif' : 'Non Aktif';
            } else {
                $this->data->status = 'Aktif';
            }
            $this->data->pengguna_id = auth()->id();
            $this->data->save();

            $this->data->pegawaiUnsurGaji()->delete();
            $this->data->pegawaiUnsurGaji()->insert(collect($this->unsurGaji)->where('nilai', '>', 0)->map(fn($q) => [
                'pegawai_id' => $this->data->id,
                'kode_akun_id' => $q['kode_akun_id'],
                'nilai' => $q['nilai'],
                'sifat' => $q['sifat'],
            ])->toArray());

            if ($this->upload == 1) {
                $upload = $this->upload($this->data);
                if ($upload) {
                    session()->flash('success', 'Berhasil mengupload data ke mesin');
                } else {
                    session()->flash('danger', 'Gagal mengupload data ke mesin');
                }
            } else {
                session()->flash('success', 'Berhasil menyimpan data');
            }
        });
        $this->redirect('/datamaster/pegawai');
    }

    public function mount(Pegawai $data)
    {
        
        $this->dataKodeAkun = KodeAkun::detail()->where('parent_id', '61000')->get()->toArray();
        $this->data = $data;
        $this->fill($this->data->toArray());
        $this->unsurGaji = $this->data->pegawaiUnsurGaji->map(fn($q) => [
            'nilai' => $q['nilai'],
            'sifat' => $q['sifat'],
            'kode_akun_id' => $q['kode_akun_id'],
        ])->toArray();
    }

    public function render()
    {
        return view('livewire.datamaster.pegawai.form');
    }
}
