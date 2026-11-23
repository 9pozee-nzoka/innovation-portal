<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-0.5 text-xs font-bold uppercase tracking-wider text-emerald-900 bg-emerald-50 border border-emerald-300">
                        Administrator Console
                    </span>
                    <span class="text-xs text-gray-500">KSG Portal Governance</span>
                </div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight mt-1">
                    Reviewer Account Verification & Approvals
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Manage committee evaluation credentials. Reviewer applicants have zero portal access until approved.
                </p>
            </div>
            <div>
                <a href="{{ route('reviewer.dashboard') }}" class="text-xs font-bold text-emerald-800 hover:text-emerald-900">
                    Open Reviewer Dashboard →
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Session Status -->
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-600 border-t border-r border-b border-gray-200 text-emerald-900 text-sm font-medium flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-700" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-50 border-l-4 border-red-600 border-t border-r border-b border-gray-200 text-red-900 text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Section 1: Pending Reviewer Applications -->
            <div class="bg-white border border-gray-300">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base text-gray-900">
                            Pending Approval Requests ({{ $pendingReviewers->total() }})
                        </h3>
                        <p class="text-xs text-gray-500">
                            These staff members registered as Reviewers and cannot access the portal until approved.
                        </p>
                    </div>
                    @if($pendingReviewers->total() > 0)
                        <span class="px-2.5 py-1 bg-amber-100 text-amber-900 border border-amber-300 text-xs font-bold">
                            Action Needed
                        </span>
                    @endif
                </div>

                <div class="divide-y divide-gray-200">
                    @forelse($pendingReviewers as $applicant)
                        <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-gray-50/50 transition">
                            <div class="space-y-1">
                                <div class="flex items-center space-x-3">
                                    <h4 class="font-extrabold text-base text-gray-900">
                                        {{ $applicant->name }}
                                    </h4>
                                    <span class="px-2 py-0.5 text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        Pending Reviewer Access
                                    </span>
                                </div>
                                <div class="text-xs text-gray-600 flex flex-wrap items-center gap-x-4 gap-y-1">
                                    <span><strong>Email:</strong> {{ $applicant->email }}</span>
                                    @if($applicant->department)
                                        <span><strong>Department / Campus:</strong> {{ $applicant->department }}</span>
                                    @endif
                                    @if($applicant->phone)
                                        <span><strong>Phone:</strong> {{ $applicant->phone }}</span>
                                    @endif
                                    <span class="text-gray-400">
                                        Registered: {{ $applicant->created_at->format('M d, Y \a\t h:i A') }} ({{ $applicant->created_at->diffForHumans() }})
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center space-x-3 shrink-0">
                                <!-- Approve Form -->
                                <form method="POST" action="{{ route('admin.reviewers.approve', $applicant) }}" onsubmit="return confirm('Approve {{ $applicant->name }} for Reviewer access? An email notification will be sent.');">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition flex items-center space-x-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Approve Reviewer</span>
                                    </button>
                                </form>

                                <!-- Decline Form -->
                                <form method="POST" action="{{ route('admin.reviewers.reject', $applicant) }}" onsubmit="return confirm('Decline reviewer access for {{ $applicant->name }}?');">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-2 bg-white hover:bg-red-50 text-red-700 border border-red-300 text-xs font-semibold transition">
                                        Decline
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-gray-500">
                            <div class="w-12 h-12 bg-gray-100 text-gray-500 border border-gray-300 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-800">No pending reviewer verification requests.</p>
                            <p class="text-xs text-gray-500 mt-1">All applicant requests have been reviewed.</p>
                        </div>
                    @endforelse
                </div>

                @if($pendingReviewers->hasPages())
                    <div class="p-4 border-t border-gray-200 bg-gray-50">
                        {{ $pendingReviewers->links() }}
                    </div>
                @endif
            </div>

            <!-- Section 2: Recently Approved or Rejected Reviewers -->
            @if($pastReviewers->isNotEmpty())
                <div class="bg-white border border-gray-300">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-bold text-sm text-gray-900">
                            Recent Reviewer Verification History
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-gray-600">
                            <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider font-semibold text-[11px] border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3">Staff Name</th>
                                    <th class="px-6 py-3">Email</th>
                                    <th class="px-6 py-3">Department</th>
                                    <th class="px-6 py-3">Decision</th>
                                    <th class="px-6 py-3">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($pastReviewers as $past)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-3.5 font-bold text-gray-900">{{ $past->name }}</td>
                                        <td class="px-6 py-3.5">{{ $past->email }}</td>
                                        <td class="px-6 py-3.5">{{ $past->department ?? '—' }}</td>
                                        <td class="px-6 py-3.5">
                                            @if($past->reviewer_status === 'approved')
                                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                    Approved
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-800 border border-red-300">
                                                    Declined
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3.5 text-gray-400">
                                            {{ $past->reviewer_approved_at ? $past->reviewer_approved_at->format('M d, Y') : $past->updated_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
