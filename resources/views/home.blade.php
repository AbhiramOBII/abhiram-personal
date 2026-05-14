@extends('layouts.app')

@section('title', 'Abhiram Chandramohan — Founder · Podcaster · Product Strategist')

@section('content')

<!-- ═══════════════════════════════════════════════════════════════
     HERO SECTION — Cinematic Full-screen Introduction
     ═══════════════════════════════════════════════════════════════ -->
<section class="relative min-h-[90vh] flex items-center overflow-hidden">
    <!-- Decorative grid lines -->
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
        <div class="absolute top-0 left-1/4 w-px h-full bg-gradient-to-b from-transparent via-ivory/[0.03] to-transparent"></div>
        <div class="absolute top-0 left-1/2 w-px h-full bg-gradient-to-b from-transparent via-ivory/[0.04] to-transparent"></div>
        <div class="absolute top-0 left-3/4 w-px h-full bg-gradient-to-b from-transparent via-ivory/[0.03] to-transparent"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-6 lg:px-8 py-20 lg:py-32 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-20 items-center">

            <!-- Left: Content -->
            <div class="space-y-8">
                <!-- Eyebrow -->
                <div class="animate-fade-in flex items-center gap-3">
                    <div class="w-8 h-px bg-gold"></div>
                    <span class="text-xs font-medium uppercase tracking-[0.25em] text-gold">Founder · Builder · Storyteller</span>
                </div>

                <!-- Heading -->
                <h1 class="animate-slide-up font-heading text-5xl sm:text-6xl lg:text-7xl font-bold leading-[1.05] tracking-tight">
                    <span class="text-gradient-gold">Abhiram</span><br>
                    <span class="text-ivory">Chandramohan</span>
                </h1>

                <!-- Subheading -->
                <p class="animate-slide-up text-lg lg:text-xl text-ivory/50 leading-relaxed max-w-lg" style="animation-delay: 0.2s">
                    Building ventures at the intersection of <span class="text-ivory/80">technology</span>, <span class="text-ivory/80">media</span>, and <span class="text-ivory/80">impact</span>. Turning ideas into ecosystems.
                </p>

              

                <!-- Stats Row -->
                <div class="animate-slide-up pt-8 flex items-center gap-8 lg:gap-12" style="animation-delay: 0.6s">
                    <div>
                        <div class="text-2xl lg:text-3xl font-heading font-bold text-ivory">250+</div>
                        <div class="text-xs text-ivory/30 mt-1 uppercase tracking-wider">Business Consulted</div>
                    </div>
                    <div class="w-px h-10 bg-ivory/10"></div>
                    <div>
                        <div class="text-2xl lg:text-3xl font-heading font-bold text-ivory">20+</div>
                        <div class="text-xs text-ivory/30 mt-1 uppercase tracking-wider">Podcasts</div>
                    </div>
                    <div class="w-px h-10 bg-ivory/10"></div>
                    <div>
                        <div class="text-2xl lg:text-3xl font-heading font-bold text-ivory">20+</div>
                        <div class="text-xs text-ivory/30 mt-1 uppercase tracking-wider">Founder Stories Built</div>
                    </div>
                </div>
            </div>

            <!-- Right: Profile Image -->
            <div class="hidden lg:flex justify-center items-center">
                <div class="w-[500px] h-[500px] rounded-2xl overflow-hidden">
                    <img
                        src="{{ asset('images/Abhiram-photo-03.jpg') }}"
                        alt="Abhiram Chandramohan"
                        class="w-full h-full object-cover"
                    />
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 animate-fade-in" style="animation-delay: 1s">
        <span class="text-[10px] uppercase tracking-[0.3em] text-ivory/20">Scroll</span>
        <div class="w-px h-8 bg-gradient-to-b from-ivory/20 to-transparent"></div>
    </div>
</section>





@endsection
