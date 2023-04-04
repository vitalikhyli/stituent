<?php

namespace App\Http\Livewire;

use Auth;
use Livewire\Component;

class AppMain extends Component
{
	public $email;
	public $password;
	public $attempted = false;

	public $on_phone = false;
	public $approved = false;
	public $verified = false;

	public function onPhone()
	{
		$this->on_phone = !$this->on_phone;
	}
	public function approved()
	{
		$this->approved = !$this->approved;
	}
	public function logout()
	{
		if (Auth::user()) {
			Auth::logout();
		}
		$this->attempted = false;
		$this->approved = false;
	}
	public function login()
	{
		if (!Auth::check()) {
			if ($this->email && $this->password) {
				$username = $this->email;
				$fieldname = filter_var($username, FILTER_VALIDATE_EMAIL) 
									? 'email' : 'username';
				$this->attempted = true;
				Auth::attempt([$fieldname => $this->email, 'password' => $this->password]);
				$this->password = '';
			}
		}
	}
    public function render()
    {

        return view('livewire.app-main-simple');
    }
}
