<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class People extends Model
{
    use HasFactory;
    protected $table = 'people';

    protected $fillable = [
        'name',
        'romaji',
        'gender',
        'date_of_birth',
        'date_of_death',
        'age',
        'years_active',
        'home_town',
        'blood_type',
        'description',
        'image_large',
        'image_medium'
    ];

    public function characters(): BelongsToMany
    {
        return $this->belongsToMany(Character::class, 'person_dubs_character', 'person_id', 'character_id');
    }

    public function medias(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'works_in', 'person_id', 'media_id');
    }
}
