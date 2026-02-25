<?php

namespace App\Livewire\Member\Deposit;

use Livewire\Component;
use App\Models\Member;
use App\Models\MemberSaldo;

class Index extends Component
{
    public $tanggal1, $tanggal2;

    public function mount()
    {
        $this->tanggal1 = $this->tanggal1 ?: date('Y-m-d');
        $this->tanggal2 = $this->tanggal2 ?: date('Y-m-d');
    }

    public function delete($id)
    {
        $data = MemberSaldo::find($id);
        $data->delete();
        session()->flash('success', 'Berhasil menghapus data');
    }

    public function getData($paginate = true)
    {
        $query = MemberSaldo::whereBetween('created_at', [$this->tanggal1 . ' 00:00:00', $this->tanggal2 . ' 23:59:59'])->with('member')->whereNotNull('metode_bayar')->orderBy('created_at', 'desc');
        return $paginate ? $query->paginate(10) : $query;
    }

    public function render()
    {
        return view('livewire.member.deposit.index', [
            'data' => $this->getData(true),
        ]);
    }
}
