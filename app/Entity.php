<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// Tables needed
// case_entity
// contact_entity
// group_entity
// relationships

class Entity extends Model
{
    use SoftDeletes;
    
    protected $table = 'entities';

    protected $casts = ['contact_info' => 'array'];

    public function communityBenefits()
    {
        return $this->belongsToMany(CommunityBenefit::class)->withPivot(['partner', 'beneficiary', 'initiator', 'notes']);
    }

    public function cases()
    {
      return $this->belongsToMany(WorkCase::class, 'entity_cases', 'entity_id', 'case_id');
    }
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function notes()
    {
        return $this->belongsToMany(Contact::class)->withTimestamps();
    }

    public function groupEntities()
    {
        return $this->hasMany(GroupEntity::class);
    }

    public function partnerships()
    {
        return $this->hasMany(Partnership::class, 'partner_id');
    }

    public function departmentPartnerships()
    {
        return $this->hasMany(Partnership::class, 'department_id');
    }

    public function partnershipTypes()
    {
        return PartnershipType::select(DB::raw('partnership_types.*'))->join('partnerships', 'partnership_types.id', 'partnership_type_id')->where('partnerships.partner_id', $this->id)->groupBy('partnership_types.id')->get();
    }

    public function geturlWithHttpAttribute()
    {
        if (substr($this->url, 0, 7) != 'http://') {
            return 'http://'.$this->url;
        }

        return $this->url;
    }

    public function getFullAddressAttribute($full_address)
    {
        if (! $full_address) {
            if ($this->address_raw) {
                $full_address = $this->address_raw;
                $full_address .= "\n".$this->address_city;
                $full_address .= ', '.$this->address_state;
                $full_address .= ' '.$this->address_zip;
            }
        }

        return $full_address;
    }

    public function people()
    {
        return $this->belongsToMany(Person::class)->withPivot('relationship', 'id');
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class);
    }

    public function related_people()
    {
        $people = DB::table('relationships')->select('people.*',
                                                    'relationships.kind',
                                                    'relationships.id as relationship_id')
                                              ->where('subject_id', $this->id)
                                              ->where('subject_type', 'e')
                                              ->where('object_type', 'p')
                                              ->join('people', 'relationships.object_id', 'people.id')
                                              ->orderBy('people.last_name')
                                              ->get();

        return $people;
    }

    public function related_people_reverse()
    {
        $people = DB::table('relationships')->select('people.*',
                                                    'relationships.kind',
                                                    'relationships.id as relationship_id')
                                              ->where('object_id', $this->id)
                                              ->where('subject_type', 'p')
                                              ->where('object_type', 'e')
                                              ->join('people', 'relationships.subject_id', 'people.id')
                                              ->orderBy('people.last_name')
                                              ->get();

        return $people;
    }

    public function related_entities()
    {
        $entities = DB::table('relationships')->select('entities.*',
                                                    'relationships.kind',
                                                    'relationships.id as relationship_id')
                                              ->where('subject_id', $this->id)
                                              ->where('subject_type', 'e')
                                              ->where('object_type', 'e')
                                              ->join('entities', 'relationships.object_id', 'entities.id')
                                              ->orderBy('entities.name')
                                              ->get();

        return $entities;
    }

    public function related_entities_reverse()
    {
        $entities = DB::table('relationships')->select('entities.*',
                                                    'relationships.kind',
                                                    'relationships.id as relationship_id')
                                              ->where('object_id', $this->id)
                                              ->where('subject_type', 'e')
                                              ->where('object_type', 'e')
                                              ->join('entities', 'relationships.subject_id', 'entities.id')
                                              ->orderBy('entities.name')
                                              ->get();

        return $entities;
    }

    public function generateFullAddress()
    {
        $full_address = preg_replace('!\s+!', ' ', //Remove >1 spaces
              titleCase(
                (($this->address_number == 0) ? '' : $this->address_number).' '.
                $this->address_fraction.' '.
                $this->address_street.' '.
                $this->address_apt.' '.
                $this->address_city
              ).' '.$this->address_state.' '.$this->address_zip);

        return $full_address;
    }

    public function generateHouseholdId()
    {
        return strtoupper(substr($this->address_state, 0, 2).'|'.
              Str::slug(str_pad($this->address_city, 15, '0', STR_PAD_RIGHT)).'|'.
              Str::slug(str_pad($this->address_street, 20, '0', STR_PAD_RIGHT)).'|'.
              Str::slug(str_pad($this->address_number, 8, '0', STR_PAD_LEFT)).'|'.
              Str::slug(str_pad($this->address_fraction, 5, '0', STR_PAD_LEFT)).'|'.
              Str::slug(str_pad($this->address_apt, 7, '0', STR_PAD_LEFT))
            );
    }

    //////////////////////////////////////////////////////////////////////////////

    public function getPrivateAttribute($value)
    {
        for ($i = 0; $i < 3; $i++) {
            $value = base64_decode($value);
        }

        return $value;
    }

    public function setPrivateAttribute($value)
    {
        for ($i = 0; $i < 3; $i++) {
            $value = base64_encode($value);
        }
        $this->attributes['private'] = $value;
    }
}
