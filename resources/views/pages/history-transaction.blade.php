<x-app-layout title="Kasir">

    <div class="mx-auto w-full p-2">

        <div class="flex flex-col md:flex-row gap-6 ">

            <x-card class="w-full md:w-3/4 p-4">

                <x-card.title>
                    Riwayat Transaksi
                </x-card.title>
                <x-card.content>
                    <livewire:history-transaction.list />
                </x-card.content>
            </x-card>

            <x-card class="w-full md:w-1/4 p-4">
                <livewire:history-transaction.filter />

            </x-card>

        </div>
    </div>
</x-app-layout>
