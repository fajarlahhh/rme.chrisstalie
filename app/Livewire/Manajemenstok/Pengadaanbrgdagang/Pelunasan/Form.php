<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Pelunasan;

use Livewire\Component;
use App\Models\KodeAkun;
use App\Models\Pembelian;
use App\Class\JurnalClass;
use App\Models\PelunasanPembelian;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $pembelian, $dataPembelian = [], $dataKodePembayaran = [], $kode_akun_pembayaran_id, $pembelian_id, $tanggal, $uraian;

    public function mount($data = null)
    {
        if ($data) {
            $this->pembelian_id = $data;
            $this->pembelian = Pembelian::with('supplier', 'pembelianDetail')->find($data);
        }
        $this->dataPembelian = Pembelian::where('pembayaran', 'Jatuh Tempo')->with('supplier', 'pembelianDetail')
            ->whereDoesntHave('pelunasanPembelian')->get();
        $this->dataKodePembayaran = KodeAkun::where('parent_id', '11100')->detail()->get()->toArray();
    }

    public function updatedPembelianId()
    {
        $this->pembelian = Pembelian::with('supplier', 'pembelianDetail')->find($this->pembelian_id);
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'pembelian_id' => 'required',
            'tanggal' => 'required',
            'uraian' => 'required',
            'kode_akun_pembayaran_id' => 'required',
        ]);

        DB::transaction(function () {
            $pembelian = Pembelian::find($this->pembelian_id);

            $data = new PelunasanPembelian();
            $data->pembelian_id = $this->pembelian_id;
            $data->tanggal = $this->tanggal;
            $data->uraian = $this->uraian;
            $data->kode_akun_pembayaran_id = $this->kode_akun_pembayaran_id;
            $data->jumlah = $pembelian->total_harga;
            $data->save();

            JurnalClass::insert(
                jenis: 'Pengeluaran',
                sub_jenis: 'Pelunasan Pembelian Barang Dagang',
                tanggal: $this->tanggal,
                uraian: $this->uraian,
                system: 1,
                pembelian_id: null,
                stok_masuk_id: null,
                pembayaran_id: null,
                penggajian_id: null,
                pelunasan_pembelian_id: $data->id,
                aset_id: null,
                stok_keluar_id: null,
                detail: [
                    [
                        'debet' => 0,
                        'kredit' => $pembelian->total_harga,
                        'kode_akun_id' => $this->kode_akun_pembayaran_id,
                    ],
                    [
                        'debet' => $pembelian->total_harga,
                        'kredit' => 0,
                        'kode_akun_id' => $pembelian->kode_akun_id,
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
