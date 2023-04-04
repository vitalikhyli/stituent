<?php

namespace App\Models\CC;

use DB;
use Illuminate\Database\Eloquent\Model;

class NEUPhoneEmail extends Model
{
    protected $primaryKey = 'senecaID';
    protected $table = 'neu_seneca_phoneemail';

    // neu_seneca_addresses		senecaID
    // neu_seneca_casework		senecaID
    // neu_seneca_issueaction	senecaID
    // neu_seneca_phoneemail	senecaID

    public function getConnection()
    {
        if (env('APP_ENV') == 'production') {
            return DB::connection('cc_local');
        }

        return DB::connection('cc_remote');
    }
}
