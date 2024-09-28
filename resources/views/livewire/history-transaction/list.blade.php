<?php

use App\Models\Transaction;

use function Livewire\Volt\{state, mount, on};
state([
    'transactions' => [],
    'filterData' => [
        'paymentMethod' => 'cash',
        'creditStatus' => 'success',
        'timeFilter' => 'today',
    ],
]);

mount(function () {
    $this->transactions = $this->listTransactions();
});

$updateStatus = function (Transaction $transaction) {
    $transaction->update(['status' => 'success']);
    $this->dispatch('credit-success');
};

on([
    'filter-transaction' => function ($filterData) {
        $this->transactions = $this->filteringData($filterData);
    },
    'credit-success' => function () {
        $this->transactions = $this->listTransactions();
    },
]);

$filteringData = function ($filterData) {
    $this->filterData = $filterData;
    $query = Transaction::query()->with(['itemTransactions.product', 'customer']);

    if ($filterData['customerId']) {
        $query->where('customer_id', $filterData['customerId']);
    }

    // Filter berdasarkan metode pembayaran dan status
    if ($filterData['paymentMethod'] === 'cash') {
        $query->where('payment', 'cash')->where('status', 'success');
    } elseif ($filterData['paymentMethod'] === 'credit') {
        $query->where('payment', 'credit');
        if ($filterData['creditStatus'] === 'success' || $filterData['creditStatus'] === 'pending') {
            $query->where('status', $filterData['creditStatus']);
        }
    }

    // Filter berdasarkan waktu
    switch ($filterData['timeFilter']) {
        case 'today':
            $query->whereDate('created_at', now()->toDateString());
            break;
        case 'oneWeek':
            $query->where('created_at', '>=', now()->subWeek());
            break;
        case 'oneMonth':
            $query->where('created_at', '>=', now()->subMonth());
            break;
        case 'threeMonth':
            $query->where('created_at', '>=', now()->subMonths(3));
            break;
        default:
            break;
    }
    return $query->latest()->get();
};

$listTransactions = function () {
    return Transaction::with(['itemTransactions.product', 'customer'])
        ->whereDate('created_at', now())
        ->latest()
        ->get();
};

?>

<x-accordion type="single" collapsible class="overflow-y-auto max-h-[400px]">


    @foreach ($this->transactions as $transaction)
        <x-accordion.item value="item-{{ $transaction->id }}">
            <x-accordion.trigger>
                <div class="flex gap-2 justify-between">
                    <div>
                        {{ $transaction->created_at->isoFormat('dddd, DD/MM/YYYY-HH:mm', 'id') }}
                    </div>
                    @php
                        $badgeVariant =
                            $transaction->payment == 'credit' && $transaction->status != 'success'
                                ? 'secondary'
                                : 'default';
                        $badgeText = $transaction->payment == 'credit' ? 'Bon' : 'Tunai';
                    @endphp

                    <x-badge variant="{{ $badgeVariant }}">{{ $badgeText }}</x-badge>

                </div>
            </x-accordion.trigger>
            <x-accordion.content>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        @if ($transaction->payment == 'credit')
                            <x-typography.h4 class="capitalize">
                                {{ $transaction->customer->name }}
                            </x-typography.h4>
                        @endif
                        @if ($transaction->payment == 'credit' && $transaction->status != 'success')
                            <x-button wire:click="updateStatus({{ $transaction->id }})">
                                <span wire:loading.remove wire:target="updateStatus({{ $transaction->id }})">
                                    Selesaikan Bon
                                </span>
                                <span wire:loading wire:target="updateStatus({{ $transaction->id }})">
                                    Memproses..
                                </span>
                            </x-button>
                        @else
                            <x-typography.h4>
                                Lunas
                            </x-typography.h4>
                        @endif
                    </div>
                    @foreach ($transaction->itemTransactions as $itemTransaction)
                        <div
                            class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-800 dark:text-white rounded-md">
                            <span class="font-medium">{{ $itemTransaction->product->name }}</span>
                            <div class="flex items-center space-x-4">
                                <span class="text-sm">{{ $itemTransaction->quantity }} x</span>
                                <span class="font-semibold">Rp
                                    {{ number_format($itemTransaction->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 flex justify-end">
                    <span class="font-bold">Total: Rp
                        {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                </div>
            </x-accordion.content>
        </x-accordion.item>
    @endforeach
</x-accordion>
