<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'LaravelFaster' }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="flex min-h-screen flex-col font-mono">
        {{-- Header --}}
        <header class="fixed left-0 right-0 top-0 z-10 flex h-[90px] w-full items-center justify-between border-b-2 border-accent2 bg-white p-2 pb-[4px] pt-2 sm:h-[70px] sm:p-4 sm:pb-[4px] sm:pt-0">
            {{-- Logo --}}
            <a href="/" class="text-4xl font-bold text-accent1" wire:navigate.hover>
                LaravelFaster
            </a>

            {{-- Search --}}
            @persist('search')
                <div class="mx-auto hidden sm:block">
                    <livewire:components::search />
                </div>
            @endpersist

            {{-- Order Links --}}
            <div class="flex flex-row items-center space-x-4">
                <div class="relative">
                    <a href="/order" class="text-lg text-accent1 hover:underline" wire:navigate.hover>
                        ORDER
                    </a>
                    <livewire:components::cart />
                </div>
                <a href="/order-history" class="hidden text-lg text-accent1 hover:underline md:block" wire:navigate.hover>
                    ORDER HISTORY
                </a>
            </div>

            {{-- Mobile Search --}}
            @persist('search-mobile')
                <div class="absolute left-0 right-0 top-14 px-2 sm:hidden">
                    <livewire:components::search />
                </div>
            @endpersist
        </header>

        {{-- Main Content --}}
        <main class="flex flex-grow pb-12 pt-[85px] sm:pb-6 sm:pt-[70px]">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="fixed bottom-0 flex h-12 w-full flex-col items-center justify-between space-y-2 border-t border-gray-400 bg-white px-4 text-[11px] sm:h-6 sm:flex-row sm:space-y-0">
            <div class="flex flex-wrap justify-center space-x-2 pt-2 sm:justify-start">
                <span class="hover:bg-accent2 hover:underline">Home</span>
                <span>|</span>
                <span class="hover:bg-accent2 hover:underline">FAQ</span>
                <span>|</span>
                <span class="hover:bg-accent2 hover:underline">Returns</span>
                <span>|</span>
                <span class="hover:bg-accent2 hover:underline">Careers</span>
                <span>|</span>
                <span class="hover:bg-accent2 hover:underline">Contact</span>
            </div>
        </footer>

        @livewireScripts
    </body>
</html>
