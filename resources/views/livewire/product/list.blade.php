<?php
use App\Models\Product;
use App\Models\Category;
use function Livewire\Volt\{state, computed, on, mount};

state([
    'grid' => true,
    'isTransaction' => false,
    'categorySelected' => null,
]);

mount(function ($grid, $isTransaction) {
    $this->grid = $grid;
    $this->isTransaction = $isTransaction;
});

$categories = computed(fn() => Category::latest()->get());

on([
    'search-product' => function ($search) {
        $this->products = Product::where('name', 'like', '%' . $search . '%')->get();
    },
    'toast' => function () {
        $this->products = Product::with('category')->latest()->get();
    },
]);

$edit = function (Product $product) {
    $productArray = [
        'id' => $product->id,
        'name' => $product->name,
        'price' => $product->price,
        'category_id' => $product->category_id,
    ];

    $this->dispatch('edit-product', $productArray);
    $this->dispatch('toast', $message = 'Product berhasil dipilih', 'info');
};

$delete = function (Product $product) {
    $product->delete();
    $this->dispatch('toast', $message = 'Product berhasil dihapus');
};

$products = computed(function () {
    $query = Product::with('category');

    if ($this->categorySelected) {
        $query->where('category_id', $this->categorySelected);
    }

    return $query->latest()->get();
});

$startTransaction = function (Product $product) {
    $productArray = [
        'id' => $product->id,
        'name' => $product->name,
        'price' => $product->price,
        'category_id' => $product->category_id,
    ];

    $this->dispatch('start-transaction', $productArray);
    $this->dispatch('edit-product', $productArray);
    $this->dispatch('toast', $message = 'Product berhasil dipilih', 'info');
};

$resetCategory = function () {
    $this->categorySelected = null;
};

?>
<div x-data="{ grid: @entangle('grid') }">


    <div class="flex flex-col md:flex-row gap-2 justify-between mb-4">
        <livewire:product.search />

        <div x-data="{ isTransaction: @entangle('isTransaction') }">
            <div class="flex gap-2">

                <x-select class="w-full md:w-48" wire:model.live="categorySelected" id="categorySelected">
                    <x-select.option value="">Pilih Kategori</x-select.option>
                    @foreach ($this->categories as $category)
                        <x-select.option value="{{ $category->id }}">
                            {{ $category->name }}
                        </x-select.option>
                    @endforeach
                </x-select>
                @if ($categorySelected)
                    <x-button wire:click="resetCategory">


                        <x-lucide-loader-circle class="size-4 animate-spin" wire:loading
                            wire:target="categorySelected" />

                        <x-lucide-x class="size-4" wire:loading.remove wire:target="categorySelected" />

                    </x-button>
                @endif

                <template x-if="!isTransaction">
                    <x-button x-on:click="grid = !grid">

                        <template x-if="grid">
                            <x-lucide-align-justify class="size-4" />
                        </template>
                        <template x-if="!grid">
                            <x-lucide-grid-2x2 class="size-4" />
                        </template>
                    </x-button>
                </template>
            </div>

        </div>
    </div>



    <template x-if="grid">
        <!-- Tampilan Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4  "
            style="max-height: 500px; overflow-y: auto; overflow-x:hidden">
            @forelse ($this->products as $item)
                <x-card wire:loading.class="bg-gray-100 dark:bg-gray-800 "
                    wire:target="startTransaction({{ $item->id }})"
                    class="mb-4 hover:bg-gray-100 dark:hover:bg-gray-800 hover:cursor-pointer"
                    wire:key="{{ $item->id }}" wire:click="startTransaction({{ $item->id }})">

                    @if ($item->photo)
                        <img src="{{ Storage::url($item->photo) }}" alt="foto-{{ $item->name }}"
                            class="w-full object-cover" loading="lazy">
                    @endif

                    <div class="p-4 transform hover:scale-105 transition-transform duration-200">
                        <x-card.title>{{ $item->name }}</x-card.title>

                        <x-card.description>{{ $item->category->name ?? '-' }}
                        </x-card.description>
                        <x-card.description class=" font-bold text-xl text-right">
                            Rp.{{ $item->price }}
                        </x-card.description>
                    </div>

                </x-card>
            @empty
                <p class="p-3 text-center text-gray-500">Produk tidak ada.</p>
            @endforelse

        </div>

    </template>
    <template x-if="!grid">
        <!-- Tampilan Tabel -->
        <div wire:loadng.remove class="overflow-x-auto overflow-y-auto" style="max-height: 500px">
            <table class="min-w-full rounded-lg overflow-hidden">
                <thead class="bg-gray-100 dark:bg-gray-800 dark:text-white">
                    <tr>
                        <th class="py-2 px-4 border-b">Foto</th>
                        <th class="py-2 px-4 border-b">Nama</th>
                        <th class="py-2 px-4 border-b">Kategori</th>
                        <th class="py-2 px-4 border-b">Harga</th>

                        <th class="py-2 px-4 border-b">Aksi</th>

                    </tr>
                </thead>
                <tbody class="text-center">

                    @forelse ($this->products as $item)
                        <tr wire:key="{{ $item->id }}"
                            class="dark:bg-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-500">
                            <td class="py-2 px-4 border-b">
                                <img src="{{ Storage::url($item->photo) }}" alt="foto-{{ $item->name }}"
                                    class="w-16 h-16 object-cover" loading="lazy">
                            </td>
                            <td class="py-2 px-4 border-b">{{ $item->name }}</td>

                            <td class="py-2 px-4 border-b">{{ $item->category->name ?? '-' }}</td>
                            <td class="py-2 px-4 border-b">Rp. {{ $item->price }}</td>

                            <td class="py-2 px-4 border-b">
                                <x-button variant="secondary" size="icon" wire:click="edit({{ $item->id }})">
                                    <x-lucide-loader-circle class="size-4 animate-spin" wire:loading
                                        wire:target="edit({{ $item->id }})" />

                                    <x-lucide-pencil wire:loading.remove wire:target="edit({{ $item->id }})"
                                        class="size-4" />
                                </x-button>
                                <x-button variant="destructive" size="icon"
                                    wire:click="delete({{ $item->id }})">
                                    <x-lucide-loader-circle class="size-4 animate-spin" wire:loading
                                        wire:target="delete({{ $item->id }})" />

                                    <x-lucide-trash wire:loading.remove wire:target="delete({{ $item->id }})"
                                        class="size-4" />
                                </x-button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">Produk tidak ada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </template>
</div>
