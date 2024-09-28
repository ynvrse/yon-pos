<?php

use App\Models\Transaction;
use App\Models\ItemTransaction;
use App\Models\Customer;

use function Livewire\Volt\{state, on, computed};

state([
    'cart' => [],
    'totalAmount' => 0,
    'totalBayar' => null,
    'isCredit' => false,
    'customer_id' => null,
    'newCustomer' => '',
]);

on([
    'start-transaction' => function ($product) {
        $this->addProductToCart($product);
    },
    'new-customer' => function ($customer) {
        $this->customer_id = $customer;
    },
]);

$addTransaction = function () {
    if ($this->isCredit) {
        $transaction = Transaction::create([
            'customer_id' => $this->customer_id,
            'total_price' => $this->totalAmount,
            'payment' => 'credit',
            'status' => 'pending',
        ]);

        $this->addItemTransaction($transaction->id);
    } else {
        $transaction = Transaction::create([
            'customer_id' => 1,
            'total_price' => $this->totalAmount,
            'payment' => 'cash',
            'status' => 'success',
        ]);

        $this->addItemTransaction($transaction->id);
    }
};

$addItemTransaction = function ($transactionId) {
    foreach ($this->cart as $item) {
        ItemTransaction::create([
            'transaction_id' => $transactionId,
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'total' => $item['total'],
        ]);
    }
};

$addProductToCart = function ($product) {
    $existingItemIndex = collect($this->cart)->search(function ($item) use ($product) {
        return $item['product_id'] === $product['id'];
    });

    if ($existingItemIndex !== false) {
        $this->setQuantity($existingItemIndex, $this->cart[$existingItemIndex]['quantity'] + 1);
    } else {
        $this->cart[] = [
            'product_id' => $product['id'],
            'product_name' => $product['name'],
            'price' => $product['price'],
            'quantity' => 1,
            'total' => $product['price'],
        ];
    }

    $this->calculateTotalAmount();
};

$setQuantity = function ($index, $quantity) {
    if ($quantity > 0) {
        $this->cart[$index]['quantity'] = $quantity;
        $this->cart[$index]['total'] = $this->cart[$index]['price'] * $this->cart[$index]['quantity'];
        $this->calculateTotalAmount();
    } else {
        $this->removeFromCart($index);
    }
};

$removeFromCart = function ($index) {
    unset($this->cart[$index]);
    $this->cart = array_values($this->cart);
    $this->calculateTotalAmount();
};

$calculateTotalAmount = function () {
    $this->totalAmount = collect($this->cart)->sum('total');
};

$customers = computed(fn() => Customer::whereNot('id', 1)->latest()->get());

$resetCart = function () {
    $this->cart = [];
    $this->totalAmount = 0;
    $this->totalBayar = null;
    $this->isCredit = false;
    $this->customer_id = null;
};

$addCustomer = function () {
    if ($this->newCustomer) {
        $customer = Customer::create([
            'name' => $this->newCustomer,
        ]);
        $this->newCustomer = '';

        $this->dispatch('new-customer', $customer->id);
    }
};

?>

<div>

    <x-card.title class="flex justify-between">
        <div class="flex items-center">
            Keranjang

            <span class="ml-2 text-sm text-gray-500" wire:loading>
                <x-lucide-loader-circle class="size-4 animate-spin" />
            </span>
        </div>
        <div class="flex items-center space-x-2">
            <x-switch id="isCredit" wire:model.live="isCredit" />
            <x-label htmlFor="isCredit">Bon</x-label>
        </div>

    </x-card.title>

    @if ($this->cart)
        <div class="overflow-y-auto max-h-[300px]">
            @foreach ($cart as $index => $item)
                <div class="border rounded-md p-4 mb-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="flex items-center font-bold space-x-2">
                                <x-badge>x{{ $item['quantity'] }}</x-badge>
                                <p>{{ $item['product_name'] }}</p>
                            </div>
                            <p>Rp. {{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                        <div class="flex items-center space-x-2">

                            <x-button size="icon" wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                wire:click="setQuantity({{ $index }}, {{ $item['quantity'] - 1 }})"
                                wire:target="setQuantity" variant="destructive">
                                <x-lucide-minus class="size-4" />
                            </x-button>

                            <span class="font-medium">{{ $item['quantity'] }}</span>

                            <x-button size="icon" wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                wire:click="setQuantity({{ $index }}, {{ $item['quantity'] + 1 }})"
                                wire:target="setQuantity" variant="secondary">
                                <x-lucide-plus class="size-4" />
                            </x-button>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 text-right">
            <p class="text-xl font-bold">Total: Rp
                <span wire:loading.remove wire:target="setQuantity">
                    {{ number_format($totalAmount, 0, ',', '.') }}
                </span>
                <span wire:loading wire:target="setQuantity">
                    <x-lucide-loader-circle class="size-4 animate-spin" />
                </span>
            </p>
        </div>
        <div x-data="{ totalBayar: @entangle('totalBayar') }">

            <form wire:submit="addTransaction">
                <div class="{{ !$isCredit ? 'flex gap-2' : '' }} mt-6">
                    @if (!$isCredit)
                        <x-input wire:model.live.debounce.500ms="totalBayar" type="number" x-model="totalBayar"
                            placeholder="Jumlah Bayar" autofocus />
                    @else
                        <div class="flex gap-2 mb-2" x-data="{ addCustomer: false }">
                            <template x-if="!addCustomer">
                                <x-select wire:model.live="customer_id" required>
                                    <x-select.option value="">Pilih Pembeli</x-select.option>
                                    @foreach ($this->customers as $item)
                                        <x-select.option
                                            value="{{ $item->id }}">{{ $item->name }}</x-select.option>
                                    @endforeach
                                </x-select>

                            </template>
                            <template x-if="addCustomer">
                                <x-input wire:model.live.debounce.500ms="newCustomer" type="text" required
                                    placeholder="Nama Pembeli Baru" />
                            </template>

                            <template x-if="!addCustomer">
                                <x-button @click="addCustomer = !addCustomer;$wire.set('customer_id', '');">
                                    <x-lucide-user-plus class="size-4" />
                                </x-button>
                            </template>
                            <template x-if="addCustomer">
                                <x-button wire:click="addCustomer" @click="addCustomer = !addCustomer;" type="submit">
                                    <x-lucide-check class="size-4" />
                                </x-button>
                            </template>
                        </div>
                    @endif

                    <x-sheet>

                        <template x-if="$wire.isCredit && $wire.customer_id">
                            <x-sheet.trigger>
                                <div class="flex justify-end items-end">
                                    <x-button type="submit" wire:loading.attr="disabled"
                                        wire:target="addTransaction,addCustomer" x-bind:disabled="!$wire.customer_id">
                                        Catat Transaksi
                                    </x-button>
                                </div>
                            </x-sheet.trigger>
                        </template>

                        <template x-if="!$wire.isCredit">
                            <x-sheet.trigger>
                                <div class="flex justify-end items-end">

                                    <x-button type="submit" wire:loading.attr="disabled"
                                        wire:target="addTransaction,addCustomer"
                                        x-bind:disabled="totalBayar < {{ $totalAmount }}">
                                        Bayar
                                    </x-button>
                                </div>
                            </x-sheet.trigger>
                        </template>


                        <x-sheet.content x-data="{
                            printReceipt() {
                                const printContent = this.$refs.receiptContent.innerHTML;
                                const printWindow = window.open();
                                printWindow.document.write('<html><head><title>Struk Pembayaran-{{ Str::random(10) }}</title>');
                                printWindow.document.write('<style>@page { size: 80mm 180mm; margin: 0; } body { font-family: monospace; font-size: 12px; line-height: 1.2; width: 80mm; margin: 0; padding: 0; } .receipt { width: 100%; padding: 5px; box-sizing: border-box; } .text-center { text-align: center; } .text-right { text-align: right; } .font-bold { font-weight: bold; } .border-b { border-bottom: 1px dashed #000; padding-bottom: 5px; margin-bottom: 5px; } .mt-2 { margin-top: 10px; } .flex { display: flex; } .justify-between { justify-content: space-between; } .items-center { align-items: center; } .space-y-2 > * + * { margin-top: 0.5rem; } .self-center { align-self: center; } .whitespace-nowrap { white-space: nowrap; } .text-xl { font-size: 1.25rem; } .text-sm { font-size: 0.875rem; } .text-gray-500 { color: #6b7280; }</style>');
                                printWindow.document.write('</head><body>');
                                printWindow.document.write(`<div class='receipt'>`);
                                printWindow.document.write(printContent);
                                printWindow.document.write('</div></body></html>');
                                printWindow.document.close();
                                printWindow.print();
                                printWindow.close();
                            }
                        }">


                            <x-sheet.close wire:click="resetCart" />

                            <x-sheet.header>
                                <div x-ref="receiptContent">
                                    <x-sheet.title class="flex border-b border-dashed pb-4 text-center">
                                        <x-avatar>
                                            <x-avatar.image src="" />
                                            <x-avatar.fallback>YON</x-avatar.fallback>
                                        </x-avatar>
                                        <span class="self-center text-xl font-bold whitespace-nowrap dark:text-white">
                                            VRSE
                                        </span>
                                    </x-sheet.title>
                                    <x-sheet.description>
                                        <p class="border-b border-dashed p-2 text-left">
                                            {{ date('d.m.Y-H:i') }}
                                        </p>
                                        <ul class="space-y-2 border-b p-2">
                                            @foreach ($cart as $index => $item)
                                                <li class="">
                                                    <div class="flex justify-between items-center">

                                                        <p class="font-bold">{{ $item['product_name'] }} <span
                                                                class="text-sm text-gray-500">x{{ $item['quantity'] }}</span>
                                                        </p>
                                                        <p class="text-sm text-gray-500">Rp
                                                            {{ number_format($item['price'], 0, ',', '.') }}</p>
                                                        <p class="text-sm text-gray-500">Rp
                                                            {{ number_format($item['total'], 0, ',', '.') }}</p>

                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <div class="mt-4 text-right font-bold">
                                            Total: Rp {{ number_format($totalAmount, 0, ',', '.') }}
                                        </div>
                                        @if ($isCredit && $customer_id)
                                            <div class="mt-4 text-right font-bold">
                                                Pembeli: {{ $this->customers->find($customer_id)->name }}
                                            </div>
                                        @else
                                            <div class="mt-4 text-right font-bold">
                                                Tunai: Rp <span x-text="totalBayar.toLocaleString('id-ID')"></span>
                                            </div>
                                        @endif

                                        <div class="mt-4 text-right font-bold">
                                            <template x-if="totalBayar > {{ $totalAmount }}">
                                                <span>Kembali: Rp <span
                                                        x-text="(totalBayar - {{ $totalAmount }}).toLocaleString('id-ID')"></span></span>
                                            </template>
                                        </div>
                                    </x-sheet.description>
                                </div>
                            </x-sheet.header>
                            <x-sheet.footer>
                                <x-button class="mt-4" @click="printReceipt()">
                                    <x-lucide-printer class="size-4 mr-2" />
                                    Cetak Struk
                                </x-button>
                            </x-sheet.footer>
                        </x-sheet.content>
                    </x-sheet>
                </div>
            </form>
            @if ($totalBayar < $totalAmount && !$isCredit)
                <p class="text-red-500 text-xs">Jumlah bayar tidak boleh kurang dari total belanja.</p>
            @endif


            <x-button variant="destructive" wire:click="resetCart" class="w-full mt-6">
                <x-lucide-trash class="size-4 mr-2" />
                Kosongkan Keranjang
            </x-button>


        </div>
    @else
        <p class="text-center text-gray-600 mt-4">Belum ada produk yang dipilih.</p>
    @endif
</div>

</div>
