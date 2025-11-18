@blaze

@props(['collections' => []])

<div class="flex flex-grow">
    {{-- Sidebar - hidden on mobile, fixed on desktop --}}
    <aside class="fixed left-0 hidden h-full w-64 overflow-y-auto border-r bg-white md:block">
        <div class="p-4">
            <h2 class="mb-4 text-lg font-bold">Categories</h2>

            @foreach ($collections as $collection)
                <div class="mb-6">
                    <h3 class="mb-2 text-sm font-semibold text-gray-900">
                        {{ $collection->name }}
                    </h3>

                    <ul class="space-y-1">
                        @foreach ($collection->categories as $category)
                            <li>
                                <a
                                    href="/products/{{ $category->slug }}"
                                    class="block rounded px-2 py-1 text-sm text-gray-700 hover:bg-accent2 hover:text-accent1 data-current:bg-accent1 data-current:text-white data-current:font-semibold"
                                    wire:navigate.hover
                                >
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </aside>

    {{-- Main content area with left padding on desktop to account for fixed sidebar --}}
    <main class="min-h-[calc(100vh-113px)] flex-1 md:pl-64">
        {{ $slot }}
    </main>
</div>
