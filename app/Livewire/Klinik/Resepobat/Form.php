<?php

namespace App\Livewire\Klinik\Resepobat;

use Livewire\Component;
use App\Models\ResepObat;
use App\Models\Registrasi;
use Illuminate\Support\Facades\DB;
use App\Class\BarangClass;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $dataBarang = [];
    public $dataMetodeBayar = [];
    public $barang = [];
    public $keterangan;
    public $metode_bayar = "Cash";
    public $cash = 0;
    public $total_harga_barang = 0;
    public $diskon = 0;
    public $data;
    public $resep = [];


    public function copyResep($id)
    {
        $data = Registrasi::find($id)->resepobat;
        return collect($data)
            ->groupBy('resep')
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'catatan' => $first->catatan,
                    'nama' => $first->nama,
                    'barang' => $group->map(function ($r) {
                        return [
                            'id' => $r->barang_satuan_id,
                            'harga' => $r->harga,
                            'qty' => $r->qty,
                            'subtotal' => $r->harga * $r->qty,
                        ];
                    })->toArray(),
                ];
            })
            ->values()
            ->toArray();
    }

    public function submit()
    {
        if($this->data->pembayaran){
            return abort(404);
        }
        $this->validateWithCustomMessages([
            'resep' => 'required|array|min:1',
            'resep.*.barang' => 'required|array|min:1',
            'resep.*.barang.*.id' => 'required|integer',
            'resep.*.barang.*.qty' => 'required|integer|min:1',
        ]);

        DB::transaction(function () {
            $registrasiId = $this->data->getKey();

            // Delete all resepobat for this registrasi at once
            ResepObat::where('id', $registrasiId)->delete();

            $resepObatBatch = [];
            $userId = auth()->id();
            $now = now();

            foreach ($this->resep as $i => $resepRow) {
                $catatan = $resepRow['catatan'] ?? '';
                $nama = $resepRow['nama'] ?? '';
                foreach ($resepRow['barang'] as $barang) {
                    $resepObatBatch[] = [
                        'id' => $registrasiId,
                        'nama' => $nama,
                        'barang_id' => collect($this->dataBarang)->firstWhere('id', $barang['id'])['barang_id'],
                        'barang_satuan_id' => $barang['id'],
                        'resep' => $i + 1,
                        'qty' => $barang['qty'],
                        'qty_asli' => $barang['qty'],
                        'harga' => collect($this->dataBarang)->firstWhere('id', $barang['id'])['harga'],
                        'catatan' => $catatan,
                        'pengguna_id' => $userId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
            if (!empty($resepObatBatch)) {
                ResepObat::insert($resepObatBatch);
            }

            session()->flash('success', 'Berhasil menyimpan data');
        });

        return redirect('/klinik/resepobat');
    }

    public function mount(Registrasi $data)
    {
        $this->data = $data;
        if($this->data->pembayaran){
            return abort(404);
        }
        $this->dataBarang = BarangClass::getBarang('Apotek');
        $resepobat = $data->resepobat;
        if (!$resepobat || $resepobat->isEmpty()) {
            $this->resep[] = [
                'barang' => [],
                'catatan' => '',
            ];
        } else {
            $this->resep = collect($resepobat)
                ->groupBy('resep')
                ->map(function ($group) {
                    $first = $group->first();
                    return [
                        'catatan' => $first->catatan,
                        'nama' => $first->nama,
                        'barang' => $group->map(function ($r) {
                            return [
                                'id' => $r->barang_satuan_id,
                                'harga' => $r->harga,
                                'qty' => $r->qty,
                                'subtotal' => $r->harga * $r->qty,
                            ];
                        })->toArray(),
                    ];
                })
                ->values()
                ->toArray();
        }
    }

    public function render()
    {
        return view('livewire.klinik.resepobat.form');
    }
}
