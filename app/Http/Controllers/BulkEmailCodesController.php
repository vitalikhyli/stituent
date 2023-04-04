<?php

namespace App\Http\Controllers;

use App\BulkEmailCode;
use Illuminate\Http\Request;

class BulkEmailCodesController extends Controller
{
    public function delete($app_type, $code_id)
    {
        $code = BulkEmailCode::find($code_id);

        if ($code->emails->count() <= 0) {
            $code->delete();
        } else {

            // Error, can't orphan these emails
        }

        return redirect()->back();
    }
}
