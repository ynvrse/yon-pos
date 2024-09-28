 @props(['href'])

 @php
     $isHref = '/';
     if ($href !== '/') {
         $isHref = trim($href, '/');
     }
 @endphp

 <a href="{{ $href }}" wire:navigate.hover
     {{ $attributes->twMerge(request()->is($isHref) ? 'font-medium underline underline-offset-4' : 'font-medium hover:underline underline-offset-4') }}>{{ $slot }}</a>
