<?php

namespace App\Livewire\Datamaster\Asetinventaris;

use Carbon\Carbon;
use App\Models\Aset;
use App\Models\Jurnal;
use Livewire\Component;
use App\Models\KodeAkun;
use Illuminate\Support\Str;
use App\Models\JurnalDetail;
use Illuminate\Support\Facades\DB;
use App\Models\AsetPenyusutanGarisLurus;
use App\Models\AsetPenyusutanUnitProduksi;

class Form extends Component
{
    public $data, $previous, $dataKodeAkun = [], $dataKodeAkunSumberDana = [];
    public $nama;
    public $tanggal_perolehan;
    public $harga_perolehan;
    public $masa_manfaat;
    public $status;
    public $satuan;
    public $deskripsi;
    public $lokasi;
    public $kode_akun_id;
    public $kode_akun_sumber_dana_id;
    public $detail;
    public $metode_penyusutan;

    public function submit()
    {
        $this->validate([
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
            $this->data->metode_penyusutan = $this->metode_penyusutan;
            $this->data->status = !$this->data->exists ? 'Aktif' : $this->status;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();

            if ($this->metode_penyusutan == 'Garis Lurus') {
                if ($this->data->asetPenyusutanGarisLurus->count() == 0) {
                    $penyusutan = [];
                    for ($i = 1; $i <= $this->masa_manfaat; $i++) {
                        $penyusutan[] = [
                            'aset_id' => $this->data->id,
                            'tanggal' => Carbon::now()->addMonths($i)->format('Y-m-01'),
                            'nilai' => $this->harga_perolehan / $this->masa_manfaat,
                            'jurnal_id' => null,
                        ];
                    }
                    AsetPenyusutanGarisLurus::insert($penyusutan);
                }
            }

            if ($this->metode_penyusutan == 'Satuan Hasil Produksi') {
                if ($this->data->asetPenyusutanUnitProduksi->count() == 0) {
                    $penyusutan = [];
                    for ($i = 1; $i <= $this->masa_manfaat; $i++) {
                        $penyusutan[] = [
                            'aset_id' => $this->data->id,
                            'nilai' => $this->harga_perolehan / $this->masa_manfaat,
                            'jurnal_id' => null,
                        ];
                    }
                    foreach (array_chunk($penyusutan, 1000) as $chunk) {
                        AsetPenyusutanUnitProduksi::insert($chunk);
                    }
                }
            }

            if ($this->data->jurnal) {
                $id = Str::uuid();
                $jurnal = new Jurnal();
                $jurnal->id = $id;
                $jurnal->jenis = 'Pembelian Aset';
                $jurnal->tanggal = $this->tanggal_perolehan;
                $jurnal->uraian = 'Pembelian Aset ' . $this->nama;
                $jurnal->unit_bisnis = $this->unit_bisnis;
                $jurnal->referensi_id = $this->data->id;
                $jurnal->pengguna_id = auth()->id();
                $jurnal->save();
                $jurnalDetail = [
                    [
                        'jurnal_id' => $id,
                        'debet' => 0,
                        'kredit' => $this->harga_perolehan,
                        'kode_akun_id' => $this->kode_akun_sumber_dana_id
                    ],
                    [
                        'jurnal_id' => $id,
                        'debet' => $this->harga_perolehan,
                        'kredit' => 0,
                        'kode_akun_id' => $this->kode_akun_id
                    ]
                ];
                JurnalDetail::insert($jurnalDetail);
            }

            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect($this->previous);
    }

    public function mount(Aset $data)
    {
        $this->previous = url()->previous();
        $this->data = $data;
        $this->fill($this->data->toArray());
        $this->dataKodeAkun = KodeAkun::detail()->where('parent_id', '15100')->get()->toArray();
        $this->dataKodeAkunSumberDana = KodeAkun::detail()->where('parent_id', '11100')->get()->toArray();
    }

    public function render()
    {
        return view('livewire.datamaster.asetinventaris.form');
    }
}
