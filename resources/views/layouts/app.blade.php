<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Abhiram Chandramohan — Founder, Podcaster, Product Strategist, Startup Consultant & Ecosystem Builder.">
    <meta name="author" content="Abhiram Chandramohan">
    <title>@yield('title', 'Abhiram Chandramohan — Founder · Podcaster · Product Strategist')</title>

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Google Fonts: Space Grotesk + Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="bg-navy text-ivory antialiased bg-grain min-h-screen">

    <!-- Ambient Background Effects -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden" aria-hidden="true">
        <!-- Top-left gold glow -->
        <div class="absolute -top-40 -left-40 w-[600px] h-[600px] rounded-full bg-gold/[0.03] blur-[120px]"></div>
        <!-- Bottom-right sapphire glow -->
        <div class="absolute -bottom-60 -right-60 w-[500px] h-[500px] rounded-full bg-sapphire/[0.04] blur-[150px]"></div>
        <!-- Center subtle gradient -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] rounded-full bg-gold/[0.01] blur-[200px]"></div>
    </div>

    <!-- Site Wrapper -->
    <div class="relative z-10 flex flex-col min-h-screen">

        @include('partials.header')

        <main class="flex-1">
            @yield('content')
        </main>

        @include('partials.footer')

    </div>

    @stack('scripts')
</body>
</html>
