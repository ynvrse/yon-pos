@props(['value' => '', 'disabled' => false])

<option value="{{ $value }}" class="dark:bg-black" @if ($disabled) disabled @endif>
    {{ $slot }}
</option>
