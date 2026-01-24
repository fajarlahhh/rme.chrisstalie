<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Pelunasan;

use Livewire\Component;
use App\Models\KodeAkun;
use App\Models\PemesananPengadaan;
use App\Class\JurnalkeuanganClass;
use App\Models\PelunasanPengadaan;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $pemesananPengadaan, $dataPembelian = [], $dataKodePembayaran = [], $kode_akun_pembayaran_id, $pemesanan_pengadaan_id, $tanggal, $uraian;

    public function mount($data = null)
    {
        if ($data) {
            $this->pemesanan_pengadaan_id = $data;
            $this->pemesananPengadaan = PemesananPengadaan::with('supplier', 'pemesananPengadaanDetail')->find($data);
        }
        $this->dataPembelian = PemesananPengadaan::where('pembayaran', 'Jatuh Tempo')->with('supplier', 'pemesananPengadaanDetail')
            ->whereDoesntHave('pelunasanPemesananPengadaan')->get();
        $this->dataKodePembayaran = KodeAkun::where('parent_id', '11100')->detail()->get()->toArray();
    }

    public function updatedPembelianId()
    {
        $this->pemesananPengadaan = PemesananPengadaan::with('supplier', 'pemesananPengadaanDetail')->find($this->pemesanan_pengadaan_id);
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'pemesanan_pengadaan_id' => 'required',
            'tanggal' => 'required',
            'uraian' => 'required',
            'kode_akun_pembayaran_id' => 'required',
        ]);

        DB::transaction(function () {
            $pemesananPengadaan = PemesananPengadaan::find($this->pemesanan_pengadaan_id);

            $data = new PelunasanPengadaan();
            $data->pemesanan_pengadaan_id = $this->pemesanan_pengadaan_id;
            $data->tanggal = $this->tanggal;
            $data->uraian = $this->uraian;
            $data->kode_akun_pembayaran_id = $this->kode_akun_pembayaran_id;
            $data->jumlah = $pemesananPengadaan->total_harga;
            $data->save();

            JurnalkeuanganClass::insert(
                jenis: 'Pengeluaran',
                sub_jenis: 'Pelunasan Pembelian Barang Dagang',
                tanggal: $this->tanggal,
                uraian: $this->uraian,
                system: 1,
                pemesanan_pengadaan_id: null,
                stok_masuk_id: null,
                pembayaran_id: null,
                penggajian_id: null,
                pelunasan_pemesanan_pengadaan_id: $data->id,
                aset_id: null,
                stok_keluar_id: null,
                detail: [
                    [
                        'debet' => 0,
                        'kredit' => $pemesananPengadaan->total_harga,
                        'kode_akun_id' => $this->kode_akun_pembayaran_id,
                    ],
                    [
                        'debet' => $pemesananPengadaan->total_harga,
                        'kredit' => 0,
                        'kode_akun_id' => $pemesananPengadaan->kode_akun_id,
                    ],
                ],
            );
            session()->flash('success', 'Berhasil menambahkan data');
        });

        $this->redirect('/manajemenstok/pengadaanbrgdagang/pelunasan');
    }
    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.pelunasan.form');
    }
}
