<?php

namespace App\Livewire\Klinik\Tindakan;

use App\Models\Nakes;
use Livewire\Component;
use App\Models\Tindakan;
use App\Models\Registrasi;
use App\Models\BarangSatuan;
use App\Models\TarifTindakan;
use App\Models\TindakanAlatBarang;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;
use App\Models\TarifTindakanAlatBarang;

class Form extends Component
{
    use CustomValidationTrait;

    public $tindakan = [], $dataTindakan = [], $dataNakes = [];
    public $data, $nakes_id;

    public function mount(Registrasi $data)
    {
        $this->data = $data;
        $this->nakes_id = $data->nakes_id;

        if($this->data->pembayaran){
            return abort(404);
        }
        $this->dataTindakan = TarifTindakan::orderBy('nama')->get()->map(fn($q) => [
            'id' => $q->id,
            'nama' => $q->nama,
            'biaya_jasa_dokter' => $q->biaya_jasa_dokter,
            'biaya_jasa_perawat' => $q->biaya_jasa_perawat,
            'biaya_alat_barang' => $q->biaya_alat_barang,
            'tarif' => $q->tarif
        ])->toArray();
        if ($data->tindakan->count() > 0) {
            $this->tindakan = $data->tindakan->map(fn($q) => [
                'id' => $q->tarif_tindakan_id,
                'qty' => $q->qty,
                'harga' => $q->harga,
                'catatan' => $q->catatan,
                'membutuhkan_inform_consent' => $q->membutuhkan_inform_consent == 1 ? true : false,
                'membutuhkan_sitemarking' => $q->membutuhkan_sitemarking == 1 ? true : false,
                'dokter_id' => $q->dokter_id,
                'perawat_id' => $q->perawat_id,
                'biaya_jasa_dokter' => $q->biaya_jasa_dokter,
                'biaya_jasa_perawat' => $q->biaya_jasa_perawat,
                'biaya_alat_barang' => $q->biaya_alat_barang,
                'biaya' => $q->biaya,
            ])->toArray();
        } else {
            $this->tindakan[] = [
                'id' => null,
                'qty' => 1,
                'harga' => null,
                'catatan' => null,
                'membutuhkan_inform_consent' => false,
                'membutuhkan_sitemarking' => false,
                'dokter_id' => $this->data->nakes_id,
                'perawat_id' => null,
                'biaya_jasa_dokter' => 0,
                'biaya_jasa_perawat' => 0,
                'biaya_alat_barang' => 0,
                'biaya' => 0,
            ];
        }
        $this->dataNakes = Nakes::with('pegawai')->orderBy('nama')->get()->map(fn($q) => [
            'id' => $q->id,
            'dokter' => $q->dokter,
            'nama' => $q->pegawai ? $q->pegawai->nama : $q->nama,
        ])->toArray();
    }

    public function submit()
    {
        if($this->data->pembayaran){
            return abort(404);
        }
        $this->validateWithCustomMessages([
            'tindakan' => 'required|array',
            'tindakan.*.id' => 'required|distinct',
            'tindakan.*.qty' => 'required|min:1',
            'tindakan.*.dokter_id' => function ($attribute, $value, $fail) {
                $index = explode('.', $attribute)[1];
                if (
                    isset($this->tindakan[$index]['biaya_jasa_dokter']) &&
                    $this->tindakan[$index]['biaya_jasa_dokter'] > 0 &&
                    (empty($value) || $value == "" || $value == null)
                ) {
                    $fail('Dokter wajib dipilih untuk tindakan ' . $this->tindakan[$index]['nama']);
                }
            },
        ], [
            'tindakan.required' => 'Minimal satu tindakan harus dipilih.',
            'tindakan.array' => 'Format data tindakan tidak valid.',
            'tindakan.*.id.required' => 'Tindakan wajib dipilih.',
            'tindakan.*.id.distinct' => 'Terdapat tindakan yang duplikat.',
            'tindakan.*.qty.required' => 'Jumlah tindakan wajib diisi.',
            'tindakan.*.qty.min' => 'Jumlah tindakan minimal 1.',
        ]);

        DB::transaction(function () {
            Tindakan::where('registrasi_id', $this->data->id)->delete();

            $tindakanAlatBarang = [];
            $dataTarifTindakanAlatBarang = TarifTindakanAlatBarang::whereIn('tarif_tindakan_id', collect($this->tindakan)->pluck('id'))->get();
            $dataBarangSatuan = BarangSatuan::whereIn('id', collect($dataTarifTindakanAlatBarang)->pluck('barang_satuan_id'))->get();

            foreach (collect($this->tindakan) as $q) {
                $tindakan = new Tindakan();
                $tindakan->registrasi_id = $this->data->id;
                $tindakan->tarif_tindakan_id = $q['id'];
                $tindakan->pasien_id = $this->data->pasien_id;
                $tindakan->biaya = $q['biaya'];
                $tindakan->catatan = $q['catatan'];
                $tindakan->membutuhkan_inform_consent = $q['membutuhkan_inform_consent'];
                $tindakan->membutuhkan_sitemarking = $q['membutuhkan_sitemarking'];
                $tindakan->biaya_jasa_dokter = $q['biaya_jasa_dokter'];
                $tindakan->biaya_jasa_perawat = $q['biaya_jasa_perawat'];
                $tindakan->biaya_alat_barang = $q['biaya_alat_barang'];
                $tindakan->dokter_id = $q['dokter_id'] && $q['dokter_id'] != "" ? $q['dokter_id'] : null;
                $tindakan->perawat_id = $q['perawat_id'] ? $q['perawat_id'] : null;
                $tindakan->qty = $q['qty'];
                $tindakan->pengguna_id = auth()->id();
                $tindakan->save();
                
                $tarifTindakanAlatBarang = $dataTarifTindakanAlatBarang->where('tarif_tindakan_id', $q['id']);

                foreach ($tarifTindakanAlatBarang as $r) {
                    $barangSatuan = $r->aset_id ? null : $dataBarangSatuan->firstWhere('id', $r->barang_satuan_id);

                    $tindakanAlatBarang[] = [
                        'tindakan_id' => $tindakan->id,
                        'aset_id' => $r->aset_id,
                        'qty' => $q['qty'] * $r->qty,
                        'biaya' => $q['qty'] * $r->biaya,
                        'barang_satuan_id' => $r->barang_satuan_id,
                        'barang_id' => $barangSatuan ? $barangSatuan['barang_id'] : null,
                        'rasio_dari_terkecil' => $barangSatuan ? $barangSatuan['rasio_dari_terkecil'] : null,
                        'tarif_tindakan_id' => $q['id'],
                    ];
                }
            }
            TindakanAlatBarang::insert($tindakanAlatBarang);
            session()->flash('success', 'Berhasil menyimpan data');
        });
    }

    public function render()
    {
        return view('livewire.klinik.tindakan.form');
    }
}
