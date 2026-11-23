<?php

namespace App\Services;

use App\Enums\InnovationStatus;
use App\Models\Innovation;
use App\Models\InnovationAttachment;
use App\Models\InnovationStatusHistory;
use App\Models\User;
use App\Notifications\InnovationSubmittedNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class InnovationService
{
    /**
     * Create a new draft innovation.
     *
     * @param array{title: string, description: string} $data
     * @param array<UploadedFile> $files
     * @param array<int, array{user_id: int, role: string}> $collaborators
     */
    public function create(User $author, array $data, array $files = [], array $collaborators = []): Innovation
    {
        return DB::transaction(function () use ($author, $data, $files, $collaborators) {
            $innovation = Innovation::create([
                'user_id' => $author->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => InnovationStatus::DRAFT,
                'submitted_at' => null,
            ]);

            // Save Attachments
            $this->storeAttachments($innovation, $author, $files);

            // Sync Collaborators
            $this->syncCollaborators($innovation, $collaborators);

            // Record initial audit history
            InnovationStatusHistory::create([
                'innovation_id' => $innovation->id,
                'changed_by_user_id' => $author->id,
                'from_status' => InnovationStatus::DRAFT,
                'to_status' => InnovationStatus::DRAFT,
                'remarks' => 'Draft innovation created.',
                'created_at' => now(),
            ]);

            return $innovation;
        });
    }

    /**
     * Update an existing innovation.
     */
    public function update(Innovation $innovation, User $actor, array $data, array $files = [], array $collaborators = []): Innovation
    {
        return DB::transaction(function () use ($innovation, $actor, $data, $files, $collaborators) {
            $innovation->update([
                'title' => $data['title'],
                'description' => $data['description'],
            ]);

            if (! empty($files)) {
                $this->storeAttachments($innovation, $actor, $files);
            }

            if (! empty($collaborators)) {
                $this->syncCollaborators($innovation, $collaborators);
            }

            return $innovation->fresh(['attachments', 'collaborators']);
        });
    }

    /**
     * Submit or resubmit an innovation for review.
     */
    public function submit(Innovation $innovation, User $actor, ?string $remarks = null): Innovation
    {
        $innovation = DB::transaction(function () use ($innovation, $actor, $remarks) {
            $previousStatus = $innovation->status;
            
            // If it was revision_requested, it becomes resubmitted, else submitted
            $newStatus = ($previousStatus === InnovationStatus::REVISION_REQUESTED)
                ? InnovationStatus::RESUBMITTED
                : InnovationStatus::SUBMITTED;

            $innovation->update([
                'status' => $newStatus,
                'submitted_at' => now(),
            ]);

            InnovationStatusHistory::create([
                'innovation_id' => $innovation->id,
                'changed_by_user_id' => $actor->id,
                'from_status' => $previousStatus,
                'to_status' => $newStatus,
                'remarks' => $remarks ?? ($newStatus === InnovationStatus::RESUBMITTED ? 'Resubmitted after addressing requested revisions.' : 'Initial submission for evaluation.'),
                'created_at' => now(),
            ]);

            return $innovation;
        });

        // Notify Reviewers of new submission
        $reviewers = User::role(['reviewer', 'admin'])->get();
        if ($reviewers->isNotEmpty()) {
            Notification::send($reviewers, new InnovationSubmittedNotification($innovation));
        }

        return $innovation;
    }

    /**
     * Store uploaded attachments for an innovation.
     *
     * @param array<UploadedFile> $files
     */
    public function storeAttachments(Innovation $innovation, User $uploader, array $files): void
    {
        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store("innovations/{$innovation->id}", 'public');

            InnovationAttachment::create([
                'innovation_id' => $innovation->id,
                'user_id' => $uploader->id,
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType() ?: 'application/octet-stream',
                'file_size' => $file->getSize(),
            ]);
        }
    }

    /**
     * Delete a single attachment.
     */
    public function deleteAttachment(InnovationAttachment $attachment): void
    {
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();
    }

    /**
     * Sync collaborator users with their respective roles.
     *
     * @param array<int, array{user_id: int, role: string}> $collaborators
     */
    protected function syncCollaborators(Innovation $innovation, array $collaborators): void
    {
        $syncData = [];
        foreach ($collaborators as $collab) {
            if (! empty($collab['user_id']) && (int) $collab['user_id'] !== (int) $innovation->user_id) {
                $syncData[$collab['user_id']] = [
                    'role_in_innovation' => $collab['role'] ?? 'Collaborator',
                ];
            }
        }

        $innovation->collaborators()->sync($syncData);
    }
}
