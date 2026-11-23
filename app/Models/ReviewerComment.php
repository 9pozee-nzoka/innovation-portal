<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewerComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'innovation_id',
        'user_id',
        'comment',
    ];

    public function innovation(): BelongsTo
    {
        return $this->belongsTo(Innovation::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
