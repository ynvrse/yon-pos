@props(['variant' => 'desktop'])

<div @click="if ((window.innerWidth <= 768 && ('{{ $variant }}' === 'mobile' || '{{ $variant }}' === 'desktop')) || (window.innerWidth > 768 && '{{ $variant }}' === 'desktop')) { open = ! open } else if (window.innerWidth > 768 && '{{ $variant }}' === 'mobile') { open = false }"
    :style="{ 'cursor': '{{ $variant }}'
        === 'mobile' ? 'default' : 'pointer' }">
    {{ $slot }}
</div>
