<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    protected $fillable = [
        'name',
        'nik',
        'gender',
        'tps_id',
        'rt_id'
    ];

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function tps()
    {
        return $this->belongsTo(Tps::class);
    }
}
