<?php

namespace App\Models;

use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Suara extends Model
{
    use EagerLoadPivotTrait, SoftDeletes;
    
    protected $fillable = [
        'suara_id',
        'suara_type',
        'calon_dewan_id',
        'total',
        'target',
    ];

    public function calonDewan()
    {
        return $this->belongsTo(CalonDewan::class);
    }

    public function suara()
    {
        return $this->morphTo();
    }
}
