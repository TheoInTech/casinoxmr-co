<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'chips',
        'hashes',
    ];
}
