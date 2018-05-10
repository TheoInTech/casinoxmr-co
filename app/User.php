<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $fillable = [
        'address',
        'payment_id',
        'reference_key',
        'referred_by',
        'email',
        'password',
        'first_name',
        'last_name',
        'contact_number',
        'country',
        'birth_date',
        'picture_url'
    ];

    /**
     * Get all transactions
     */
    public function transactions()
    {
        return $this->belongsToMany('App\Transaction', 'transaction_user', 'user_id', 'transaction_id')->withTimestamps();
    }

    /**
     * Get pots
     */
    public function pots()
    {
        return $this->belongsToMany('App\Pot', 'pot_user', 'user_id', 'pot_id')->withPivot('winning')->withTimestamps();
    }
}
