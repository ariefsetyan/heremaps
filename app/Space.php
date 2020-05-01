<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
    protected $guarded = [];
    public function photos()
    {
        return $this->hasMany('App\SpacePhoto', 'space_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    public function getSpaces($latitude, $longitude, $radius)
    {
        return $this->select('spaces.*')
            ->selectRaw(
                '( 6371 *
                    acos( cos( radians(?) ) *
                        cos( radians( latitude ) ) *
                        cos( radians(longitude ) - radians(?)) +
                        sin( radians(?) ) *
                        sin( radians( latitude ) )
                    )
                ) AS distance', [$latitude, $longitude, $latitude]
            )
            ->havingRaw("distance < ?", [$radius])
            ->orderBy('distance', 'asc');
    }
}
