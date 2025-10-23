<?php

namespace App\Livewire\Hakakses;

use App\Models\Pegawai;
use App\Models\Pengguna;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use App\Traits\CustomValidationTrait;

class Form extends Component
{
    use CustomValidationTrait;
    public $data, $previous, $dataRole = [], $pegawaiData = [];
    public $uid, $nama, $pegawai_id, $password, $role, $hakAkses = [];

    public function submit()
    {
        $this->validateWithCustomMessages([
            'hakAkses' => 'required',
            'role' => 'required',
            'uid' => 'required|unique:pengguna,uid,' . $this->data->id,
            'pegawai_id' => 'required',
        ]);

        DB::transaction(function () {
            if (!$this->data->exists) {
                if (Pengguna::where('uid', $this->uid)->withTrashed()->count() > 0) {
                    session()->flash('danger', 'uid ' . $this->uid . ' sudah ada');
                    return $this->render();
                }
                $this->data->uid = $this->uid;
                $this->data->password = Hash::make($this->uid);
            }
            $this->data->nama = $this->nama;
            $this->data->pegawai_id = $this->pegawai_id;
            $this->data->save();

            $this->data->syncPermissions($this->hakAkses);

            $this->data->syncRoles($this->role);
            session()->flash('success', 'Berhasil menyimpan data');
        });
        $this->redirect($this->previous);
    }

    public function mount(Pengguna $data)
    {
        $this->data = $data;
        if ($data->uid == 'administrator') {
            abort(404);
        }
        $this->previous = url()->previous();
        $this->dataRole = Role::all()->toArray();
        $this->pegawaiData = Pegawai::aktif()->orderBy('nama')->get()->toArray();
        $this->fill($this->data->toArray());
        $this->role = $this->data->getRoleNames()?->first();
        $this->hakAkses = $this->data->getPermissionNames()->toArray();
    }

    public function changeRole()
    {
        if ($this->role == 'administrator') {
            foreach (Permission::all() as $id => $subRow) {
                $this->hakAkses[] = $subRow->nama;
            }
        } else {
            // $this->hakAkses = [];
        }
    }

    public function resetKataSandi()
    {
        $this->data->password = Hash::make($this->uid);
        $this->data->save();

        session()->flash('success', 'Berhasil menyimpan data');
    }

    public function render()
    {
        return view('livewire.hakakses.form');
    }
}
