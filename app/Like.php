<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    public function tweet()
    {
        return $this->belongsTo(Tweet::class);
    }
}
