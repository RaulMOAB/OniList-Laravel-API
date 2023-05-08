<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'romaji',
        'gender',
        'birthday',
        'age',
        'blood_type',
        'description',
        'image_large',
        'image_medium'
    ];
    protected $table = 'characters';

    public function medias():BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'characters_appears_in', 'media_id', 'character_id');
    }
}
