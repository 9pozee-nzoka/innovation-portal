<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                        <x-application-logo class="block h-10 w-auto group-hover:scale-102 transition" />
                        <span class="hidden md:inline-block border-l-2 border-emerald-700 pl-2 text-[10px] font-bold uppercase tracking-wider text-emerald-800 leading-tight">
                            Staff Innovation<br><span class="text-gray-500 font-semibold">Portal</span>
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('innovations.index')" :active="request()->routeIs('innovations.*')">
                        {{ __('Innovations') }}
                    </x-nav-link>
                    @can('create', App\Models\Innovation::class)
                        <x-nav-link :href="route('innovations.create')" :active="request()->routeIs('innovations.create')">
                            {{ __('+ New Innovation') }}
                        </x-nav-link>
                    @endcan
                    @if(Auth::user()->hasRole(['reviewer', 'admin']) || Auth::user()->can('view reviewer dashboard'))
                        <x-nav-link :href="route('reviewer.dashboard')" :active="request()->routeIs('reviewer.*')" class="text-indigo-600 font-semibold">
                            {{ __('Reviewer Dashboard') }}
                        </x-nav-link>
                    @endif
                    @if(Auth::user()->hasRole('admin'))
                        @php
                            $pendingReviewerCount = \App\Models\User::where('reviewer_status', 'pending')->count();
                        @endphp
                        <x-nav-link :href="route('admin.reviewers.index')" :active="request()->routeIs('admin.reviewers.*')" class="text-amber-700 font-semibold">
                            {{ __('Reviewer Approvals') }}
                            @if($pendingReviewerCount > 0)
                                <span class="ms-1 px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-500 text-white">
                                    {{ $pendingReviewerCount }}
                                </span>
                            @endif
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="me-3 text-right">
                    <div class="text-xs font-semibold uppercase tracking-wider text-emerald-600">
                        {{ Auth::user()->getRoleNames()->first() ?? 'Member' }}
                    </div>
                    @if(Auth::user()->department)
                        <div class="text-[11px] text-gray-400 truncate max-w-[200px]">{{ Auth::user()->department }}</div>
                    @endif
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('innovations.index')" :active="request()->routeIs('innovations.*')">
                {{ __('Innovations') }}
            </x-responsive-nav-link>
            @can('create', App\Models\Innovation::class)
                <x-responsive-nav-link :href="route('innovations.create')" :active="request()->routeIs('innovations.create')">
                    {{ __('+ New Innovation') }}
                </x-responsive-nav-link>
            @endcan
            @if(Auth::user()->hasRole(['reviewer', 'admin']) || Auth::user()->can('view reviewer dashboard'))
                <x-responsive-nav-link :href="route('reviewer.dashboard')" :active="request()->routeIs('reviewer.*')">
                    {{ __('Reviewer Dashboard') }}
                </x-responsive-nav-link>
            @endif
            @if(Auth::user()->hasRole('admin'))
                <x-responsive-nav-link :href="route('admin.reviewers.index')" :active="request()->routeIs('admin.reviewers.*')">
                    {{ __('Reviewer Approvals') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
