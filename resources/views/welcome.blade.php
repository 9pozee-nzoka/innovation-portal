<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KSG Innovation Portal — Kenya School of Government</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/ksg-logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Compiled Tailwind CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Hero Slider Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/hero-slider.css') }}">
</head>
<body class="font-sans antialiased text-gray-800 bg-gray-50 flex flex-col min-h-screen">

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- KSG Brand Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-3.5 group">
                    <img src="{{ asset('images/ksg-logo.png') }}" alt="Kenya School of Government" class="h-12 md:h-14 w-auto object-contain group-hover:scale-102 transition">
                    <div class="hidden sm:block border-l-2 border-emerald-600 pl-3">
                        <div class="text-[11px] font-black uppercase tracking-wider text-emerald-800 leading-none">
                            Staff Innovation
                        </div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mt-1">
                            Portal
                        </div>
                    </div>
                </a>

                <!-- Nav Links (Desktop) -->
                <nav class="hidden md:flex items-center space-x-8 text-sm font-medium text-gray-600">
                    <a href="{{ route('home') }}" class="text-emerald-700 font-semibold hover:text-emerald-800 transition">Home</a>
                    <a href="#innovations-section" class="hover:text-emerald-700 transition">Innovations</a>
                    <a href="#process-section" class="hover:text-emerald-700 transition">How It Works</a>
                    <a href="#campuses-section" class="hover:text-emerald-700 transition">Campuses</a>
                </nav>

                <!-- Authentication CTA Buttons -->
                <div class="flex items-center space-x-3">
                    @auth
                        <a href="{{ route('innovations.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-lg shadow-sm transition">
                            Go to Innovations Dashboard →
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-3.5 py-2 text-xs font-semibold text-gray-700 hover:text-emerald-700 transition">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-lg shadow-sm transition">
                            Staff Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-grow">

        <!-- ========================================================
             HERO SLIDER COMPONENT (Cross-fading with Staggered Text Flow)
             ======================================================== -->
        <section class="hero-slider" data-interval="6000" aria-roledescription="carousel" aria-label="KSG Highlights">
            <div class="hero-slides" aria-live="polite">

                <!-- SLIDE 1 -->
                <article class="hero-slide is-active" aria-roledescription="slide" aria-label="1 of 4">
                    <div class="hero-media">
                        <img src="{{ asset('images/slider/slide1.jpg') }}" alt="KSG Staff Leadership Innovation">
                    </div>
                    <div class="hero-overlay"></div>
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 hero-inner">
                        <div class="hero-content">
                            <p class="hero-eyebrow hero-anim" style="--i: 0;">Kenya School of Government Staff Portal</p>
                            <h1 class="hero-title hero-anim" style="--i: 1;">
                                Empowering KSG Staff Innovations & Leadership
                            </h1>
                            <p class="hero-desc hero-anim" style="--i: 2;">
                                An internal digital platform for KSG academic faculty, researchers, and administrative personnel across all campuses to propose, refine, and champion institutional transformations.
                            </p>
                            <div class="hero-actions hero-anim" style="--i: 3;">
                                <a href="{{ route('innovations.create') }}" class="hero-cta">
                                    Submit Staff Proposal →
                                </a>
                                <a href="#innovations-section" class="hero-cta-secondary">
                                    Explore Staff Pipeline
                                </a>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- SLIDE 2 -->
                <article class="hero-slide" aria-roledescription="slide" aria-label="2 of 4">
                    <div class="hero-media">
                        <img src="{{ asset('images/slider/slide2.jpg') }}" alt="Multi-Campus Collaboration">
                    </div>
                    <div class="hero-overlay"></div>
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 hero-inner">
                        <div class="hero-content">
                            <p class="hero-eyebrow hero-anim" style="--i: 0;">Multi-Campus Collaboration</p>
                            <h2 class="hero-title hero-anim" style="--i: 1;">
                                Collaborative Innovation Across All 5 KSG Campuses
                            </h2>
                            <p class="hero-desc hero-anim" style="--i: 2;">
                                Team up with colleagues across Lower Kabete, Mombasa, Embu, Matuga, and Baringo. Co-author proposals, attach research briefs, and solve institutional challenges together.
                            </p>
                            <div class="hero-actions hero-anim" style="--i: 3;">
                                <a href="{{ route('register') }}" class="hero-cta">
                                    Register as Staff Innovator →
                                </a>
                                @auth
                                    @if(Auth::user()->hasRole(['reviewer', 'admin']))
                                        <a href="{{ route('reviewer.dashboard') }}" class="hero-cta-secondary">
                                            Open Reviewer Workspace
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </article>

                <!-- SLIDE 3 -->
                <article class="hero-slide" aria-roledescription="slide" aria-label="3 of 4">
                    <div class="hero-media">
                        <img src="{{ asset('images/slider/slide3.jpg') }}" alt="Evaluation and Policy Adoption">
                    </div>
                    <div class="hero-overlay"></div>
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 hero-inner">
                        <div class="hero-content">
                            <p class="hero-eyebrow hero-anim" style="--i: 0;">Rigorous Peer & Committee Evaluation</p>
                            <h2 class="hero-title hero-anim" style="--i: 1;">
                                From Staff Concept to Institutional Policy
                            </h2>
                            <p class="hero-desc hero-anim" style="--i: 2;">
                                Structured peer review and scoring (1 to 5 stars) by KSG academic councils and evaluation committees to accelerate adoption across all School programs.
                            </p>
                            <div class="hero-actions hero-anim" style="--i: 3;">
                                <a href="#process-section" class="hero-cta">
                                    How Evaluation Works →
                                </a>
                                <a href="{{ route('login') }}" class="hero-cta-secondary">
                                    Staff Sign In
                                </a>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- SLIDE 4 -->
                <article class="hero-slide" aria-roledescription="slide" aria-label="4 of 4">
                    <div class="hero-media">
                        <img src="{{ asset('images/slider/slide4.jpg') }}" alt="Digital Transformation and AI in Public Service">
                    </div>
                    <div class="hero-overlay"></div>
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 hero-inner">
                        <div class="hero-content">
                            <p class="hero-eyebrow hero-anim" style="--i: 0;">Institutional Modernization & Digital Tools</p>
                            <h2 class="hero-title hero-anim" style="--i: 1;">
                                Modernizing KSG Operations with Next-Gen Solutions
                            </h2>
                            <p class="hero-desc hero-anim" style="--i: 2;">
                                Harnessing automated workflows, smart campus tools, and curriculum enhancements designed by KSG staff to elevate the School's training and operational excellence.
                            </p>
                            <div class="hero-actions hero-anim" style="--i: 3;">
                                <a href="{{ route('innovations.create') }}" class="hero-cta">
                                    Submit Staff Solution →
                                </a>
                                <a href="#innovations-section" class="hero-cta-secondary">
                                    View Staff Proposals
                                </a>
                            </div>
                        </div>
                    </div>
                </article>

            </div>

            <!-- Slider Arrows (Previous & Next) -->
            <button class="hero-arrow hero-prev" aria-label="Previous slide">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button class="hero-arrow hero-next" aria-label="Next slide">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Bottom Navigation Bar (Dots & Pause Toggle) -->
            <div class="hero-bar">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                    <div class="hero-dots" role="tablist" aria-label="Select slide">
                        <button class="hero-dot is-current" role="tab" aria-label="Slide 1" aria-current="true"></button>
                        <button class="hero-dot" role="tab" aria-label="Slide 2" aria-current="false"></button>
                        <button class="hero-dot" role="tab" aria-label="Slide 3" aria-current="false"></button>
                        <button class="hero-dot" role="tab" aria-label="Slide 4" aria-current="false"></button>
                    </div>

                    <button class="hero-toggle" aria-label="Pause slider">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 4h3v12H5V4zm7 0h3v12h-3V4z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        <!-- ========================================================
             KEY METRICS COUNTER BAR
             ======================================================== -->
        <section class="bg-white border-b border-gray-100 py-6 relative z-10 shadow-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center divide-y md:divide-y-0 md:divide-x divide-gray-100">
                    <div class="pt-2 md:pt-0">
                        <div class="text-3xl font-extrabold text-emerald-800">{{ $stats['total'] ?? 0 }}</div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 mt-1">Innovations Submitted</div>
                    </div>
                    <div class="pt-4 md:pt-0">
                        <div class="text-3xl font-extrabold text-amber-600">{{ $stats['under_review'] ?? 0 }}</div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 mt-1">In Active Evaluation</div>
                    </div>
                    <div class="pt-4 md:pt-0">
                        <div class="text-3xl font-extrabold text-emerald-600">{{ $stats['approved'] ?? 0 }}</div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 mt-1">Approved for Adoption</div>
                    </div>
                    <div class="pt-4 md:pt-0">
                        <div class="text-3xl font-extrabold text-gray-900">{{ $stats['innovators'] ?? 0 }}</div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 mt-1">Registered KSG Staff & Faculty</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================================
             HOW IT WORKS: 4-STAGE PIPELINE
             ======================================================== -->
        <section id="process-section" class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-12">
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-100/60 px-3 py-1 rounded-full">
                        Lifecycle Process
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-3">
                        How the KSG Innovation Pipeline Works
                    </h2>
                    <p class="text-sm text-gray-600 mt-2">
                        From drafting your idea to committee scoring, feedback, and institutional implementation.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Step 1 -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-black text-lg mb-4">
                            01
                        </div>
                        <h3 class="font-bold text-base text-gray-900">Draft & Attach</h3>
                        <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                            Formulate your proposal, define strategic objectives, and upload supporting research briefs, documents, or diagrams.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-black text-lg mb-4">
                            02
                        </div>
                        <h3 class="font-bold text-base text-gray-900">Add Collaborators</h3>
                        <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                            Invite fellow KSG faculty, campus colleagues, or department officers to co-develop and enrich your innovation.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-black text-lg mb-4">
                            03
                        </div>
                        <h3 class="font-bold text-base text-gray-900">Committee Review</h3>
                        <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                            Reviewers evaluate your submission, provide 1–5 star ratings, and leave actionable recommendations for refinement.
                        </p>
                    </div>

                    <!-- Step 4 -->
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs hover:shadow-md transition">
                        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-black text-lg mb-4">
                            04
                        </div>
                        <h3 class="font-bold text-base text-gray-900">Approval & Adoption</h3>
                        <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                            Approved innovations receive institutional recognition and are positioned for pilot deployment across KSG directorates and training programs.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================================
             FEATURED / RECENT INNOVATIONS FEED
             ======================================================== -->
        <section id="innovations-section" class="py-16 bg-white border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full">
                            Active Showcase
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-3">
                            Latest KSG Staff Innovations
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Transformative proposals currently undergoing evaluation or approved by KSG committees.
                        </p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="{{ route('innovations.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center">
                            Browse All Submissions →
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @forelse ($recentInnovations as $inn)
                        <div class="bg-gray-50/70 rounded-2xl border border-gray-200 p-6 flex flex-col justify-between hover:border-emerald-300 hover:shadow-md transition">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $inn->status->badgeClass() }}">
                                        {{ $inn->status->label() }}
                                    </span>
                                    <span class="text-[11px] text-gray-400">
                                        {{ $inn->submitted_at ? $inn->submitted_at->format('M d, Y') : $inn->created_at->format('M d, Y') }}
                                    </span>
                                </div>
                                <h3 class="font-bold text-base text-gray-900 hover:text-emerald-700 transition">
                                    <a href="{{ route('innovations.show', $inn) }}">
                                        {{ $inn->title }}
                                    </a>
                                </h3>
                                <p class="text-xs text-gray-600 mt-2 line-clamp-3 leading-relaxed">
                                    {{ $inn->description }}
                                </p>
                            </div>

                            <div class="pt-4 mt-4 border-t border-gray-200/60 flex items-center justify-between text-xs text-gray-500">
                                <div class="truncate font-medium text-gray-700">
                                    {{ $inn->author->name }}
                                </div>
                                <a href="{{ route('innovations.show', $inn) }}" class="text-emerald-700 font-semibold hover:underline">
                                    Details →
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-12 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                            <p class="text-sm text-gray-500">No public innovations found yet.</p>
                            <a href="{{ route('innovations.create') }}" class="inline-block mt-3 text-xs font-bold text-emerald-700 hover:underline">
                                Be the first to submit a proposal →
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- ========================================================
             FACULTY & OFFICER CALL TO ACTION BANNER (HIGH VISIBILITY)
             ======================================================== -->
        <section class="py-20 relative overflow-hidden text-center shadow-lg" style="background: linear-gradient(135deg, #002613 0%, #004826 45%, #002412 100%); color: #ffffff;">
            <!-- Subtle background decorative accents -->
            <div class="absolute inset-0 pointer-events-none opacity-10" style="background-image: radial-gradient(#c69214 1.5px, transparent 1.5px); background-size: 28px 28px;"></div>

            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                <span class="inline-block text-xs font-bold uppercase tracking-widest text-[#e5a817] bg-black/35 px-4 py-1.5 rounded-full mb-4 border border-[#e5a817]/30 shadow-xs">
                    Institutional Innovation Call
                </span>
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight text-white drop-shadow-md">
                    Have an Idea to Transform KSG Operations or Training?
                </h2>
                <p class="text-base sm:text-lg text-emerald-100/90 mt-5 leading-relaxed max-w-2xl mx-auto">
                    The Kenya School of Government invites faculty members, academic researchers, and administrative staff across all 5 campuses to propose actionable internal innovations.
                </p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ route('innovations.create') }}" class="px-7 py-3.5 rounded-xl font-bold text-sm text-white shadow-xl hover:scale-105 transition" style="background-color: #c69214;">
                        Submit Staff Innovation →
                    </a>
                    <a href="{{ route('login') }}" class="px-7 py-3.5 rounded-xl font-semibold text-sm text-white border border-white/40 hover:bg-white/10 transition backdrop-blur-xs">
                        Committee Member Sign In
                    </a>
                </div>
            </div>
        </section>

        <!-- ========================================================
             CAMPUSES SECTION
             ======================================================== -->
        <section id="campuses-section" class="py-12 bg-white border-t border-gray-100 text-center">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-6">
                    Kenya School of Government Campuses
                </p>
                <div class="flex flex-wrap items-center justify-center gap-6 text-xs font-semibold text-gray-600">
                    <span class="px-4 py-2 bg-gray-50 rounded-lg border border-gray-200">Lower Kabete (Main Campus)</span>
                    <span class="px-4 py-2 bg-gray-50 rounded-lg border border-gray-200">Mombasa Campus</span>
                    <span class="px-4 py-2 bg-gray-50 rounded-lg border border-gray-200">Embu Campus</span>
                    <span class="px-4 py-2 bg-gray-50 rounded-lg border border-gray-200">Matuga Campus</span>
                    <span class="px-4 py-2 bg-gray-50 rounded-lg border border-gray-200">Baringo Campus</span>
                </div>
            </div>
        </section>

    </main>

    <!-- FOOTER -->
    <footer class="bg-gray-900 text-gray-400 text-xs py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="space-y-3">
                    <div class="bg-white/95 px-3 py-1.5 rounded-xl inline-block shadow-sm">
                        <img src="{{ asset('images/ksg-logo.png') }}" alt="Kenya School of Government" class="h-9 w-auto object-contain">
                    </div>
                    <p class="text-[11px] text-gray-400 leading-relaxed">
                        Transforming public administration through leadership development, strategic policy research, and technological innovation.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3">Quick Links</h4>
                    <ul class="space-y-2 text-[11px]">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition">Home</a></li>
                        <li><a href="{{ route('innovations.index') }}" class="hover:text-white transition">My Innovations</a></li>
                        <li><a href="{{ route('innovations.create') }}" class="hover:text-white transition">+ Submit Proposal</a></li>
                        <li><a href="{{ route('reviewer.dashboard') }}" class="hover:text-white transition">Reviewer Dashboard</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3">Governance</h4>
                    <ul class="space-y-2 text-[11px]">
                        <li><a href="#" class="hover:text-white transition">Innovation Guidelines</a></li>
                        <li><a href="#" class="hover:text-white transition">Evaluation Scoring Rubric</a></li>
                        <li><a href="#" class="hover:text-white transition">Ethics & Confidentiality</a></li>
                        <li><a href="#" class="hover:text-white transition">Intellectual Property</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3">Contact Support</h4>
                    <p class="text-[11px] text-gray-400 leading-relaxed">
                        ICT & Innovation Directorate<br>
                        Kenya School of Government, Lower Kabete<br>
                        P.O. Box 23030 - 00604, Lower Kabete, Nairobi<br>
                        Email: <a href="mailto:sheilachumba7@gmail.com" class="text-emerald-400 hover:underline">sheilachumba7@gmail.com</a>
                    </p>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px]">
                <p>© {{ date('Y') }} Kenya School of Government. All rights reserved.</p>
                <p>Built with PHP Laravel 11 & Tailwind CSS</p>
            </div>
        </div>
    </footer>

    <!-- Hero Slider Engine -->
    <script src="{{ asset('js/hero-slider.js') }}"></script>
</body>
</html>
