<?php

namespace App\Models;

use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use Illuminate\Database\Eloquent\Model;

class Tps extends Model
{
    use EagerLoadPivotTrait;
    
    protected $fillable = [
        'no_tps',
        'desa',
        'kecamatan',
        'address_id',
        'suara'
    ];
    
    public function suara()
    {
        return $this->morphMany(Suara::class, 'suara');
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function voters()
    {
        return $this->hasMany(Voter::class);
    } 
    
}
