<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Traits\CustomValidationTrait;

class Login extends Component
{
    use CustomValidationTrait;
    public $uid, $password, $remember;

    public function login() {
        $this->validateWithCustomMessages([
            "uid" => "required|min:3",
            "password" => "required"
        ]);
        
        if (Auth::attempt(['uid' => $this->uid, 'password' => $this->password], $this->remember)) {
            return $this->redirect('/');
        } else {
            session()->flash('danger', 'Invalid Credential');
        }
    }

    public function render()
    {
        return view('livewire.login');
    }
}
