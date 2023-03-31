<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

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
}
