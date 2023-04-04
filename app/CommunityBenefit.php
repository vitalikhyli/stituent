<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunityBenefit extends Model
{
    public function getValueTypePrettyAttribute()
    {
        if (strtolower($this->value_type) == 'both') {
            return 'Cash and In-Kind';
        }

        return $this->value_type;
    }

    public function partnerEntities()
    {
        return $this->belongsToMany(Entity::class, 'community_benefit_entity')
        			->withPivot(['partner', 'beneficiary', 'initiator', 'notes'])
                    ->wherePivot('parnter', true);
    }

    public function beneficiaryEntities()
    {
        return $this->belongsToMany(Entity::class, 'community_benefit_entity')
        			->withPivot(['partner', 'beneficiary', 'initiator', 'notes'])
                    ->wherePivot('beneficiary', true);
    }

    public function initiatorEntities()
    {
        return $this->belongsToMany(Entity::class, 'community_benefit_entity')
        			->withPivot(['partner', 'beneficiary', 'initiator', 'notes'])
                    ->wherePivot('initiator', true);
    }

    public function entities()
    {
        return $this->belongsToMany(Entity::class, 'community_benefit_entity')
        			->withPivot(['partner', 'beneficiary', 'initiator', 'notes']);
    }
}
