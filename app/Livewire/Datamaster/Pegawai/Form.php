<?php

namespace App\Livewire\Datamaster\Pegawai;

use Livewire\Component;
use App\Models\KodeAkun;
use App\Models\Pengguna;
use App\Models\KepegawaianPegawai;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;
use App\Traits\FileTrait;
use Livewire\WithFileUploads;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class Form extends Component
{
    use CustomValidationTrait, FileTrait, WithFileUploads;
    public $data, $unsurGaji = [];
    public $dataKodeAkun = [];
    public $nama, $alamat, $no_hp, $tanggal_masuk, $tanggal_lahir, $jenis_kelamin, $nik, $npwp, $no_bpjs, $gaji, $tunjangan, $tunjangan_transport, $tunjangan_bpjs, $office, $satuan_tugas, $status, $upload = false, $panggilan, $tanda_tangan, $file_ttd, $sipa;

    public function upload($kepegawaianPegawai)
    {
        try {
            $buffer = [];
            $response = [];
            $i = 0;
            $Connect = fsockopen(config('app.fingerprint_ip'), "80", $errno, $errstr, 30);
            if ($Connect) {
                $soap_request = "<SetUserInfo><ArgComKey Xsi:type=\"xsd:integer\">" . config('app.fingerprint_key') . "</ArgComKey><Arg><PIN>" . $kepegawaianPegawai->id . "</PIN><Name>" . $kepegawaianPegawai->panggilan . "</Name><Password>" . $kepegawaianPegawai->id . "</Password><Privilege>19</Privilege></Arg></SetUserInfo>";
                $newLine = "\r\n";
                fputs($Connect, "POST /iWsService HTTP/1.0" . $newLine);
                fputs($Connect, "Content-Type: text/xml" . $newLine);
                fputs($Connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
                fputs($Connect, $soap_request . $newLine);
                $buffer[$i] = "";
                while ($response[$i] = fgets($Connect, 1024)) {
                    $buffer[$i] = $buffer[$i] . $response[$i];
                }
                KepegawaianPegawai::where('id', $kepegawaianPegawai->id)->update(['upload' => 1]);
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
            'file_ttd' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
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
            if ($this->file_ttd) {
                $extensi = $this->file_ttd->getClientOriginalExtension();
                $namaFile = $this->nik . '.' . $extensi;
                $gambar = Image::make($this->file_ttd)->encode('jpg', 0)->resize(300, null, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });
                Storage::put('public/pegawai/' . $namaFile, $gambar->stream());
                $this->data->tanda_tangan = 'pegawai/' . $namaFile;
            }
            $this->data->sipa = $this->sipa;
            if ($this->data->exists) {
                $this->data->status = $this->status == 'Aktif' ? 'Aktif' : 'Non Aktif';
                if ($this->data->status == 'Non Aktif') {
                    Pengguna::where('kepegawaian_pegawai_id', $this->data->id)->delete();
                }
            } else {
                $this->data->status = 'Aktif';
            }
            $this->data->pengguna_id = auth()->id();
            $this->data->save();

            $this->data->kepegawaianPegawaiUnsurGaji()->delete();
            $this->data->kepegawaianPegawaiUnsurGaji()->insert(collect($this->unsurGaji)->where('nilai', '>', 0)->map(fn($q) => [
                'kepegawaian_pegawai_id' => $this->data->id,
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

    public function mount(KepegawaianPegawai $data)
    {

        $this->dataKodeAkun = KodeAkun::detail()->where('kategori', 'Beban')->get()->toArray();
        $this->data = $data;
        $this->fill($this->data->toArray());
        $this->unsurGaji = $this->data->kepegawaianPegawaiUnsurGaji->map(fn($q) => [
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
