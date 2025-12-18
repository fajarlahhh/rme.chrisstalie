<?php

namespace App\Livewire\Klinik\Pemeriksaanawal;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\PemeriksaanAwal;
use App\Models\Registrasi;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $tanggal, $status = 1;
    
    public function mount()
    {
        $this->tanggal = $this->tanggal ?: date('Y-m-d');
    }

    public function updated()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        PemeriksaanAwal::where('id', $id)->delete();
    }

    public function render()
    {
        return view('livewire.klinik.pemeriksaanawal.index', [
            'data' => Registrasi::with('pasien')->with('nakes.pegawai')->with('pengguna.pegawai')->with('pemeriksaanAwal.pengguna.pegawai')->with('pembayaran')
                ->where('ketemu_dokter', 1)
                ->when($this->status == 2, fn($q) => $q->whereHas('pemeriksaanAwal', fn($q) => $q->where('created_at', 'like', $this->tanggal . '%')))
                ->when($this->status == 1, fn($q) => $q->whereDoesntHave('pemeriksaanAwal'))
                ->whereHas('pasien', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
                ->orderBy('id', 'asc')->paginate(10)
        ]);
    }
}
