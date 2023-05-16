<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class UserSubscribe extends Model
{
    use HasFactory;
    protected $table = 'user_subscribes';

    protected $fillable = [
        'user_id',
        'media_id',
        'status',
        'rate',
        'progress',
        'start_date',
        'end_date',
        'rewatches',
        'notes',
        'favorite',
        'private'
    ];


    // protected $casts = [
    //     'start_date' => 'string'
    // ];
}
