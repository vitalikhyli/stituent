<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BulkEmailCode extends Model
{
    protected $table = 'bulk_email_code';

    public function emails()
    {
        return $this->HasMany(BulkEmail::class, 'bulk_email_code_id', 'id');
    }
}
