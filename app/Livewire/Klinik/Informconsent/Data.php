<?php

namespace App\Livewire\Klinik\Informconsent;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\InformConsent;
use App\Models\Registrasi;

class Data extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $tanggal;
    public $dataInformConsent;

    public function submitUploadInformConsent()
    {
        $this->validate([
            'file' => 'required|file|mimes:pdf',
        ]);
        $this->dataInformConsent->file = $this->file->store('inform_consent', 'public');
        $this->dataInformConsent->save();
    }

    public function print($id)
    {
        $cetak = view('livewire.klinik.informconsent.cetak', [
            'cetak' => true,
            'data' => Registrasi::find($id)
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function upload($id)
    {
        $this->dataInformConsent = InformConsent::find($id);
    }

    public function mount()
    {
        $this->tanggal = $this->tanggal ?: date('Y-m-d');
    }

    public function delete($id)
    {
        InformConsent::where('id', $id)->delete();
    }

    public function render()
    {
        return view('livewire.klinik.informconsent.data', [
            'data' => Registrasi::with('pasien')->with('nakes')->with('pengguna')->with('informConsent')
                ->whereHas('informConsent', fn($q) => $q->where('created_at', 'like', $this->tanggal . '%'))
                ->whereHas('pasien', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
                ->orderBy('urutan', 'asc')->paginate(10)
        ]);
    }
}
