<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <a href="{{ route('reviewer.dashboard') }}" class="text-xs text-[#004826] hover:text-[#002613] font-bold uppercase tracking-wider flex items-center mb-2 group">
                    <svg class="w-3.5 h-3.5 mr-1 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Reviewer Dashboard
                </a>
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                        {{ $innovation->title }}
                    </h2>
                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold uppercase tracking-wider border {{ $innovation->status->badgeClass() }} rounded">
                        {{ $innovation->status->label() }}
                    </span>
                </div>
            </div>

            <div class="text-xs text-gray-600 md:text-right">
                <div>Primary Author: <strong class="text-gray-900">{{ $innovation->author->name }}</strong></div>
                @if($innovation->author->department)
                    <div class="text-gray-500">{{ $innovation->author->department }}</div>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Success Message -->
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-600 border-t border-r border-b border-gray-200 text-emerald-900 text-sm font-medium">
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Left Column: Proposal Details & Attachments (7 Columns) -->
                <div class="lg:col-span-7 space-y-6">

                    <!-- Proposal Description -->
                    <div class="bg-white border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                        <div class="p-6 sm:p-8">
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-[#002613]">
                                    Full Innovation Proposal
                                </h3>
                                <span class="text-xs text-gray-400 font-mono">#{{ $innovation->id }}</span>
                            </div>
                            <div class="prose max-w-none text-gray-800 text-sm whitespace-pre-line leading-relaxed">
                                {{ $innovation->description }}
                            </div>
                        </div>
                    </div>

                    <!-- Supporting Attachments -->
                    <div class="bg-white border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                        <div class="p-6 sm:p-8">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-[#002613] mb-4 pb-3 border-b border-gray-200">
                                Attachments to Review ({{ $innovation->attachments->count() }})
                            </h3>

                            @if ($innovation->attachments->isEmpty())
                                <p class="text-xs text-gray-500 italic py-2">The innovator did not upload supporting attachments.</p>
                            @else
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach ($innovation->attachments as $att)
                                        <div class="p-3 bg-gray-50 border border-gray-300 rounded flex items-center justify-between hover:border-[#004826] transition">
                                            <div class="flex items-center space-x-3 truncate">
                                                <div class="w-8 h-8 rounded bg-[#004826] text-white flex items-center justify-center shrink-0">
                                                    @if ($att->isImage())
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    @elseif (str_contains($att->mime_type, 'pdf'))
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="truncate text-xs">
                                                    <div class="font-semibold text-gray-800 truncate" title="{{ $att->original_name }}">
                                                        {{ $att->original_name }}
                                                    </div>
                                                    <div class="text-gray-500">{{ $att->formattedSize() }}</div>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2 shrink-0 ms-2">
                                                <button onclick="previewAttachment('{{ $att->url() }}', '{{ $att->original_name }}', '{{ $att->mime_type }}')" class="px-3 py-2 bg-emerald-600 text-white text-xs font-semibold rounded hover:bg-emerald-700 transition" title="Preview">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                <a href="{{ $att->url() }}" target="_blank" download="{{ $att->original_name }}" class="px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded transition" title="Download">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Co-Innovators -->
                    @if ($innovation->collaborators->isNotEmpty())
                        <div class="bg-white border border-gray-300 p-6">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-3 pb-2 border-b border-gray-200">
                                Co-Authors & Collaborators
                            </h3>
                            <div class="space-y-2">
                                @foreach ($innovation->collaborators as $collab)
                                    <div class="flex items-center justify-between text-xs p-2.5 bg-gray-50 border border-gray-200">
                                        <div>
                                            <span class="font-semibold text-gray-800">{{ $collab->name }}</span>
                                            <span class="text-gray-500">({{ $collab->department ?? $collab->email }})</span>
                                        </div>
                                        <span class="px-2 py-0.5 bg-gray-200 text-gray-800 border border-gray-300 text-[11px] font-medium">
                                            {{ $collab->pivot->role_in_innovation ?? 'Collaborator' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Previous Evaluations History -->
                    @if ($innovation->reviews->isNotEmpty())
                        <div class="bg-white border border-gray-300 p-6">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-4 pb-2 border-b border-gray-200">
                                Past Evaluations On Record
                            </h3>
                            <div class="space-y-3">
                                @foreach ($innovation->reviews as $rev)
                                    <div class="p-4 bg-gray-50 border border-gray-300 text-xs">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="font-bold uppercase text-[10px] px-2 py-0.5 text-white {{ $rev->decision === 'approved' ? 'bg-emerald-700' : ($rev->decision === 'rejected' ? 'bg-rose-700' : 'bg-purple-700') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $rev->decision)) }}
                                                </span>
                                                <span class="font-bold text-gray-900">{{ $rev->reviewer->name }}</span>
                                            </div>
                                            <span class="font-bold text-amber-700">Score: {{ $rev->rating }}/5</span>
                                        </div>
                                        <p class="text-gray-800 whitespace-pre-line mt-1">{{ $rev->feedback }}</p>
                                        @if ($rev->internal_notes)
                                            <div class="mt-2 pt-2 border-t border-gray-200 text-gray-600 italic">
                                                <strong>Internal Note:</strong> {{ $rev->internal_notes }}
                                            </div>
                                        @endif
                                        <div class="text-[10px] text-gray-400 mt-2">{{ $rev->created_at->diffForHumans() }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Right Column: Inter-Reviewer Discussions & Evaluation Form (5 Columns) -->
                <div class="lg:col-span-5 space-y-6">

                    <!-- Inter-Reviewer Discussion Board -->
                    <div class="bg-white border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                        <div class="p-4 bg-gradient-to-r from-emerald-50 to-emerald-100 border-b border-gray-300 flex items-center justify-between">
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-[#002613] flex items-center">
                                    <svg class="w-4 h-4 me-1.5 text-[#004826]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    Reviewer Discussion Board
                                </h3>
                                <p class="text-[11px] text-gray-600 mt-0.5">
                                    Internal discussion visible only to reviewers & admins.
                                </p>
                            </div>
                            <span class="px-2.5 py-1 bg-[#c69214] text-white border border-[#e5a817] text-xs font-bold rounded">
                                {{ $innovation->reviewerComments->count() }}
                            </span>
                        </div>

                        <div class="p-5 space-y-4">
                            <!-- Comment Feed -->
                            @if ($innovation->reviewerComments->isEmpty())
                                <p class="text-xs text-gray-500 italic text-center py-3">
                                    No reviewer remarks yet. Share your preliminary thoughts below.
                                </p>
                            @else
                                <div class="space-y-3 max-h-64 overflow-y-auto pe-1">
                                    @foreach ($innovation->reviewerComments as $comment)
                                        <div class="p-3 bg-emerald-50/50 border border-emerald-200 rounded text-xs">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="font-bold text-gray-900">{{ $comment->author->name }}</span>
                                                <span class="text-[10px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="text-gray-800 whitespace-pre-line">{{ $comment->comment }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Post Comment Form -->
                            <form method="POST" action="{{ route('reviewer.comment.store', $innovation) }}" class="pt-3 border-t border-gray-200">
                                @csrf
                                <textarea name="comment" rows="2" class="w-full text-xs border border-gray-300 rounded focus:border-[#004826] focus:ring-0" placeholder="Leave a note or question for other committee reviewers..." required></textarea>
                                <x-input-error class="mt-1" :messages="$errors->get('comment')" />
                                <div class="mt-2 text-right">
                                    <button type="submit" class="px-4 py-2 bg-[#c69214] hover:bg-[#e5a817] text-white text-xs font-bold rounded transition">
                                        Post Note
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Formal Evaluation Form -->
                    <div class="bg-white border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                        <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 p-5 border-b border-gray-200">
                            <h3 class="text-sm font-bold text-[#002613]">
                                Submit Committee Decision
                            </h3>
                            <p class="text-xs text-gray-600 mt-1">
                                Rate the innovation and decide the lifecycle next step.
                            </p>
                        </div>

                        <div class="p-6">
                            <form method="POST" action="{{ route('reviewer.review.store', $innovation) }}" class="space-y-5">
                                @csrf

                                <!-- Rating (1 to 5) -->
                                <div>
                                    <x-input-label for="rating" :value="__('Innovation Rating (1 to 5 Stars) *')" />
                                    <select id="rating" name="rating" class="mt-1 block w-full text-xs border border-gray-300 rounded focus:border-[#004826] focus:ring-0 bg-white" required>
                                        <option value="">-- Select Score --</option>
                                        <option value="5" {{ old('rating') == '5' ? 'selected' : '' }}>★ ★ ★ ★ ★ (5/5) - Exceptional / Scalable</option>
                                        <option value="4" {{ old('rating') == '4' ? 'selected' : '' }}>★ ★ ★ ★ ☆ (4/5) - Strong / Recommended</option>
                                        <option value="3" {{ old('rating') == '3' ? 'selected' : '' }}>★ ★ ★ ☆ ☆ (3/5) - Promising / Needs Refinement</option>
                                        <option value="2" {{ old('rating') == '2' ? 'selected' : '' }}>★ ★ ☆ ☆ ☆ (2/5) - Marginal / Substantial Gaps</option>
                                        <option value="1" {{ old('rating') == '1' ? 'selected' : '' }}>★ ☆ ☆ ☆ ☆ (1/5) - Unsatisfactory</option>
                                    </select>
                                    <x-input-error class="mt-1" :messages="$errors->get('rating')" />
                                </div>

                                <!-- Decision Radios -->
                                <div>
                                    <x-input-label :value="__('Evaluation Decision *')" />
                                    <div class="mt-2 space-y-2 text-xs">
                                        <label class="flex items-center p-3 border border-gray-300 rounded hover:bg-emerald-50 cursor-pointer transition">
                                            <input type="radio" name="decision" value="approved" {{ old('decision') === 'approved' ? 'checked' : '' }} class="text-emerald-700 focus:ring-0" required>
                                            <div class="ms-3">
                                                <div class="font-bold text-emerald-800">Approve Innovation</div>
                                                <div class="text-gray-500 text-[11px]">Proposal meets KSG standards and proceeds for adoption.</div>
                                            </div>
                                        </label>

                                        <label class="flex items-center p-3 border border-gray-300 rounded hover:bg-purple-50 cursor-pointer transition">
                                            <input type="radio" name="decision" value="revision_requested" {{ old('decision') === 'revision_requested' ? 'checked' : '' }} class="text-purple-700 focus:ring-0">
                                            <div class="ms-3">
                                                <div class="font-bold text-purple-800">Request Revision</div>
                                                <div class="text-gray-500 text-[11px]">Send back to innovator to improve specific areas and resubmit.</div>
                                            </div>
                                        </label>

                                        <label class="flex items-center p-3 border border-gray-300 rounded hover:bg-rose-50 cursor-pointer transition">
                                            <input type="radio" name="decision" value="rejected" {{ old('decision') === 'rejected' ? 'checked' : '' }} class="text-rose-700 focus:ring-0">
                                            <div class="ms-3">
                                                <div class="font-bold text-rose-800">Reject Innovation</div>
                                                <div class="text-gray-500 text-[11px]">Does not meet public sector innovation criteria.</div>
                                            </div>
                                        </label>
                                    </div>
                                    <x-input-error class="mt-1" :messages="$errors->get('decision')" />
                                </div>

                                <!-- Feedback to Innovator -->
                                <div>
                                    <x-input-label for="feedback" :value="__('Feedback for Innovator * (Returned to Author)')" />
                                    <textarea id="feedback" name="feedback" rows="4" class="mt-1 block w-full text-xs border border-gray-300 rounded focus:border-[#004826] focus:ring-0" placeholder="Provide specific feedback, suggestions, or areas for improvement..." required>{{ old('feedback') }}</textarea>
                                    <p class="text-[11px] text-gray-500 mt-1">Minimum 10 characters. This will be visible to the author and collaborators.</p>
                                    <x-input-error class="mt-1" :messages="$errors->get('feedback')" />
                                </div>

                                <!-- Internal Reviewer Notes (Optional) -->
                                <div>
                                    <x-input-label for="internal_notes" :value="__('Internal Committee Remarks (Optional)')" />
                                    <textarea id="internal_notes" name="internal_notes" rows="2" class="mt-1 block w-full text-xs border border-gray-300 rounded focus:border-[#004826] focus:ring-0" placeholder="Confidential notes visible only to fellow reviewers and portal admins...">{{ old('internal_notes') }}</textarea>
                                    <x-input-error class="mt-1" :messages="$errors->get('internal_notes')" />
                                </div>

                                <!-- Submit Button -->
                                <div class="pt-4 border-t border-gray-200">
                                    <button type="submit" class="w-full px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-lg shadow-md transition">
                                        Submit Evaluation & Decision
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
