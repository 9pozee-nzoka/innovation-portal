<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Innovations Portal') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kenya School of Government — Public Service Transformation & Innovation Ideas
                </p>
            </div>
            @can('create', App\Models\Innovation::class)
                <a href="{{ route('innovations.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Submit Innovation') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Success Message -->
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-md shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            <!-- Status Filter Tabs -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-wrap items-center gap-2">
                <span class="text-xs font-semibold uppercase text-gray-400 me-2">Status:</span>
                <a href="{{ route('innovations.index') }}"
                   class="px-3 py-1.5 text-xs font-medium rounded-lg border transition {{ empty($selectedStatus) ? 'bg-gray-900 text-white border-gray-900' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100' }}">
                    All
                </a>
                @foreach ($statuses as $st)
                    <a href="{{ route('innovations.index', ['status' => $st->value]) }}"
                       class="px-3 py-1.5 text-xs font-medium rounded-lg border transition {{ $selectedStatus === $st->value ? 'bg-emerald-700 text-white border-emerald-700 shadow-xs' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                        {{ $st->label() }}
                    </a>
                @endforeach
            </div>

            <!-- Innovations Listing -->
            @if ($innovations->isEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">No innovations found</h3>
                    <p class="text-sm text-gray-500 mt-1 max-w-md mx-auto">
                        @if ($selectedStatus)
                            There are currently no innovations with status "{{ $selectedStatus }}".
                        @else
                            Start by submitting your first public service transformation idea.
                        @endif
                    </p>
                    @can('create', App\Models\Innovation::class)
                        <div class="mt-6">
                            <a href="{{ route('innovations.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition">
                                + Create New Innovation
                            </a>
                        </div>
                    @endcan
                </div>
            @else
                <div class="grid grid-cols-1 gap-4">
                    @foreach ($innovations as $innovation)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $innovation->status->badgeClass() }}">
                                            {{ $innovation->status->label() }}
                                        </span>
                                        <span class="text-xs text-gray-400">#{{ $innovation->id }}</span>
                                        @if($innovation->submitted_at)
                                            <span class="text-xs text-gray-400">· Submitted {{ $innovation->submitted_at->diffForHumans() }}</span>
                                        @else
                                            <span class="text-xs text-gray-400">· Created {{ $innovation->created_at->diffForHumans() }}</span>
                                        @endif
                                    </div>

                                    <h3 class="text-lg font-bold text-gray-900 hover:text-emerald-600 transition">
                                        <a href="{{ route('innovations.show', $innovation) }}">
                                            {{ $innovation->title }}
                                        </a>
                                    </h3>

                                    <p class="text-sm text-gray-600 mt-2 line-clamp-2">
                                        {{ $innovation->description }}
                                    </p>

                                    <!-- Author & Meta -->
                                    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 mt-4 pt-4 border-t border-gray-100">
                                        <div class="flex items-center space-x-1.5">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span class="font-medium text-gray-700">{{ $innovation->author->name }}</span>
                                            @if($innovation->author->department)
                                                <span class="text-gray-400">({{ $innovation->author->department }})</span>
                                            @endif
                                        </div>

                                        <div class="flex items-center space-x-1">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            <span>{{ $innovation->attachments->count() }} {{ Str::plural('attachment', $innovation->attachments->count()) }}</span>
                                        </div>

                                        @if($innovation->collaborators_count > 0)
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                <span>{{ $innovation->collaborators_count }} {{ Str::plural('collaborator', $innovation->collaborators_count()) }}</span>
                                            </div>
                                        @endif

                                        @if($innovation->reviews_count > 0)
                                            <div class="flex items-center space-x-1 text-amber-600 font-medium">
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                <span>{{ $innovation->averageRating() ?? 'N/A' }}/5 ({{ $innovation->reviews_count }} {{ Str::plural('review', $innovation->reviews_count) }})</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-2 shrink-0 pt-2 md:pt-0">
                                    <a href="{{ route('innovations.show', $innovation) }}" class="px-3 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                                        View Details
                                    </a>

                                    @can('update', $innovation)
                                        <a href="{{ route('innovations.edit', $innovation) }}" class="px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                                            Edit
                                        </a>
                                    @endcan

                                    @can('submit', $innovation)
                                        <form method="POST" action="{{ route('innovations.submit', $innovation) }}" onsubmit="return confirm('Are you ready to submit this innovation for committee evaluation?');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition">
                                                {{ $innovation->status->value === 'revision_requested' ? 'Resubmit' : 'Submit' }}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $innovations->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
