<!-- ═══════════════════════════════════════════════════════════════
     HEADER — Premium Glassmorphism Navigation
     ═══════════════════════════════════════════════════════════════ -->
<header
    id="site-header"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-500"
>
    <!-- Header Glass Background (appears on scroll) -->
    <div id="header-bg" class="absolute inset-0 glass-navy opacity-0 transition-opacity duration-500"></div>

    <!-- Gold accent line at top -->
    <div class="absolute top-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-gold/40 to-transparent"></div>

    <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex items-center justify-between h-20 lg:h-24">

            <!-- ─── Logo ─── -->
            <a href="/" class="group flex items-center gap-3 relative z-10">
         
                <!-- Logo Text -->
                <div class="hidden sm:block">
                    <span class="font-heading text-lg font-semibold text-ivory tracking-tight group-hover:text-gold transition-colors duration-300">
                        Abhiram Chandramohan
                    </span>
                    <span class="font-heading text-lg font-semibold text-ivory-dim tracking-tight group-hover:text-ivory transition-colors duration-300">
                        .
                    </span>
                </div>
            </a>

            <!-- ─── Desktop Navigation (hidden for now) ─── -->
            <nav class="hidden items-center gap-1">
                @php
                    $navItems = [
                        ['label' => 'About', 'href' => '#about'],
                        ['label' => 'Ventures', 'href' => '#ventures'],
                        ['label' => 'Podcast', 'href' => '#podcast'],
                        ['label' => 'Speaking', 'href' => '#speaking'],
                        ['label' => 'Writing', 'href' => '#writing'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    <a
                        href="{{ $item['href'] }}"
                        class="nav-link relative px-4 py-2 text-sm font-medium text-ivory/60 hover:text-ivory transition-colors duration-300 group"
                    >
                        <span class="relative z-10">{{ $item['label'] }}</span>
                        <!-- Hover pill background -->
                        <span class="absolute inset-0 rounded-full bg-ivory/[0.04] scale-90 opacity-0 group-hover:opacity-100 group-hover:scale-100 transition-all duration-300"></span>
                        <!-- Active indicator dot -->
                        <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-gold opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                    </a>
                @endforeach
            </nav>

            <!-- ─── Right Actions ─── -->
            <div class="flex items-center gap-4">
                <!-- CTA Button -->
                <a
                    href="#contact"
                    class="hidden lg:inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-full bg-gold/10 text-gold border border-gold/20 hover:bg-gold hover:text-navy transition-all duration-300 group"
                >
                    <span>Let's Talk</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-0.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>

                <!-- Mobile Menu Toggle -->
                <button
                    id="mobile-menu-toggle"
                    class="lg:hidden relative z-10 w-10 h-10 flex items-center justify-center rounded-lg hover:bg-ivory/[0.05] transition-colors duration-300"
                    aria-label="Toggle menu"
                    aria-expanded="false"
                >
                    <div class="w-5 flex flex-col gap-1.5" id="hamburger-icon">
                        <span class="block h-[1.5px] w-full bg-ivory transition-all duration-300 origin-center" id="bar-1"></span>
                        <span class="block h-[1.5px] w-full bg-ivory transition-all duration-300" id="bar-2"></span>
                        <span class="block h-[1.5px] w-3 bg-ivory transition-all duration-300 origin-center ml-auto" id="bar-3"></span>
                    </div>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- ═══════════════════════════════════════════════════════════════
     MOBILE MENU — Full-screen Overlay
     ═══════════════════════════════════════════════════════════════ -->
<div
    id="mobile-menu"
    class="fixed inset-0 z-40 lg:hidden pointer-events-none"
    aria-hidden="true"
>
    <!-- Backdrop -->
    <div id="mobile-menu-backdrop" class="absolute inset-0 bg-navy/95 backdrop-blur-xl opacity-0 transition-opacity duration-500"></div>

    <!-- Menu Content -->
    <div id="mobile-menu-content" class="relative h-full flex flex-col justify-center px-8 opacity-0 translate-y-8 transition-all duration-500">
        <nav class="space-y-2">
            @foreach($navItems as $index => $item)
                <a
                    href="{{ $item['href'] }}"
                    class="mobile-nav-link block py-4 text-3xl font-heading font-semibold text-ivory/40 hover:text-ivory transition-all duration-300 border-b border-ivory/[0.04]"
                    style="transition-delay: {{ ($index + 1) * 80 }}ms"
                >
                    <span class="flex items-center justify-between">
                        <span>{{ $item['label'] }}</span>
                        <svg class="w-5 h-5 opacity-0 -translate-x-4 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </span>
                </a>
            @endforeach
        </nav>

        <!-- Mobile CTA -->
        <div class="mt-12">
            <a
                href="#contact"
                class="inline-flex items-center gap-3 px-8 py-4 text-base font-medium rounded-full bg-gold text-navy hover:bg-gold-light transition-colors duration-300"
            >
                <span>Let's Talk</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        <!-- Mobile Social Links -->
        <div class="mt-12 flex items-center gap-6">
            <a href="#" class="text-ivory/30 hover:text-gold transition-colors duration-300" aria-label="Twitter/X">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
            <a href="#" class="text-ivory/30 hover:text-gold transition-colors duration-300" aria-label="LinkedIn">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
            </a>
            <a href="#" class="text-ivory/30 hover:text-gold transition-colors duration-300" aria-label="Instagram">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
            </a>
            <a href="#" class="text-ivory/30 hover:text-gold transition-colors duration-300" aria-label="YouTube">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
            </a>
        </div>
    </div>
</div>

<!-- Spacer for fixed header -->
<div class="h-20 lg:h-24"></div>
