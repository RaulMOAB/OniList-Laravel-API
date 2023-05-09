<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dubbers extends Model
{
    use HasFactory;
    protected $table = 'person_dubs_character';

    protected $fillable = [
        'person_id',
        'character_id',        
    ];
}
