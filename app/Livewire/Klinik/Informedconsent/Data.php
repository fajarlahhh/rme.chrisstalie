<?php

namespace App\Livewire\Klinik\Informedconsent;

use Livewire\Component;
use App\Models\Registrasi;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\InformedConsent;
use Illuminate\Support\Facades\Storage;
use App\Traits\CustomValidationTrait;

class Data extends Component
{
    use WithPagination, WithFileUploads, CustomValidationTrait;

    #[Url]
    public $cari, $tanggal, $file, $status = 1;
    public $dataInformConsent;

    public function submitInformedConsent($id)
    {
        $this->validateWithCustomMessages([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg',
        ]);
        $this->dataInformConsent->uploaded_at = now();
        $this->dataInformConsent->file = $this->file->store('informedconsent/' . date('Ymd') , 'public');
        $this->dataInformConsent->save();
        session()->flash('success', 'Berhasil menyimpan data');
        $this->redirect('/klinik/informedconsent/data');
    }

    public function print($id)
    {
        $cetak = view('livewire.klinik.informedconsent.cetak', [
            'cetak' => true,
            'data' => Registrasi::find($id)
        ])->render();
        session()->flash('cetak', $cetak);
    }

    public function uploadInformedConsent($id)
    {
        $this->dataInformConsent = InformedConsent::find($id);
    }

    public function mount()
    {
        $this->tanggal = $this->tanggal ?: date('Y-m-d');
    }

    public function delete($id)
    {
        InformedConsent::where('id', $id)->delete();
    }

    public function deleteInformedConsent($id)
    {
        $dataInformedConsent = InformedConsent::where('id', $id);
        if(Storage::delete($dataInformedConsent->first()->file)){
            $dataInformedConsent->update(['file' => null, 'uploaded_at' => null]);
        }
    }

    public function render()
    {
        return view('livewire.klinik.informedconsent.data', [
            'data' => Registrasi::with('pasien')->with('nakes')->with('pengguna')->with('informedConsent')
                ->whereHas('informedConsent')
                ->when($this->status == 2, fn($q) => $q->whereHas('informedConsent', fn($q) => $q->where('uploaded_at', 'like', $this->tanggal . '%')))
                ->when($this->status == 1, fn($q) => $q->whereHas('informedConsent', fn($q) => $q->whereNull('uploaded_at')))
                ->whereHas('pasien', fn($q) => $q->where('nama', 'like', '%' . $this->cari . '%'))
                ->orderBy('urutan', 'asc')->paginate(10)
        ]);
    }
}
