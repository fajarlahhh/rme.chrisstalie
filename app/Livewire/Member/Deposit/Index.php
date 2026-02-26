<?php

namespace App\Livewire\Member\Deposit;

use Livewire\Component;
use App\Models\Member;
use App\Models\MemberSaldo;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $tanggal1, $tanggal2, $cari;

    public function mount()
    {
        $this->tanggal1 = $this->tanggal1 ?: date('Y-m-01');
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
        $query = MemberSaldo::whereBetween('created_at', [$this->tanggal1 . ' 00:00:00', $this->tanggal2 . ' 23:59:59'])->with('member.pasien')->whereNotNull('metode_bayar')->orderBy('created_at', 'desc')
            ->where(fn($q) => $q
                ->where('member_id', 'like', '%' . $this->cari . '%')
                ->orWhereHas('member', fn($r) => $r->whereHas('pasien', fn($r) => $r->where('nama', 'like', '%' . $this->cari . '%'))));
        return $paginate ? $query->paginate(10) : $query;
    }

    public function render()
    {
        return view('livewire.member.deposit.index', [
            'data' => $this->getData(true),
        ]);
    }
}
