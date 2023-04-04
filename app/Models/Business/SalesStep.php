<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class SalesStep extends Model
{
    protected $table = 'sales_steps';

    public function user()
    {
        return $this->belongsTo(\SalesPattern::class, 'id', 'pattern_id');
    }

    public function fulfilled($sales_entity)
    {
        $entity_contacts = $sales_entity->entity->contacts->pluck('id')->toArray();

        $sales_contacts = SalesContact::where('step', $this->name)
                                      ->whereIn('contact_id',
                                        $entity_contacts
                                      )->first();

        if ($sales_contacts) {
            return true;
        }
    }
}
