<x-app-layout title="Kasir">
    <div class="mx-auto w-full p-2">

        <div class="flex flex-col md:flex-row gap-6 ">

            <x-card class="w-full md:w-3/4 p-4">

                <x-card.title>
                    Produk
                </x-card.title>

                <livewire:product.list :grid=true :isTransaction=true />
            </x-card>

            <x-card class="w-full md:w-1/4 p-4">
                <livewire:cart.main />
            </x-card>
        </div>
    </div>
</x-app-layout>
