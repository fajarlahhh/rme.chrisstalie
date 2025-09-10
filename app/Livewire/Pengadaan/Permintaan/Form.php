<?php

namespace App\Livewire\Pengadaan\Permintaan;

use App\Models\Barang;
use Livewire\Component;
use App\Models\Pengguna;
use App\Models\Verifikasi;
use Illuminate\Support\Str;
use App\Models\BarangSatuan;
use Illuminate\Support\Facades\DB;
use App\Models\PermintaanPembelian;

class Form extends Component
{
    public $dataBarang = [], $dataPengguna = [], $barang = [], $previous;
    public $deskripsi, $data, $verifikator_id;

    public function tambahBarang()
    {
        array_push($this->barang, [
            'id' => null,
            'satuan' => null,
            'barangSatuan' => [],
            'qty' => 0,
            'rasio_dari_terkecil' => 0,
        ]);
    }

    public function hapusbarang($key)
    {
        unset($this->barang[$key]);
        $this->barang = array_merge($this->barang);
    }

    public function submit()
    {
        $this->validate([
            'deskripsi' => 'required',
            'barang' => 'required|array',
            'barang.*.id' => 'required',
            'barang.*.satuan' => 'required',
            'barang.*.qty' => 'required',
            'barang.*.rasio_dari_terkecil' => 'required',
        ]);

        DB::transaction(function () {

            if (!$this->data->exists) {
                $this->data->id = Str::uuid();
            }

            $this->data->deskripsi = $this->deskripsi;
            $this->data->pengguna_id = auth()->id();
            $this->data->save();

            $this->data->permintaanPembelianDetail()->delete();

            $this->data->permintaanPembelianDetail()->insert(collect($this->barang)->map(fn($q) => [
                'qty_permintaan' => $q['qty'],
                'permintaan_pembelian_id' => $this->data->id,
                'barang_satuan_id' => $q['satuan'],
                'barang_id' => $q['id'],
                'rasio_dari_terkecil' => $q['rasio_dari_terkecil'],
            ])->toArray());

            if ($this->verifikator_id) {
                $verifikasi = new Verifikasi();
                $verifikasi->id = Str::uuid();
                $verifikasi->referensi_id = $this->data->id;
                $verifikasi->jenis = 'Permintaan Pembelian';
                $verifikasi->unit_bisnis = 'Apotek';
                $verifikasi->pengguna_id = $this->verifikator_id;
                $verifikasi->save();
            }

            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect($this->previous);
    }

    public function mount(PermintaanPembelian $data)
    {
        $this->previous = url()->previous();
        $this->dataBarang = Barang::with('barangSatuan.satuanKonversi')->orderBy('nama')->get()->map(fn($q) => [
            'id' => $q['id'],
            'nama' => $q['nama'],
            'barangSatuan' => $q['barangSatuan']->map(fn($r) => [
                'id' => $r['id'],
                'nama' => $r['nama'],
                'rasio_dari_terkecil' => $r['rasio_dari_terkecil'],
                'konversi_satuan' => $r['konversi_satuan'],
                'satuan_konversi' => $r['satuanKonversi'] ? [
                    'id' => $r['satuanKonversi']['id'],
                    'nama' => $r['satuanKonversi']['nama'],
                    'rasio_dari_terkecil' => $r['satuanKonversi']['rasio_dari_terkecil'],
                ] : null,
            ]),
        ])->toArray();
        $this->dataPengguna = Pengguna::where(fn($q) => $q->whereHas('permissions', function ($q) {
            $q->where('name', 'pengadaanverifikasi');
        })->orWhere(fn($q) => $q->whereHas('roles', fn($q) => $q->where('name', 'administrator'))))->orderBy('nama')->get()->toArray();
        $this->data = $data;
        $this->fill($this->data->toArray());
        if ($this->data->exists) {
            # code...
            $this->barang = $this->data->permintaanPembelianDetail->map(fn($q) => [
                'id' => $q->barang_id,
                'satuan' => $q->barang_satuan_id,
                'barangSatuan' => BarangSatuan::where('barang_id', $q->barang_id)->get()->map(fn($r) => [
                    'id' => $r->id,
                    'nama' => $r->nama,
                    'rasio_dari_terkecil' => $r->rasio_dari_terkecil,
                    'konversi_satuan' => $r->konversi_satuan,
                ]),
                'qty' => $q->qty_permintaan,
                'rasio_dari_terkecil' => $q->rasio_dari_terkecil,
            ])->toArray();
        }
    }

    public function updatedBarang($value, $key)
    {
        $index = explode('.', $key);
        if ($index[1] == 'id') {
            $barang = collect($this->dataBarang)->where('id', $value)->first();
            $barangSatuan = collect($barang['barangSatuan']);
            $this->barang[$index[0]]['id'] = $barang['id'] ?? null;
            $this->barang[$index[0]]['satuan'] = null;
            $this->barang[$index[0]]['barangSatuan'] = $barangSatuan->toArray();
            $this->barang[$index[0]]['qty'] = $this->barang[$index[0]]['qty'] ?? 0;
            $this->barang[$index[0]]['rasio_dari_terkecil'] = null;
        }

        if ($index[1] == 'satuan') {
            $barang = collect($this->dataBarang)->where('id', $this->barang[$index[0]]['id'])->first();
            $barangSatuan = collect($barang['barangSatuan']);
            $selectedSatuan = $barangSatuan->where('id', $this->barang[$index[0]]['satuan'])->first();
            $this->barang[$index[0]]['satuan'] = $this->barang[$index[0]]['satuan'];
            $this->barang[$index[0]]['rasio_dari_terkecil'] = $selectedSatuan['rasio_dari_terkecil'] ?? null;
        }
    }

    public function render()
    {
        return view('livewire.pengadaan.permintaan.form');
    }
}
