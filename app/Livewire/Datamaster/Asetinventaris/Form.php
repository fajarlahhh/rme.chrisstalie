<?php

namespace App\Livewire\Datamaster\Asetinventaris;

use App\Models\Aset;
use Livewire\Component;
use App\Models\KodeAkun;
use App\Class\JurnalClass;
use App\Models\AsetPenyusutan;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $data, $dataKodeAkun = [], $dataKodeAkunSumberDana = [], $dataKodeAkunPenyusutan = [];
    public $nama;
    public $tanggal_perolehan;
    public $harga_perolehan;
    public $masa_manfaat;
    public $status;
    public $satuan;
    public $deskripsi;
    public $lokasi;
    public $nilai_residu;
    public $kode_akun_id;
    public $kode_akun_sumber_dana_id;
    public $kode_akun_penyusutan_id;
    public $detail;
    public $metode_penyusutan = 'Garis Lurus';

    public function submit()
    {
        $this->validateWithCustomMessages([
            'nama' => 'required',
            'tanggal_perolehan' => 'required|date',
            'harga_perolehan' => 'required|numeric',
            'masa_manfaat' => 'required|integer',
            'kode_akun_sumber_dana_id' => 'required',
            'satuan' => 'required',
            'lokasi' => 'required',
            'kode_akun_id' => 'required',
            'metode_penyusutan' => 'required',
        ]);

        DB::transaction(function () {
            $edit = true;
            if (!$this->data->exists) {
                $terakhir = Aset::where('kode_akun_id', $this->kode_akun_id)
                    ->orderBy('created_at', 'desc')
                    ->first();
                $nomor = $terakhir ? (int)substr($terakhir->nomor, 6, 4) : 0;
                $this->data->nomor = $this->kode_akun_id . '.' . sprintf('%04d', $nomor + 1);
                $edit = false;
            }

            $this->data->nama = $this->nama;
            $this->data->tanggal_perolehan = $this->tanggal_perolehan;
            $this->data->harga_perolehan = $this->harga_perolehan;
            $this->data->masa_manfaat = $this->masa_manfaat;
            $this->data->satuan = $this->satuan;
            $this->data->deskripsi = $this->deskripsi;
            $this->data->lokasi = $this->lokasi;
            $this->data->kode_akun_id = $this->kode_akun_id;
            $this->data->detail = $this->detail;
            $this->data->kode_akun_sumber_dana_id = $this->kode_akun_sumber_dana_id;
            $this->data->kode_akun_penyusutan_id = collect($this->dataKodeAkunPenyusutan)->where('nama', 'Akumulasi Penyusutan ' . collect($this->dataKodeAkun)->where('id', $this->kode_akun_id)->first()['nama'])->first()['id'];
            $this->data->metode_penyusutan = $this->metode_penyusutan;
            $this->data->status = !$this->data->exists ? 'Aktif' : $this->status;
            $this->data->nilai_penyusutan = $this->harga_perolehan / $this->masa_manfaat;
            $this->data->nilai_residu = $this->nilai_residu;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();

            if ($edit == false) {
                JurnalClass::insert(
                    jenis: 'Pembelian',
                    sub_jenis: 'Pembelian Aset Inventaris',
                    tanggal: $this->tanggal_perolehan,
                    uraian: 'Pembelian Aset Inventaris ' . $this->nama,
                    system: 1,
                    aset_id: $this->data->id,
                    pemesanan_pengadaan_id: null,
                    stok_masuk_id: null,
                    pembayaran_id: null,
                    penggajian_id: null,
                    pelunasan_pemesanan_pengadaan_id: null,
                    stok_keluar_id: null,
                    detail: [
                        [
                            'debet' => 0,
                            'kredit' => $this->harga_perolehan,
                            'kode_akun_id' => $this->kode_akun_sumber_dana_id
                        ],
                        [
                            'debet' => $this->harga_perolehan,
                            'kredit' => 0,
                            'kode_akun_id' => $this->kode_akun_id
                        ]
                    ]
                );

                if ($this->metode_penyusutan == 'Garis Lurus') {
                    $jurnal = JurnalClass::insert(
                        jenis: 'Penyusutan',
                        sub_jenis: 'Penyusutan Aset Inventaris',
                        tanggal: $this->tanggal_perolehan,
                        uraian: 'Penyusutan Aset Inventaris ' . $this->nama,
                        system: 1,
                        aset_id: $this->data->id,
                        pemesanan_pengadaan_id: null,
                        stok_masuk_id: null,
                        pembayaran_id: null,
                        penggajian_id: null,
                        pelunasan_pemesanan_pengadaan_id: null,
                        stok_keluar_id: null,
                        detail: [
                            [
                                'debet' => 0,
                                'kredit' => $this->data->nilai_penyusutan,
                                'kode_akun_id' => $this->data->kode_akun_penyusutan_id
                            ],
                            [
                                'debet' => $this->data->nilai_penyusutan,
                                'kredit' => 0,
                                'kode_akun_id' => '65900'
                            ]
                        ]
                    );

                    $penyusutan = new AsetPenyusutan();
                    $penyusutan->aset_id = $this->data->id;
                    $penyusutan->nilai = $this->data->nilai_penyusutan;
                    $penyusutan->jurnal_id = $jurnal->id;
                    $penyusutan->save();
                }
            }
            $data = Aset::findOrFail($this->data->id);

            $cetak = view('livewire.datamaster.asetinventaris.qr', [
                'cetak' => true,
                'data' => $data,
            ])->render();
            session()->flash('cetak', $cetak);
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/datamaster/asetinventaris');
    }

    public function mount(Aset $data)
    {
        $this->tanggal_perolehan = date('Y-m-d');
        $this->data = $data;
        $this->fill($this->data->toArray());
        $this->dataKodeAkun = KodeAkun::detail()->where('parent_id', '15100')->get()->toArray();
        $this->dataKodeAkunSumberDana = KodeAkun::detail()->whereIn('parent_id', ['11100', '20000', '15300', '21200 '])->get()->toArray();
        $this->dataKodeAkunPenyusutan = KodeAkun::detail()->whereIn('parent_id', ['15200'])->get()->toArray();
    }

    public function render()
    {
        return view('livewire.datamaster.asetinventaris.form');
    }
}
