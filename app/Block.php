<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
