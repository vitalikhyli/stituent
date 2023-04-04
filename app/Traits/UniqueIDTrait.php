<?php 
namespace App\Traits;

use Carbon\Carbon;

use App\CampaignListUser;

use Illuminate\Support\Str;


trait UniqueIDTrait
{

    public function createUniqueID()
    {
        $check = true;
        while ($check == true) {
            $uuid = (string) Str::uuid();
            if (!CampaignListUser::where('uuid', $uuid)->exists()) $check = false;
        }
        return $uuid;
    }

}   