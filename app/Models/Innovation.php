<?php

namespace App\Models;

use App\Enums\InnovationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Innovation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'status',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => InnovationStatus::class,
            'submitted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Innovation $innovation) {
            if (empty($innovation->slug)) {
                $baseSlug = Str::slug($innovation->title);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $innovation->slug = $slug;
            }
        });
    }

    /**
     * Primary author of the innovation.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Co-innovators / Collaborators.
     */
    public function collaborators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'innovation_user')
            ->withPivot('role_in_innovation')
            ->withTimestamps();
    }

    /**
     * File attachments.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(InnovationAttachment::class);
    }

    /**
     * Reviews and evaluations.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    /**
     * Inter-reviewer discussion comments.
     */
    public function reviewerComments(): HasMany
    {
        return $this->hasMany(ReviewerComment::class)->latest();
    }

    /**
     * Status transition audit trail.
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(InnovationStatusHistory::class)->latest('created_at');
    }

    /**
     * Check if a given user is the author.
     */
    public function isAuthor(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * Check if a given user is an approved collaborator.
     */
    public function isCollaborator(User $user): bool
    {
        return $this->collaborators()->where('users.id', $user->id)->exists();
    }

    /**
     * Check if a user has edit access (Author or Collaborator) while status is editable.
     */
    public function canBeEditedBy(User $user): bool
    {
        if (! $this->status->isEditable()) {
            return false;
        }

        return $this->isAuthor($user) || $this->isCollaborator($user) || $user->hasRole('admin');
    }

    /**
     * Calculate average rating from all reviews.
     */
    public function averageRating(): ?float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? round((float) $avg, 1) : null;
    }
}
