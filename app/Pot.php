<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pot extends Model
{
    protected $table = 'pots';

    protected $fillable = [ 'raffle_date', 'is_drawn' ];

    /**
     * Get users
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'pot_user', 'pot_id', 'user_id')->withPivot('winning')->withTimestamps();
    }
}
