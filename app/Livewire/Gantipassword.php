<?php

namespace App\Livewire;

use App\Models\Pengguna;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Traits\CustomValidationTrait;

class Gantipassword extends Component
{
    use CustomValidationTrait;
    public $oldPassword, $newPassword;

    public function submit()
    {
        $this->validateWithCustomMessages([
            'newPassword' => 'required',
            'oldPassword' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, auth()->user()->password)) {
                    $fail("Invalid old password");
                }
            }],
        ]);

        Pengguna::where('id', auth()->id())->update([
            'password' => Hash::make($this->newPassword),
        ]);
        session()->flash('success', 'Berhasil menyimpan data');
        return $this->redirect('gantipassword');
    }


    public function render()
    {
        return view('livewire.gantipassword');
    }
}
