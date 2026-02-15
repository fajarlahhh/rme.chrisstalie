<?php

namespace App\Livewire\Datamaster\Pegawai;

use Livewire\Component;
use App\Models\Pengguna;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\KepegawaianPegawai;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public $cari, $status = 'Aktif';

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            try {
                KepegawaianPegawai::findOrFail($id)
                    ->forceDelete();
            } catch (\Throwable $th) {
                KepegawaianPegawai::where('id', $id)
                    ->update([
                        'status' => 'Non Aktif',
                    ]);
            }
            Pengguna::where('kepegawaian_pegawai_id', $id)->delete();
        });
    }

    public function restore($id)
    {
        KepegawaianPegawai::withTrashed()
            ->findOrFail($id)
            ->restore();
    }

    public function render()
    {
        return view('livewire.datamaster.pegawai.index', [
            'data' => KepegawaianPegawai::where(fn($q) => $q
                ->where('nama', 'like', '%' . $this->cari . '%')
                ->where('status', $this->status))
                ->with('pengguna')
                ->orderBy('nama')
                ->paginate(10)
        ]);
    }
}
