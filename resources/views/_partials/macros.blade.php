@php
  $logoWidth = $width ?? 32;
  $logoClass = $class ?? '';
@endphp
<img src="{{ asset('assets/logo/abk-bg-transparent.jpeg') }}" 
     alt="A.B.K Construction Supplies" 
     width="{{ $logoWidth }}" 
     style="height: auto; max-height: {{ max(32, (int)$logoWidth + 8) }}px; object-fit: contain; border-radius: 6px;" 
     class="app-brand-logo-img {{ $logoClass }}">

