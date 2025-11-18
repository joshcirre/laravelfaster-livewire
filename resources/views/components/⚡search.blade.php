<?php

use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public string $search = '';
    public bool $isOpen = false;

    public function updatedSearch(): void
    {
        $this->isOpen = strlen($this->search) > 0;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    #[On('livewire:navigating')]
    public function clearOnNavigate(): void
    {
        $this->search = '';
        $this->isOpen = false;
    }

    #[Computed]
    public function results()
    {
        if (strlen($this->search) === 0) {
            return collect([]);
        }

        return $this->getSearchResults($this->search);
    }

    protected function getSearchResults(string $searchTerm)
    {
        return cache()->remember(
            "search-results:" . strtolower($searchTerm),
            7200, // 2 hours like NextFaster
            function () use ($searchTerm) {
                if (config('database.default') === 'pgsql') {
                    // PostgreSQL: use ILIKE for case-insensitive search
                    if (strlen($searchTerm) <= 2) {
                        $results = Product::with(['subcategory.subcollection.category'])
                            ->where('name', 'ILIKE', $searchTerm . '%')
                            ->limit(5)
                            ->get();
                    } else {
                        // Use full-text search for longer terms
                        $formattedSearch = implode(' & ', array_map(
                            fn($term) => $term . ':*',
                            array_filter(explode(' ', $searchTerm), fn($t) => trim($t) !== '')
                        ));

                        $results = Product::with(['subcategory.subcollection.category'])
                            ->whereRaw("to_tsvector('english', name) @@ to_tsquery('english', ?)", [$formattedSearch])
                            ->limit(5)
                            ->get();
                    }
                } else {
                    // SQLite/MySQL: use LIKE (case-insensitive by default in SQLite)
                    $results = Product::with(['subcategory.subcollection.category'])
                        ->where('name', 'LIKE', '%' . $searchTerm . '%')
                        ->limit(5)
                        ->get();
                }

                return $results->map(function ($product) {
                    return [
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'image_url' => $product->image_url,
                        'href' => "/products/{$product->subcategory->subcollection->category->slug}/{$product->subcategory->slug}/{$product->slug}",
                    ];
                });
            }
        );
    }
};
?>

<div
    class="relative flex-grow font-sans"
    x-data
    x-init="window.addEventListener('livewire:navigating', () => { $wire.search = ''; $wire.isOpen = false; })"
    @click.outside="$wire.close()"
>
    <div class="relative">
        <input
            type="search"
            wire:model.live.debounce.300ms="search"
            placeholder="Search..."
            autocomplete="off"
            autocapitalize="off"
            autocorrect="off"
            class="w-full rounded border border-gray-300 px-3 py-2 pr-12 font-sans text-sm font-medium focus:border-accent1 focus:outline-none focus:ring-1 focus:ring-accent1 sm:w-[300px] md:w-[375px]"
        />
        <button
            x-show="$wire.isOpen"
            @click="$wire.search = ''; $wire.close()"
            type="button"
            class="absolute right-7 top-2 h-5 w-5 text-gray-400 hover:text-gray-600"
            x-cloak
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    @if ($isOpen)
        <div class="absolute z-10 w-full border border-gray-200 bg-white shadow-lg sm:w-[300px]">
            <div class="max-h-[300px] overflow-y-auto">
                @if ($this->results->count() > 0)
                    @foreach ($this->results as $result)
                        <a
                            href="{{ $result['href'] }}"
                            wire:navigate.hover
                            class="flex cursor-pointer items-center p-2 hover:bg-gray-100"
                            @click="$wire.close()"
                        >
                            @if ($result['image_url'])
                                <img
                                    src="{{ $result['image_url'] }}"
                                    alt="{{ $result['name'] }}"
                                    class="h-10 w-10 pr-2 object-cover"
                                    loading="eager"
                                />
                            @endif
                            <span class="text-sm">{{ $result['name'] }}</span>
                        </a>
                    @endforeach
                @else
                    <div class="flex h-[300px] items-center justify-center">
                        <p class="text-sm text-gray-500">No results found</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
