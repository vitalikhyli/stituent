<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;

use App\Account;
use App\Team;


class ActiveSwitch extends Component
{
	public $model_type;
	public $model_id;
	public $size;

	public function toggleActive()
	{
    	if ($this->model_type == 'Account')  $model 	= Account::find($this->model_id);
    	if ($this->model_type == 'Team') 	$model 	= Team::find($this->model_id);

    	$model->active = !$model->active;
    	$model->save();
	}

    public function render()
    {
    	if ($this->model_type == 'Account') $model 	= Account::find($this->model_id);
    	if ($this->model_type == 'Team') 	$model 	= Team::find($this->model_id);

        return view('livewire.admin.active-switch', ['model' => $model]);
    }
}
