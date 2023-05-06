<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Relations extends Model
{
    use HasFactory;

    protected $table = 'related_to';

    protected $fillable = [
        'media_id',
        'related_media_id',
        'relationship_type',
    ];
    
}
