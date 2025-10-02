<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimilarityMatch extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'similarity_matches';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'report_id',
        'source_id',
        'original_text_fragment',
        'source_text_fragment',
        'fragment_similarity_percentage',
    ];

    /**
     * Get the report that this match belongs to.
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Get the source that this match belongs to.
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }
}
