<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'url_identifier',
        'source_title',
    ];
    
    /**
     * Get the similarity matches for the source.
     */
    public function similarityMatches(): HasMany
    {
        return $this->hasMany(SimilarityMatch::class);
    }
}