<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function formatPhone($data)
    {
        // THIS NEEDS WORK

        if (preg_match('/^\+*\d*(\d{3})(\d{3})(\d{4})$/', $data, $matches)) {
            $result = $matches[1].'-'.$matches[2].'-'.$matches[3];

            return $result;
        } else {
            return $data;
        }
    }
}
