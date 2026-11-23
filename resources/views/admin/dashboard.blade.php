<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-2 mb-1">
                    <span class="px-2.5 py-0.5 text-xs font-extrabold uppercase tracking-wider text-green bg-[#002613] border border-[#004826] rounded">
                        Administrator
                    </span>
                    <span class="text-xs text-gray-500">System Overview</span>
                </div>
                <h2 class="font-extrabold text-3xl text-gray-900 leading-tight">
                    Innovation Portal Dashboard
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Real-time analytics and system metrics for the KSG Innovation Portal
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.reviewers.index') }}" class="px-4 py-2 bg-[#c69214] hover:bg-[#e5a817] text-white text-sm font-bold rounded-lg transition shadow-md hover:shadow-lg">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span class="bg-red-500 text-green">Reviewer Approvals</span>
                        @if($stats['pending_reviewers'] > 0)
                            <span class="px-2 py-0.5 bg-red-500 text-green text-xs font-bold rounded-full">
                                {{ $stats['pending_reviewers'] }}
                            </span>
                        @endif
                    </div>
                </a>
                <a href="{{ route('reviewer.dashboard') }}" class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold border border-gray-300 rounded-lg transition">
                    Reviewer Portal →
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Innovations -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-[#004826] transform hover:scale-105 transition duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Total Innovations</p>
                            <h3 class="text-4xl font-extrabold text-[#002613] mt-2">{{ $stats['total_innovations'] }}</h3>
                            <p class="text-xs text-gray-600 mt-2">All submissions</p>
                        </div>
                        <div class="bg-emerald-50 rounded-lg p-3">
                            <svg class="w-8 h-8 text-[#004826]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pending Review -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-amber-500 transform hover:scale-105 transition duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Pending Review</p>
                            <h3 class="text-4xl font-extrabold text-amber-700 mt-2">{{ $stats['pending_review'] }}</h3>
                            <p class="text-xs text-gray-600 mt-2">Awaiting evaluation</p>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-3">
                            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Approved -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-[#c69214] transform hover:scale-105 transition duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Approved</p>
                            <h3 class="text-4xl font-extrabold text-emerald-700 mt-2">{{ $stats['approved'] }}</h3>
                            <p class="text-xs text-gray-600 mt-2">Success rate: {{ $stats['total_innovations'] > 0 ? round(($stats['approved'] / $stats['total_innovations']) * 100, 1) : 0 }}%</p>
                        </div>
                        <div class="bg-emerald-50 rounded-lg p-3">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Users -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-indigo-500 transform hover:scale-105 transition duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Total Users</p>
                            <h3 class="text-4xl font-extrabold text-gray-900 mt-2">{{ $stats['total_users'] }}</h3>
                            <p class="text-xs text-gray-600 mt-2">{{ $stats['innovators'] }} innovators, {{ $stats['reviewers'] }} reviewers</p>
                        </div>
                        <div class="bg-indigo-50 rounded-lg p-3">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656.126-1.283.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow p-4 border-t-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Rejected</p>
                            <p class="text-3xl font-bold text-red-600 mt-1">{{ $stats['rejected'] }}</p>
                        </div>
                        <div class="bg-red-50 rounded-full p-2.5">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4 border-t-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Revisions Needed</p>
                            <p class="text-3xl font-bold text-purple-600 mt-1">{{ $stats['revision_requested'] }}</p>
                        </div>
                        <div class="bg-purple-50 rounded-full p-2.5">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4 border-t-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Total Reviews</p>
                            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $stats['total_reviews'] }}</p>
                        </div>
                        <div class="bg-blue-50 rounded-full p-2.5">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4 border-t-4 border-[#c69214]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Avg Rating</p>
                            <p class="text-3xl font-bold text-[#c69214] mt-1">{{ $stats['average_rating'] ?? 'N/A' }}<span class="text-sm text-gray-400">/5.0</span></p>
                        </div>
                        <div class="bg-yellow-50 rounded-full p-2.5">
                            <svg class="w-5 h-5 text-[#c69214]" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Tables Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Innovations by Status -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 px-6 py-4 border-b border-gray-200">
                        <h3 class="font-bold text-lg text-[#002613]">Innovations by Status</h3>
                        <p class="text-xs text-gray-600 mt-1">Current distribution across lifecycle stages</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach(\App\Enums\InnovationStatus::cases() as $status)
                                @php
                                    $count = $innovationsByStatus[$status->value] ?? 0;
                                    $percentage = $stats['total_innovations'] > 0 ? ($count / $stats['total_innovations']) * 100 : 0;
                                @endphp
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-semibold text-gray-700">{{ $status->label() }}</span>
                                        <span class="text-sm font-bold text-gray-900">{{ $count }} <span class="text-xs text-gray-500">({{ round($percentage, 1) }}%)</span></span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                        <div class="h-2.5 rounded-full {{ $status->badgeClass() }} border-0" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Top Innovators -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 px-6 py-4 border-b border-gray-200">
                        <h3 class="font-bold text-lg text-[#002613]">Top Innovators</h3>
                        <p class="text-xs text-gray-600 mt-1">Most active innovation contributors</p>
                    </div>
                    <div class="p-6">
                        @if($topInnovators->count() > 0)
                            <div class="space-y-4">
                                @foreach($topInnovators as $index => $innovator)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#c69214] to-[#e5a817] flex items-center justify-center text-white font-bold text-sm shadow-md">
                                                    {{ $index + 1 }}
                                                </div>
                                            </div>
                                            <div>
                                                <p class="font-bold text-sm text-gray-900">{{ $innovator->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $innovator->department }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-[#004826]">{{ $innovator->innovations_count }}</p>
                                            <p class="text-xs text-gray-500">submissions</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <p class="text-sm">No innovators yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Activity and Reviews -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Innovations -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 px-6 py-4 border-b border-gray-200">
                        <h3 class="font-bold text-lg text-[#002613]">Recent Innovations</h3>
                        <p class="text-xs text-gray-600 mt-1">Latest submissions (last 7 days)</p>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($recentInnovations as $innovation)
                            <a href="{{ route('innovations.show', $innovation) }}" class="block p-5 hover:bg-gray-50 transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-sm text-gray-900 truncate">{{ $innovation->title }}</h4>
                                        <p class="text-xs text-gray-600 mt-1">
                                            By <span class="font-semibold">{{ $innovation->author->name }}</span>
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $innovation->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="ml-3 px-2 py-1 text-[10px] font-bold {{ $innovation->status->badgeClass() }} border rounded">
                                        {{ $innovation->status->label() }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="p-8 text-center text-gray-500">
                                <p class="text-sm">No recent innovations</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Reviews -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 px-6 py-4 border-b border-gray-200">
                        <h3 class="font-bold text-lg text-[#002613]">Recent Reviews</h3>
                        <p class="text-xs text-gray-600 mt-1">Latest evaluation decisions</p>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($recentReviews as $review)
                            <div class="p-5 hover:bg-gray-50 transition">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-sm text-gray-900 truncate">{{ $review->innovation->title }}</h4>
                                        <p class="text-xs text-gray-600 mt-1">
                                            Reviewed by <span class="font-semibold">{{ $review->reviewer->name }}</span>
                                        </p>
                                    </div>
                                    <div class="ml-3 flex items-center space-x-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-[#c69214]' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 mt-2">
                                    @php
                                        $decisionClass = match($review->decision) {
                                            'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                            'rejected' => 'bg-red-100 text-red-800 border-red-300',
                                            'revision_requested' => 'bg-purple-100 text-purple-800 border-purple-300',
                                            default => 'bg-gray-100 text-gray-800 border-gray-300'
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 text-[10px] font-bold {{ $decisionClass }} border rounded uppercase">
                                        {{ str_replace('_', ' ', $review->decision) }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500">
                                <p class="text-sm">No reviews yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Department Statistics -->
            @if($departmentStats->count() > 0)
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-50 to-emerald-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-lg text-[#002613]">Department Participation</h3>
                    <p class="text-xs text-gray-600 mt-1">User distribution by department</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($departmentStats as $dept)
                            <div class="flex items-center justify-between p-4 bg-emerald-50/30 rounded-lg border border-emerald-100 hover:border-[#004826] hover:shadow-md transition">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-sm text-gray-900 truncate">{{ $dept->department }}</p>
                                </div>
                                <div class="ml-4 flex items-center space-x-2">
                                    <span class="text-lg font-bold text-[#004826]">{{ $dept->count }}</span>
                                    <span class="text-xs text-gray-500">users</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
