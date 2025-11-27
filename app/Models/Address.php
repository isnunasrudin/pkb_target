<?php

namespace App\Models;

use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use EagerLoadPivotTrait;
    
    protected $guarded = [];

    public function rt()
    {
        return $this->hasMany(Rt::class);
    }

    public function getDapilAttribute()
    {
        $dapils = config('kecamatan.dapil');
        return $dapils[$this->kecamatan] ?? null;
    }

    public function tps()
    {
        return $this->hasMany(Tps::class);
    }
}
