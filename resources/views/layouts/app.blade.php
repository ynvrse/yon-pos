<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? '' }} - {{ config('app.name') }} </title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ url(asset('favicon.ico')) }}">

    @vite(['resources/css/luvi-ui.css', 'resources/css/filepond.css', 'resources/js/app.js'])

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <script src="https://unpkg.com/filepond@^4/dist/filepond.js" defer></script>

    <livewire:templates.header />

    <div class="mx-6 my-6 sm:mx-2 sm:my-2 pt-[60px]">
        {{ $slot }}
    </div>

    <livewire:templates.footer />


</body>

</html>
