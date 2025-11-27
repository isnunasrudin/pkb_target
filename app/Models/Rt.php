<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rt extends Model
{
    protected $guarded = [];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function suara()
    {
        return $this->morphMany(Suara::class, 'suara');
    }

    public function voters()
    {
        return $this->hasMany(Voter::class);
    }
}
