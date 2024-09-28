<?php

use App\Models\Customer;
use function Livewire\Volt\{state, computed};

state([
    'filterData' => [
        'paymentMethod' => 'all',
        'creditStatus' => 'pending',
        'timeFilter' => 'today',
        'customerId' => null,
    ],
]);

$filter = function () {
    $this->dispatch('filter-transaction', $this->filterData);
};

$resetFilter = function () {
    $this->filterData = [
        'paymentMethod' => 'all',
        'creditStatus' => 'pending',
        'timeFilter' => 'today',
        'customerId' => null,
    ];

    $this->dispatch('filter-transaction', $this->filterData);
};
$customers = computed(fn() => Customer::whereNot('id', 1)->latest()->get());
?>

<div>
    <div class="flex justify-between">
        <x-card.title>
            Filter Transaksi
        </x-card.title>

        @if (
            $filterData['paymentMethod'] !== 'all' ||
                $filterData['creditStatus'] !== 'pending' ||
                $filterData['timeFilter'] !== 'today')
            <x-button variant="ghost" size="icon" wire:click="resetFilter">
                <x-lucide-rotate-cw class="size-4" wire:loading.class="animate-spin" />
            </x-button>
        @endif
    </div>

    <x-typography.muted class="my-2">
        Waktu Transaksi
    </x-typography.muted>

    <x-select wire:model.live="filterData.timeFilter" wire:change="filter" id="timeFilter">
        <x-select.option value="">Semua Transaksi</x-select.option>
        <x-select.option value="today">Hari ini</x-select.option>
        <x-select.option value="oneWeek">1 minggu terakhir</x-select.option>
        <x-select.option value="oneMonth">1 bulan terakhir</x-select.option>
        <x-select.option value="threeMonth">3 bulan terakhir</x-select.option>
    </x-select>

    <x-typography.muted class="my-2">
        Metode Pembayaran
    </x-typography.muted>

    <x-radio-group id="paymentMethod" wire:model.live="filterData.paymentMethod" wire:change="filter" name="payment"
        defaultValue="cash">
        <div class="flex items-center space-x-2">
            <x-radio-group.item value="all" id="all" />
            <x-label htmlFor="all"> Semua </x-label>
        </div>
        <div class="flex items-center space-x-2">
            <x-radio-group.item value="cash" id="cash" />
            <x-label htmlFor="cash"> Cash </x-label>
        </div>
        <div class="flex items-center space-x-2">
            <x-radio-group.item value="credit" id="credit" />
            <x-label htmlFor="credit"> Credit </x-label>
        </div>
    </x-radio-group>



    @if ($filterData['paymentMethod'] === 'credit')
        <x-typography.muted class="my-2">
            Pembeli
        </x-typography.muted>

        <x-select wire:model.live="filterData.customerId" wire:change="filter" id="customerId">
            <x-select.option value="" disabled>Pilih Pembeli</x-select.option>
            @foreach ($this->customers as $customer)
                <x-select.option value="{{ $customer->id }}">{{ $customer->name }}</x-select.option>
            @endforeach
        </x-select>




        <x-typography.muted class="my-2">
            Status
        </x-typography.muted>

        <x-radio-group wire:model.live="filterData.creditStatus" wire:change="filter" name="creditStatus"
            defaultValue="pending" class="flex gap-2">
            <div class="flex items-center space-x-2">
                <x-radio-group.item value="pending" id="pending" />
                <x-label htmlFor="pending"> Belum Lunas </x-label>
            </div>
            <div class="flex items-center space-x-2">
                <x-radio-group.item value="success" id="success" />
                <x-label htmlFor="success"> Lunas </x-label>
            </div>
        </x-radio-group>
    @endif

</div>
