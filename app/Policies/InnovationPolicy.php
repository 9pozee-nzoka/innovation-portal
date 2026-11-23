<?php

namespace App\Policies;

use App\Enums\InnovationStatus;
use App\Models\Innovation;
use App\Models\User;

class InnovationPolicy
{
    /**
     * Determine whether the user can view any innovations.
     */
    public function viewAny(User $user): bool
    {
        return true; // Any authenticated user can view their list
    }

    /**
     * Determine whether the user can view the innovation.
     */
    public function view(User $user, Innovation $innovation): bool
    {
        // Admins and reviewers can view all innovations
        if ($user->hasRole(['admin', 'reviewer'])) {
            return true;
        }

        // Innovators can view if they are the author or a collaborator
        return $innovation->isAuthor($user) || $innovation->isCollaborator($user);
    }

    /**
     * Determine whether the user can create innovations.
     */
    public function create(User $user): bool
    {
        // Only staff innovators submit innovation proposals.
        // Admins manage the portal and Reviewers evaluate; neither submits proposals.
        if ($user->hasRole(['admin', 'reviewer'])) {
            return false;
        }

        return $user->can('create innovations') || $user->hasRole('innovator');
    }

    /**
     * Determine whether the user can update the innovation.
     */
    public function update(User $user, Innovation $innovation): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        // Only author or collaborator can edit, and only when status allows editing
        return $innovation->canBeEditedBy($user);
    }

    /**
     * Determine whether the user can delete the innovation.
     */
    public function delete(User $user, Innovation $innovation): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        // Only author can delete, and only when still in draft
        return $innovation->isAuthor($user) && $innovation->status === InnovationStatus::DRAFT;
    }

    /**
     * Determine whether the user can submit or resubmit the innovation for review.
     */
    public function submit(User $user, Innovation $innovation): bool
    {
        if (! ($innovation->isAuthor($user) || $innovation->isCollaborator($user) || $user->hasRole('admin'))) {
            return false;
        }

        return in_array($innovation->status, [
            InnovationStatus::DRAFT,
            InnovationStatus::REVISION_REQUESTED,
        ], true);
    }

    /**
     * Determine whether the user can remove an attachment.
     */
    public function deleteAttachment(User $user, Innovation $innovation): bool
    {
        return $this->update($user, $innovation);
    }
}
