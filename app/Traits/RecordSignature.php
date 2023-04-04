<?php

namespace App\Traits;

use Auth;

trait RecordSignature
{
    protected static function boot()
    {
        parent::boot();

        if (Auth::user()) {
            static::updating(function ($model) {
                $model->updated_by = Auth::user()->id;
            });

            static::creating(function ($model) {
                $model->created_by = Auth::user()->id;
            });

            static::deleting(function ($model) {
                $model->deleted_by = Auth::user()->id;
            });
        }
    }
}
