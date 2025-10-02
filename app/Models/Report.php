<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'analysis_id',
        'total_similarity_percentage',
        'executive_summary',
    ];

    /**
     * Get the analysis that this report belongs to.
     */
    public function analysis(): BelongsTo
    {
        return $this->belongsTo(Analysis::class);
    }

    /**
     * Get the similarity matches for the report.
     */
    public function similarityMatches(): HasMany
    {
        return $this->hasMany(SimilarityMatch::class);
    }
}

