<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <a href="{{ route('innovations.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold uppercase tracking-wider flex items-center mb-1">
                    ← Back to All Innovations
                </a>
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        {{ $innovation->title }}
                    </h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $innovation->status->badgeClass() }}">
                        {{ $innovation->status->label() }}
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('update', $innovation)
                    <a href="{{ route('innovations.edit', $innovation) }}" class="inline-flex items-center px-3.5 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 transition shadow-xs">
                        <svg class="w-4 h-4 me-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Innovation
                    </a>
                @endcan

                @can('submit', $innovation)
                    <form method="POST" action="{{ route('innovations.submit', $innovation) }}" onsubmit="return confirm('Ready to submit this innovation for committee evaluation?');">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 rounded-lg text-xs font-semibold text-white transition shadow-xs">
                            <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $innovation->status->value === 'revision_requested' ? 'Resubmit Innovation' : 'Submit for Review' }}
                        </button>
                    </form>
                @endcan

                @can('delete', $innovation)
                    <form method="POST" action="{{ route('innovations.destroy', $innovation) }}" onsubmit="return confirm('Are you sure you want to delete this draft? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3.5 py-2 bg-rose-50 border border-rose-200 rounded-lg text-xs font-semibold text-rose-700 hover:bg-rose-100 transition shadow-xs">
                            Delete Draft
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Success Message -->
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-md shadow-sm">
                    <span class="text-sm font-medium">{{ session('status') }}</span>
                </div>
            @endif

            <!-- Revision Requested Notice Banner -->
            @if ($innovation->status->value === 'revision_requested')
                <div class="p-5 bg-purple-50 border-l-4 border-purple-500 rounded-xl text-purple-900 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="font-bold text-sm text-purple-900 flex items-center">
                                <svg class="w-5 h-5 me-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Revisions Requested by Evaluation Committee
                            </h4>
                            <p class="text-xs text-purple-800 mt-1">
                                The reviewer committee evaluated this innovation and requested specific improvements before approval. Review their feedback below, edit your proposal, and click <strong>Resubmit Innovation</strong>.
                            </p>
                        </div>
                        @can('update', $innovation)
                            <a href="{{ route('innovations.edit', $innovation) }}" class="shrink-0 px-3 py-1.5 bg-purple-700 text-white rounded-lg text-xs font-semibold hover:bg-purple-800 transition">
                                Edit & Revise Now
                            </a>
                        @endcan
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Main Content (2 Columns) -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Proposal Description -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sm:p-8">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">
                            Proposal Overview & Objectives
                        </h3>
                        <div class="prose max-w-none text-gray-700 text-sm whitespace-pre-line leading-relaxed">
                            {{ $innovation->description }}
                        </div>
                    </div>

                    <!-- Supporting Attachments -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sm:p-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                                Supporting Attachments ({{ $innovation->attachments->count() }})
                            </h3>
                            @can('update', $innovation)
                                <a href="{{ route('innovations.edit', $innovation) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">
                                    + Add or manage files
                                </a>
                            @endcan
                        </div>

                        @if ($innovation->attachments->isEmpty())
                            <p class="text-xs text-gray-400 italic py-4">No attachments uploaded for this innovation.</p>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach ($innovation->attachments as $att)
                                    <div class="p-3.5 bg-gray-50 border border-gray-200 rounded-lg flex items-center justify-between hover:bg-gray-100 transition">
                                        <div class="flex items-center space-x-3 truncate">
                                            <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 font-bold text-xs">
                                                @if ($att->isImage())
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                @elseif (str_contains($att->mime_type, 'pdf'))
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="truncate text-xs">
                                                <div class="font-semibold text-gray-800 truncate" title="{{ $att->original_name }}">
                                                    {{ $att->original_name }}
                                                </div>
                                                <div class="text-gray-400">
                                                    {{ $att->formattedSize() }}
                                                    @if($att->uploader) · {{ $att->uploader->name }} @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2 shrink-0 ms-2">
                                            <button onclick="previewAttachment('{{ $att->url() }}', '{{ $att->original_name }}', '{{ $att->mime_type }}')" class="px-2.5 py-1 bg-emerald-600 border border-emerald-700 text-white rounded text-xs font-medium hover:bg-emerald-700 transition" title="Preview">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <a href="{{ $att->url() }}" target="_blank" download="{{ $att->original_name }}" class="px-2.5 py-1 bg-white border border-gray-300 text-gray-700 rounded text-xs font-medium hover:bg-gray-50" title="Download">
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

                    <!-- Evaluation Reviews & Feedback (Visible to Innovator) -->
                    @if ($innovation->reviews->isNotEmpty())
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sm:p-8">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">
                                Evaluation Committee Feedback
                            </h3>
                            <div class="space-y-4">
                                @foreach ($innovation->reviews as $rev)
                                    <div class="p-4 rounded-xl border {{ $rev->decision === 'approved' ? 'bg-emerald-50/50 border-emerald-200' : ($rev->decision === 'rejected' ? 'bg-rose-50/50 border-rose-200' : 'bg-purple-50/50 border-purple-200') }}">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs font-bold uppercase px-2 py-0.5 rounded text-white {{ $rev->decision === 'approved' ? 'bg-emerald-600' : ($rev->decision === 'rejected' ? 'bg-rose-600' : 'bg-purple-600') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $rev->decision)) }}
                                                </span>
                                                <span class="text-xs font-medium text-gray-600">by {{ $rev->reviewer->name }}</span>
                                            </div>
                                            <!-- Rating Stars -->
                                            <div class="flex items-center text-amber-500 text-xs font-bold">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $rev->rating ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                                <span class="ms-1 text-gray-700">({{ $rev->rating }}/5)</span>
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-800 whitespace-pre-line leading-relaxed">
                                            {{ $rev->feedback }}
                                        </div>
                                        <div class="text-[11px] text-gray-400 mt-2">
                                            Evaluated on {{ $rev->created_at->format('M d, Y \a\t H:i') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Sidebar (1 Column) -->
                <div class="space-y-6">

                    <!-- Primary Author & Metadata Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">
                            Primary Innovator
                        </h3>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-sm shrink-0">
                                {{ substr($innovation->author->name, 0, 2) }}
                            </div>
                            <div class="truncate">
                                <div class="font-bold text-sm text-gray-900 truncate">{{ $innovation->author->name }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ $innovation->author->email }}</div>
                                @if($innovation->author->department)
                                    <div class="text-xs text-emerald-700 mt-0.5 truncate">{{ $innovation->author->department }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-100 space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Status</span>
                                <span class="font-semibold text-gray-800">{{ $innovation->status->label() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Created</span>
                                <span class="text-gray-700">{{ $innovation->created_at->format('M d, Y') }}</span>
                            </div>
                            @if($innovation->submitted_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Submitted</span>
                                    <span class="text-gray-700">{{ $innovation->submitted_at->format('M d, Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Co-Innovators / Collaborators Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">
                            Co-Innovators & Collaborators ({{ $innovation->collaborators->count() }})
                        </h3>

                        @if ($innovation->collaborators->isEmpty())
                            <p class="text-xs text-gray-400 italic">No collaborators attached.</p>
                        @else
                            <div class="space-y-3">
                                @foreach ($innovation->collaborators as $collab)
                                    <div class="flex items-center justify-between text-xs">
                                        <div class="truncate">
                                            <div class="font-medium text-gray-800 truncate">{{ $collab->name }}</div>
                                            <div class="text-gray-400 truncate">{{ $collab->department ?? $collab->email }}</div>
                                        </div>
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[11px] font-medium shrink-0 ms-2">
                                            {{ $collab->pivot->role_in_innovation ?? 'Collaborator' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Status History / Audit Trail -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">
                            Lifecycle Audit Trail
                        </h3>
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @foreach ($innovation->statusHistories as $idx => $history)
                                    <li>
                                        <div class="relative pb-6">
                                            @if (! $loop->last)
                                                <span class="absolute top-4 left-3 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3 items-start">
                                                <div>
                                                    <span class="h-6 w-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center ring-4 ring-white text-[10px]">
                                                        ✓
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="text-xs text-gray-800 font-semibold">
                                                        {{ $history->to_status->label() }}
                                                    </div>
                                                    @if ($history->remarks)
                                                        <p class="text-[11px] text-gray-500 mt-0.5">{{ $history->remarks }}</p>
                                                    @endif
                                                    <div class="text-[10px] text-gray-400 mt-1">
                                                        {{ $history->created_at->diffForHumans() }}
                                                        @if ($history->actor) · by {{ $history->actor->name }} @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>


</x-app-layout>
