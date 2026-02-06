<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Tagihan;

use Livewire\Component;
use App\Models\PengadaanTagihan;
use App\Models\PengadaanPemesanan;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;
use App\Models\PengadaanPemesananDetail;
use App\Class\JurnalkeuanganClass;

class Form extends Component
{
    use CustomValidationTrait;
    public $data, $dataPemesanan = [], $barang = [], $pengadaanPemesanan, $pengadaan_pemesanan_id, $no_faktur, $tanggal, $jatuh_tempo, $diskon, $ppn, $catatan, $total_harga_barang, $total_tagihan;

    public function updatedPengadaanPemesananId($value)
    {
        $this->pengadaanPemesanan = PengadaanPemesanan::find($value);
        $this->barang = PengadaanPemesananDetail::where('pengadaan_pemesanan_id', $value)->with('barang', 'pengadaanPemesanan.stokMasuk')->get()->map(fn($q) => [
            'id' => $q->barang_id,
            'nama' => $q->barang->nama,
            'satuan' => $q->barangSatuan->nama,
            'qty' => $q->pengadaanPemesanan->stokMasuk->where('barang_id', $q->barang_id)->sum('qty'),
            'harga_beli' => $q->harga_beli,
        ])->toArray();
        $this->total_harga_barang = collect($this->barang)->sum(fn($q) => $q['qty'] * $q['harga_beli']);
        $this->total_tagihan = $this->total_harga_barang - $this->diskon + $this->ppn;
        // Sync value to blade variable for Alpine.js x-data
        $this->dispatch('set-total-harga-barang', value: $this->total_harga_barang);
    }

    public function submit()
    {
        dd($this->pengadaanPemesanan);
        $this->validateWithCustomMessages([
            'pengadaan_pemesanan_id' => 'required',
            'no_faktur' => 'required',
            'tanggal' => 'required|date',
            'diskon' => 'required|numeric|min:0',
            'ppn' => 'required|numeric|min:0',
            'jatuh_tempo' => 'required|date|after:tanggal',
            'catatan' => 'nullable',
        ]);
        DB::transaction(function () {
            $data = new PengadaanTagihan();
            $data->no_faktur = $this->no_faktur;
            $data->tanggal = $this->tanggal;
            $data->tanggal_jatuh_tempo = $this->jatuh_tempo;
            $data->catatan = $this->catatan;
            $data->total_harga_barang = $this->total_harga_barang;
            $data->diskon = $this->diskon;
            $data->ppn = $this->ppn;
            $data->total_tagihan = $this->total_tagihan;
            $data->pengadaan_pemesanan_id = $this->pengadaan_pemesanan_id;
            $data->supplier_id = $this->pengadaanPemesanan->supplier_id;
            $data->pengguna_id = auth()->id();
            $data->save();

            JurnalkeuanganClass::insert(
                jenis: 'Hutang',
                sub_jenis: 'Tagihan Pengadaan',
                tanggal: $this->tanggal,
                uraian: 'Tagihan pengadaan ' . $this->pengadaanPemesanan->jenis . ' ' . $this->pengadaanPemesanan->nomor . ' dari supplier ' . $this->pengadaanPemesanan->supplier->nama . ' dengan nomor faktur ' . $this->no_faktur . ' tanggal ' . $this->tanggal,
                system: 1,
                foreign_key: 'pengadaan_tagihan_id',
                foreign_id: $data->id,
                detail: [
                    [
                        'debet' => collect($this->barang)->sum(fn($q) => $q['harga_beli'] * $q['qty']),
                        'kredit' => 0,
                        'kode_akun_id' => '12000'
                    ],
                    [
                        'debet' => 0,
                        'kredit' => $this->diskon,
                        'kode_akun_id' => '45000'
                    ],
                    [
                        'debet' => $this->ppn,
                        'kredit' => 0,
                        'kode_akun_id' => '11400'
                    ],
                    [
                        'debet' => 0,
                        'kredit' => collect($this->barang)->sum(fn($q) => $q['harga_beli'] * $q['qty']) - $this->diskon + $this->ppn,
                        'kode_akun_id' => $data->kode_akun_id
                    ]
                ]
            );
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/manajemenstok/pengadaanbrgdagang/tagihan');
    }

    public function mount()
    {
        $this->dataPemesanan = PengadaanPemesanan::select(DB::raw('pengadaan_pemesanan.id id'), 'tanggal', 'supplier_id', 'uraian', 'nomor')
            ->leftJoin('pengadaan_pemesanan_detail', 'pengadaan_pemesanan.id', '=', 'pengadaan_pemesanan_detail.pengadaan_pemesanan_id')
            ->groupBy('pengadaan_pemesanan.id', 'tanggal', 'supplier_id', 'uraian')
            ->havingRaw('SUM(pengadaan_pemesanan_detail.qty) = (SELECT ifnull(SUM(stok_masuk.qty), 0) FROM stok_masuk WHERE pengadaan_pemesanan_id = pengadaan_pemesanan.id )')
            ->whereDoesntHave('pengadaanTagihan')->whereNotNull('nomor')
            ->with('supplier')->get()->toArray();
    }

    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.tagihan.form');
    }
}
