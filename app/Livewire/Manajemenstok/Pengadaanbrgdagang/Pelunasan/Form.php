<?php

namespace App\Livewire\Manajemenstok\Pengadaanbrgdagang\Pelunasan;

use Livewire\Component;
use App\Models\KodeAkun;
use App\Models\PengadaanPemesanan;
use App\Class\JurnalkeuanganClass;
use App\Models\PengadaanPelunasan;
use App\Models\PengadaanTagihan;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;
use App\Models\Supplier;
use App\Traits\KodeakuntransaksiTrait;
use Livewire\Attributes\Url;

class Form extends Component
{
    use CustomValidationTrait, KodeakuntransaksiTrait;

    #[Url]
    public $supplier;
    public $pengadaanTagihan = [], $dataSupplier = [], $dataKodePembayaran = [], $kode_akun_pembayaran_id, $pengadaan_pemesanan_id, $tanggal, $catatan, $pengadaan_tagihan_id = [], $bukti;

    public function mount()
    {
        $this->dataSupplier = Supplier::whereIn(
            'id',
            PengadaanTagihan::select('supplier_id')
                ->distinct()
                ->whereDoesntHave('pengadaanPelunasan')
                ->get()
                ->pluck('supplier_id')
        )->orderBy('nama')->get()->toArray();
        $this->updatedSupplier();
        $this->dataKodePembayaran = KodeAkun::detail()->whereIn('id', ($this->getKodeAkunTransaksiByTransaksi('Pembayaran')->pluck('kode_akun_id')))->get()->toArray();
    }

    public function updatedSupplier()
    {
        $this->pengadaanTagihan = PengadaanTagihan::with('pengadaanPemesanan.pengadaanPemesananDetail.barang', 'pengadaanPemesanan.pengadaanPemesananDetail.barangSatuan')->where('supplier_id', $this->supplier)->get()->toArray();

        $this->dispatch('set-total-tagihan', value: collect($this->pengadaanTagihan));
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'pengadaan_tagihan_id' => 'required|array',
            'tanggal' => 'required|date',
            'catatan' => 'required',
            'kode_akun_pembayaran_id' => 'required',
        ]);
        
        if (JurnalkeuanganClass::tutupBuku(substr($this->tanggal, 0, 7) . '-01')) {
            session()->flash('danger', 'Pembukuan periode ini sudah ditutup');
            return;
        }
        
        DB::transaction(function () {
            $pengadaanTagihan = PengadaanTagihan::whereIn('id', $this->pengadaan_tagihan_id)->get();

            $data = new PengadaanPelunasan();
            $data->tanggal = $this->tanggal;
            $data->bukti = $this->bukti;
            $data->catatan = $this->catatan;
            $data->supplier_id = $this->supplier;
            $data->kode_akun_pembayaran_id = $this->kode_akun_pembayaran_id;
            $data->jumlah = $pengadaanTagihan->sum('total_tagihan');
            $data->save();

            $data->pengadaanPelunasanDetail()->delete();
            $data->pengadaanPelunasanDetail()->insert(collect($pengadaanTagihan)->map(fn($q) => [
                'pengadaan_pelunasan_id' => $data->id,
                'tagihan' => $q->total_tagihan,
                'pengadaan_tagihan_id' => $q->id,
            ])->toArray());

            $this->jurnalKeuangan(
                uraian: 'Pelunasan pengadaan barang dagang No. Tagihan ' . $pengadaanTagihan->pluck('no_faktur')->implode(', ') . ' supplier ' . $pengadaanTagihan->first()->supplier->nama . ', Bukti : ' . $this->bukti,
                foreign_id: $data->id,
                detail: [
                    [
                        'debet' => 0,
                        'kredit' => $pengadaanTagihan->sum('total_tagihan'),
                        'kode_akun_id' => $this->kode_akun_pembayaran_id,
                    ],
                    [
                        'debet' => $pengadaanTagihan->sum('total_tagihan'),
                        'kredit' => 0,
                        'kode_akun_id' => $pengadaanTagihan->first()->supplier->kode_akun_id,
                    ],
                ],
            );
            session()->flash('success', 'Berhasil menambahkan data');
        });

        $this->redirect('/manajemenstok/pengadaanbrgdagang/pelunasan');
    }

    private function jurnalKeuangan($uraian, $foreign_id, $detail)
    {

        JurnalkeuanganClass::insert(
            jenis: 'Pengeluaran',
            sub_jenis: 'Pengeluaran pelunasan pengadaan barang dagang',
            tanggal: $this->tanggal,
            uraian: $uraian,
            system: 1,
            foreign_key: 'pengadaan_pelunasan_id',
            foreign_id: $foreign_id,
            detail: $detail,
        );
    }
    public function render()
    {
        return view('livewire.manajemenstok.pengadaanbrgdagang.pelunasan.form');
    }
}
