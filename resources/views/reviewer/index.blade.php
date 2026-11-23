<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 text-xs font-bold bg-[#002613] text-white uppercase tracking-wider border border-[#004826] rounded">
                        Evaluation Committee
                    </span>
                    <span class="text-xs text-gray-500">KSG Innovation Review</span>
                </div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight mt-1">
                    {{ __('Reviewer Dashboard') }}
                </h2>
            </div>
            <a href="{{ route('innovations.index') }}" class="text-xs text-[#004826] hover:text-[#002613] font-semibold flex items-center">
                Switch to All Innovations View →
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Success Message -->
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-600 border-t border-r border-b border-gray-200 text-emerald-900 text-sm font-medium flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            <!-- Metric Cards (Contiguous Joined Bar - Zero Margins/Gaps, Divide-X) -->
            <div class="grid grid-cols-2 md:grid-cols-4 border border-gray-300 bg-white divide-y md:divide-y-0 md:divide-x divide-gray-300 rounded-lg overflow-hidden shadow-sm">
                <a href="{{ route('reviewer.dashboard', ['tab' => 'queue']) }}"
                   class="relative p-5 md:p-6 transition flex flex-col justify-between {{ $activeTab === 'queue' ? 'bg-emerald-50' : 'bg-white hover:bg-gray-50' }}">
                    @if($activeTab === 'queue')
                        <span class="absolute top-0 left-0 right-0 h-1 bg-[#c69214]"></span>
                    @endif
                    <div>
                        <div class="text-3xl font-black text-[#004826]">{{ $counts['queue'] }}</div>
                        <div class="font-bold text-sm text-gray-900 mt-2">Pending Review</div>
                        <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mt-1">Submitted & Resubmitted</div>
                    </div>
                </a>

                <a href="{{ route('reviewer.dashboard', ['tab' => 'in_review']) }}"
                   class="relative p-5 md:p-6 transition flex flex-col justify-between {{ $activeTab === 'in_review' ? 'bg-amber-50' : 'bg-white hover:bg-gray-50' }}">
                    @if($activeTab === 'in_review')
                        <span class="absolute top-0 left-0 right-0 h-1 bg-[#c69214]"></span>
                    @endif
                    <div>
                        <div class="text-3xl font-black text-amber-600">{{ $counts['in_review'] }}</div>
                        <div class="font-bold text-sm text-gray-900 mt-2">Under Review</div>
                        <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mt-1">In progress evaluation</div>
                    </div>
                </a>

                <a href="{{ route('reviewer.dashboard', ['tab' => 'decided']) }}"
                   class="relative p-5 md:p-6 transition flex flex-col justify-between {{ $activeTab === 'decided' ? 'bg-emerald-50' : 'bg-white hover:bg-gray-50' }}">
                    @if($activeTab === 'decided')
                        <span class="absolute top-0 left-0 right-0 h-1 bg-[#c69214]"></span>
                    @endif
                    <div>
                        <div class="text-3xl font-black text-emerald-700">{{ $counts['decided'] }}</div>
                        <div class="font-bold text-sm text-gray-900 mt-2">Decided</div>
                        <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mt-1">Approved, Revised or Rejected</div>
                    </div>
                </a>

                <a href="{{ route('reviewer.dashboard', ['tab' => 'all']) }}"
                   class="relative p-5 md:p-6 transition flex flex-col justify-between {{ $activeTab === 'all' ? 'bg-gray-50' : 'bg-white hover:bg-gray-50' }}">
                    @if($activeTab === 'all')
                        <span class="absolute top-0 left-0 right-0 h-1 bg-[#c69214]"></span>
                    @endif
                    <div>
                        <div class="text-3xl font-black text-gray-900">{{ $counts['all'] }}</div>
                        <div class="font-bold text-sm text-gray-900 mt-2">All Submitted</div>
                        <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 mt-1">Total evaluation pipeline</div>
                    </div>
                </a>
            </div>

            <!-- Innovations Queue (Flat, Sharp Borders, Zero Glow / Shadows) -->
            @if ($innovations->isEmpty())
                <div class="bg-white border border-gray-200 p-12 text-center">
                    <div class="w-12 h-12 bg-gray-100 text-gray-600 flex items-center justify-center mx-auto mb-3 border border-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-800">Queue is Clear</h3>
                    <p class="text-xs text-gray-500 mt-1">There are no innovations in this category requiring attention.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($innovations as $innovation)
                        <div class="bg-white border border-gray-300 p-6">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold uppercase tracking-wider border {{ $innovation->status->badgeClass() }} rounded">
                                            {{ $innovation->status->label() }}
                                        </span>
                                        <span class="text-xs text-gray-400 font-mono">#{{ $innovation->id }}</span>
                                        @if($innovation->submitted_at)
                                            <span class="text-xs font-medium text-gray-500">
                                                · Queued {{ $innovation->submitted_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="text-lg font-bold text-gray-900 hover:text-[#004826] transition">
                                        <a href="{{ route('reviewer.evaluate', $innovation) }}">
                                            {{ $innovation->title }}
                                        </a>
                                    </h3>

                                    <p class="text-sm text-gray-600 mt-2 line-clamp-2 leading-relaxed">
                                        {{ $innovation->description }}
                                    </p>

                                    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 mt-4 pt-4 border-t border-gray-200">
                                        <div class="flex items-center space-x-1.5 font-medium text-gray-800">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span>{{ $innovation->author->name }}</span>
                                            @if($innovation->author->department)
                                                <span class="text-gray-500">({{ $innovation->author->department }})</span>
                                            @endif
                                        </div>

                                        <div class="flex items-center space-x-1">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            <span>{{ $innovation->attachments->count() }} {{ Str::plural('attachment', $innovation->attachments->count()) }}</span>
                                        </div>

                                        <div class="flex items-center space-x-1 text-gray-700 font-medium">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            <span>{{ $innovation->reviewer_comments_count }} reviewer {{ Str::plural('comment', $innovation->reviewer_comments_count) }}</span>
                                        </div>

                                        @if($innovation->reviews_count > 0)
                                            <div class="flex items-center space-x-1 text-[#c69214] font-semibold">
                                                <span>★ {{ $innovation->averageRating() }}/5 ({{ $innovation->reviews_count }} evaluations)</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="shrink-0 pt-2 md:pt-0">
                                    <a href="{{ route('reviewer.evaluate', $innovation) }}" class="inline-flex items-center px-5 py-2.5 bg-[#c69214] hover:bg-[#e5a817] text-white text-xs font-bold rounded-lg transition shadow-md hover:shadow-lg">
                                        Evaluate Proposal →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $innovations->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
