<?php

namespace App\Livewire\Member\Deposit;

use App\Models\Member;
use Livewire\Component;
use App\Models\MemberSaldo;
use App\Models\MetodeBayar;
use App\Class\JurnalkeuanganClass;
use Illuminate\Support\Facades\DB;
use App\Traits\CustomValidationTrait;
use App\Traits\KodeakuntransaksiTrait;

class Form extends Component
{
    use CustomValidationTrait;
    use KodeakuntransaksiTrait;
    public $data;
    public $dataMetodeBayar = [];
    public $metode_bayar = 1;
    public $jumlah = 0;
    public $catatan;
    public $tanggal;
    public $member_id;

    public function mount(Member $data)
    {
        $this->data = $data;
        $this->dataMetodeBayar = MetodeBayar::orderBy('nama')->get()->toArray();
    }

    public function submit()
    {
        $this->validateWithCustomMessages([
            'metode_bayar' => 'required',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
        ]);

        DB::transaction(function () {
            $memberSaldo = new MemberSaldo();
            $memberSaldo->member_id = $this->member_id;
            $memberSaldo->metode_bayar = collect($this->dataMetodeBayar)->where('id', $this->metode_bayar)->first()['nama'];
            $memberSaldo->masuk = $this->jumlah;
            $memberSaldo->uraian = $this->catatan;
            $memberSaldo->tanggal = $this->tanggal;
            $memberSaldo->pengguna_id = auth()->id();
            $memberSaldo->save();
            
            $this->jurnalKeuangan($memberSaldo, [
                [
                    'debet' => $this->jumlah,
                    'kredit' => 0,
                    'kode_akun_id' => collect($this->dataMetodeBayar)->where('id', $this->metode_bayar)->first()['kode_akun_id']
                ],
                [
                    'debet' => 0,
                    'kredit' => $this->jumlah,
                    'kode_akun_id' => $this->getKodeAkunTransaksiByTransaksi(['Deposit Member'])['kode_akun_id']
                ]
            ]);
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect('/member/deposit');
    }


    private function jurnalKeuangan($memberSaldo, $detail)
    {
        JurnalkeuanganClass::insert(
            jenis: 'Hutang',
            sub_jenis: 'Deposit Member',
            tanggal: $memberSaldo->tanggal,
            uraian: 'Deposit Member, ID : ' . $this->member_id . ' a/n ' . $memberSaldo->member->pasien->nama . ' Ket : ' . $memberSaldo->uraian,
            system: 1,
            foreign_key: 'member_saldo_id',
            foreign_id: $memberSaldo->id,
            detail: $detail
        );
    }

    public function render()
    {
        return view('livewire.member.deposit.form');
    }
}
