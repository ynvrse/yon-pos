<x-app-layout title="Produk">
    @volt
        <div class="mx-auto w-full p-2">
            <div class="flex flex-col md:flex-row gap-6 ">
                {{-- @vite('resources/js/filepond.js') --}}
                <x-card class="sm:w-full md:w-1/4 p-4">
                    <livewire:product.add />
                </x-card>

                <x-card class="sm:w-full md:w-3/4 p-4">

                    <x-card.title>List Produk</x-card.title>

                    <x-card.content>
                        <livewire:product.list :grid=false :isTransaction=false />
                    </x-card.content>
                </x-card>

                <livewire:toast />

            </div>
        </div>
    @endvolt
</x-app-layout>
