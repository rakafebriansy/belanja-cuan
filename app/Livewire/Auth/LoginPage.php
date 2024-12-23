<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Login')]
class LoginPage extends Component
{
    public $email;
    public $password;
    public function save()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if(!auth()->attempt([
            'email' => $this->email,
            'password' => $this->password,
        ])) {
            // $this->addError('password', 'These credentials don\'t match our records.');
            session()->flash('error','Invalid Credentials.');
            return;
        }

        return redirect()->intended();
    }
    public function render()
    {
        return view('livewire.auth.login-page');
    }
}
