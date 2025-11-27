<?php

namespace App\Models;

use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use Illuminate\Database\Eloquent\Model;

class CalonDewan extends Model
{
    use EagerLoadPivotTrait;
    
    protected $fillable = [
        'name',
        'dapil',
    ];

    public function suara()
    {
        return $this->hasMany(Suara::class);
    }
}
