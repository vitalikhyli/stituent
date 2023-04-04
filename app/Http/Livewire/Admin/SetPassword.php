<?php

namespace App\Http\Livewire\Admin;

use App\Traits\RandomWordsTrait;
use App\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class SetPassword extends Component
{
    use RandomWordsTrait;

    public $user_id;
    public $proposed_password;
    public $changed = false;

    public function mount($user_id)
    {
        $this->user_id = $user_id;
    }

    public function generatePassword()
    {
        $this->changed = false;
        $this->proposed_password = $this->randomNoun().$this->randomString(3);
    }

    public function setPassword()
    {
        $user = User::find($this->user_id);
        $user->password = Hash::make($this->proposed_password);
        $user->save();
        $this->changed = true;
    }

    public function randomString($length)
    {
        // $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*';
        // $pass = array(); //remember to declare $pass as an array
        // $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        // for ($i = 0; $i < $length; $i++) {
        //     $n = rand(0, $alphaLength);
        //     $pass[] = $alphabet[$n];
        // }
        for ($i = 0; $i < $length; $i++) {
            $pass[] = rand(1, 9);
        }

        return implode($pass); //turn the array into a string
    }

    public function render()
    {
        return view('livewire.admin.set-password');
    }
}
