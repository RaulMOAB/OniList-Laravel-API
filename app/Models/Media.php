<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    use HasFactory;

    protected $table = 'medias';

    protected $fillable = [
        'title',
        'description',
        'cover_image',
        'extra_large_banner_image',
        'large_banner_image',
        'medium_banner_image',
        'format',
        'episodes',
        'chapters',
        'airing_status',
        'start_date',
        'end_state',
        'season',
        'season_year',
        'studio',
        'source',
        'genres',
        'romaji',
        'native',
        'trailer',
        'tags',
        'external_link',
        'type'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_subscribes', 'user_id', 'media_id');
    }

    public function related()
    {
        return $this->belongsToMany(Media::class,'related_to','media_id', 'related_media_id');
    }

    public function characters(): BelongsToMany
    {
        return $this->belongsToMany(Character::class, 'characters_appears_in', 'media_id', 'character_id');
    }
}
