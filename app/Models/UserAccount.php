<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    protected $fillable = [
        'user_id',
        'operation',
        'amount',
        'comment',
        'balance',
    ];

    protected $casts = [
        'amount' => 'float',
        'balance' => 'float',
    ];
}
