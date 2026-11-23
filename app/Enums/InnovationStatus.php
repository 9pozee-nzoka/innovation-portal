<?php

namespace App\Enums;

enum InnovationStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case UNDER_REVIEW = 'under_review';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case REVISION_REQUESTED = 'revision_requested';
    case RESUBMITTED = 'resubmitted';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Submitted',
            self::UNDER_REVIEW => 'Under Review',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::REVISION_REQUESTED => 'Revision Requested',
            self::RESUBMITTED => 'Resubmitted',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-gray-100 text-gray-800 border-gray-300',
            self::SUBMITTED => 'bg-blue-100 text-blue-800 border-blue-300',
            self::UNDER_REVIEW => 'bg-amber-100 text-amber-800 border-amber-300',
            self::APPROVED => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            self::REJECTED => 'bg-rose-100 text-rose-800 border-rose-300',
            self::REVISION_REQUESTED => 'bg-purple-100 text-purple-800 border-purple-300',
            self::RESUBMITTED => 'bg-indigo-100 text-indigo-800 border-indigo-300',
        };
    }

    public function isEditable(): bool
    {
        return in_array($this, [self::DRAFT, self::REVISION_REQUESTED], true);
    }

    public function isReviewable(): bool
    {
        return in_array($this, [self::SUBMITTED, self::UNDER_REVIEW, self::RESUBMITTED], true);
    }
}
