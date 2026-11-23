<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'department', 'phone', 'otp_code', 'otp_expires_at', 'reviewer_status', 'reviewer_approved_at'])]
#[Hidden(['password', 'remember_token', 'otp_code'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'reviewer_approved_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is a reviewer applicant awaiting administrator approval.
     */
    public function isPendingReviewer(): bool
    {
        return $this->reviewer_status === 'pending';
    }

    /**
     * Check if user is an approved reviewer.
     */
    public function isApprovedReviewer(): bool
    {
        return $this->reviewer_status === 'approved' && $this->hasRole('reviewer');
    }

    /**
     * Innovations authored by the user.
     */
    public function innovations(): HasMany
    {
        return $this->hasMany(Innovation::class, 'user_id');
    }

    /**
     * Innovations where the user is a collaborator.
     */
    public function collaborations(): BelongsToMany
    {
        return $this->belongsToMany(Innovation::class, 'innovation_user')
            ->withPivot('role_in_innovation')
            ->withTimestamps();
    }

    /**
     * Reviews submitted by this reviewer.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    /**
     * Reviewer comments posted by this user.
     */
    public function reviewerComments(): HasMany
    {
        return $this->hasMany(ReviewerComment::class, 'user_id');
    }
}
