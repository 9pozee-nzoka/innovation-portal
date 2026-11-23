<?php

namespace App\Http\Controllers;

use App\Enums\InnovationStatus;
use App\Http\Requests\StoreInnovationRequest;
use App\Http\Requests\UpdateInnovationRequest;
use App\Models\Innovation;
use App\Models\InnovationAttachment;
use App\Models\User;
use App\Services\InnovationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class InnovationController extends Controller
{
    public function __construct(
        protected InnovationService $innovationService
    ) {}

    /**
     * Display a listing of innovations accessible to the current user.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Innovation::class);

        $user = $request->user();
        $selectedStatus = $request->query('status');

        $query = Innovation::query()
            ->with(['author', 'attachments'])
            ->withCount(['reviews', 'collaborators'])
            ->latest();

        // If not admin/reviewer, restrict to authored or collaborated innovations
        if (! $user->hasRole(['admin', 'reviewer'])) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('collaborators', fn ($collab) => $collab->where('users.id', $user->id));
            });
        }

        if ($selectedStatus && InnovationStatus::tryFrom($selectedStatus)) {
            $query->where('status', $selectedStatus);
        }

        $innovations = $query->paginate(10)->withQueryString();

        return view('innovations.index', [
            'innovations' => $innovations,
            'selectedStatus' => $selectedStatus,
            'statuses' => InnovationStatus::cases(),
        ]);
    }

    /**
     * Show the form for creating a new innovation.
     */
    public function create(): View
    {
        Gate::authorize('create', Innovation::class);

        $potentialCollaborators = User::where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name', 'department', 'email']);

        return view('innovations.create', [
            'potentialCollaborators' => $potentialCollaborators,
        ]);
    }

    /**
     * Store a newly created innovation in storage.
     */
    public function store(StoreInnovationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $files = $request->file('attachments') ?? [];
        $collaborators = $validated['collaborators'] ?? [];

        $innovation = $this->innovationService->create(
            author: $request->user(),
            data: $validated,
            files: $files,
            collaborators: $collaborators
        );

        return redirect()
            ->route('innovations.show', $innovation)
            ->with('status', 'Innovation draft created successfully!');
    }

    /**
     * Display the specified innovation.
     */
    public function show(Innovation $innovation): View
    {
        Gate::authorize('view', $innovation);

        $innovation->load([
            'author',
            'collaborators',
            'attachments.uploader',
            'reviews.reviewer',
            'reviewerComments.author',
            'statusHistories.actor',
        ]);

        return view('innovations.show', [
            'innovation' => $innovation,
        ]);
    }

    /**
     * Show the form for editing the specified innovation.
     */
    public function edit(Innovation $innovation): View
    {
        Gate::authorize('update', $innovation);

        $potentialCollaborators = User::where('id', '!=', $innovation->user_id)
            ->orderBy('name')
            ->get(['id', 'name', 'department', 'email']);

        $innovation->load(['collaborators', 'attachments']);

        return view('innovations.edit', [
            'innovation' => $innovation,
            'potentialCollaborators' => $potentialCollaborators,
        ]);
    }

    /**
     * Update the specified innovation in storage.
     */
    public function update(UpdateInnovationRequest $request, Innovation $innovation): RedirectResponse
    {
        $validated = $request->validated();
        $files = $request->file('attachments') ?? [];
        $collaborators = $validated['collaborators'] ?? [];

        $this->innovationService->update(
            innovation: $innovation,
            actor: $request->user(),
            data: $validated,
            files: $files,
            collaborators: $collaborators
        );

        return redirect()
            ->route('innovations.show', $innovation)
            ->with('status', 'Innovation updated successfully!');
    }

    /**
     * Submit an innovation for review.
     */
    public function submit(Request $request, Innovation $innovation): RedirectResponse
    {
        Gate::authorize('submit', $innovation);

        $request->validate([
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->innovationService->submit(
            innovation: $innovation,
            actor: $request->user(),
            remarks: $request->input('remarks')
        );

        return redirect()
            ->route('innovations.show', $innovation)
            ->with('status', 'Innovation has been submitted for evaluation!');
    }

    /**
     * Remove the specified innovation from storage (drafts only).
     */
    public function destroy(Innovation $innovation): RedirectResponse
    {
        Gate::authorize('delete', $innovation);

        $innovation->delete();

        return redirect()
            ->route('innovations.index')
            ->with('status', 'Draft innovation deleted.');
    }

    /**
     * Remove an attachment from an innovation.
     */
    public function destroyAttachment(Innovation $innovation, InnovationAttachment $attachment): RedirectResponse
    {
        Gate::authorize('deleteAttachment', $innovation);

        if ($attachment->innovation_id !== $innovation->id) {
            abort(404);
        }

        $this->innovationService->deleteAttachment($attachment);

        return back()->with('status', 'Attachment removed successfully.');
    }
}
