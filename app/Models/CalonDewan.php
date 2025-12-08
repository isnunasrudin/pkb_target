<?php

namespace App\Models;

use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class CalonDewan extends Model implements Sortable
{
    use EagerLoadPivotTrait, SoftDeletes, SortableTrait;
    
    protected static function booted()
    {
        static::deleting(function ($calonDewan) {
            $calonDewan->suara()->delete();
        });

        static::restored(function ($calonDewan) {
            $calonDewan->suara()->withTrashed()->restore();
        });
    }
    
    
    protected $fillable = [
        'name',
        'dapil',
        'order',
    ];

    public function suara()
    {
        return $this->hasMany(Suara::class);
    }
}
