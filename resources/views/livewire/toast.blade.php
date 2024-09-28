<?php

use function Livewire\Volt\{state, on};

state([
    'message' => '',
    'icon' => 'default',
    'isVisible' => false,
    'variant' => 'default',
]);

on([
    'toast' => function ($message, $variant = 'default') {
        $this->message = $message;
        $this->variant = $variant;
        $this->isVisible = true;

        // Sembunyikan toast setelah 3 detik menggunakan setTimeout
        $this->dispatch('hideToastAfterDelay');
    },
    'hideToastAfterDelay' => function () {
        // Gunakan JavaScript setTimeout untuk menunda eksekusi selama 3 detik
        $this->dispatch('hideToast', [], 3000);
    },
    'hideToast' => function () {
        $this->isVisible = false;
    },
]);

?>

<div x-data="{ show: @entangle('isVisible') }" x-show="show" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-90" class="fixed top-4 right-4 z-50">

    <x-alert :variant="$this->variant">
        @switch($this->variant)
            @case('default')
                <x-lucide-circle-check class="size-4" />
                <x-alert.title>Berhasil!</x-alert.title>
                <x-alert.description>
                    {{ $this->message }}
                </x-alert.description>
            @break

            @case('info')
                <x-lucide-info class="size-4" />
                <x-alert.title>Informasi!</x-alert.title>
                <x-alert.description>
                    {{ $this->message }}
                </x-alert.description>
            @break

            @case('warning')
                <x-lucide-circle-alert class="size-4" />
                <x-alert.title>Perhatian!</x-alert.title>
                <x-alert.description>
                    {{ $this->message }}
                </x-alert.description>
            @break

            @case('destructive')
                <x-lucide-triangle-alert class="size-4" />
                <x-alert.title>Gagal!</x-alert.title>
                <x-alert.description>
                    {{ $this->message }}
                </x-alert.description>
            @break
        @endswitch
    </x-alert>



</div>
