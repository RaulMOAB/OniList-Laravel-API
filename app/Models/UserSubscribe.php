<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'start_dat',
        'end_date',
        'rewatches',
        'notes',
        'favourite',
        'private'
    ];
}
