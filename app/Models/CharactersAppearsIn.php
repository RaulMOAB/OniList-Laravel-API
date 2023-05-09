<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharactersAppearsIn extends Model
{
    use HasFactory;
    protected $table = 'characters_appears_in';

    protected $fillable = [
        'media_id',
        'character_id',
        'role'
    ];
}
