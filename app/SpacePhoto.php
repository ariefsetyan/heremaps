<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpacePhoto extends Model
{
    protected $guarded = [];
    public function space()
    {
        return $this->belongsTo('App\Space', 'space_id', 'id');
    }
}
