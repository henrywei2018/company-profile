{{-- resources/views/components/seo/analytics.blade.php --}}
@props(['position' => 'head'])

@php
use App\Helpers\SeoHelper;
$trackingCodes = SeoHelper::getTrackingCodes();
@endphp

@if($position === 'head')
    {{-- Google Tag Manager - Head --}}
    @if($trackingCodes['google_tag_manager'])
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{{ $trackingCodes['google_tag_manager'] }}');</script>
    <!-- End Google Tag Manager -->
    @endif

    {{-- Google Analytics 4 --}}
    @if($trackingCodes['google_analytics'])
    <!-- Google Analytics 4 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $trackingCodes['google_analytics'] }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '{{ $trackingCodes['google_analytics'] }}');
    </script>
    <!-- End Google Analytics 4 -->
    @endif

    {{-- Custom Head Scripts --}}
    @if(settings('head_scripts'))
    {!! settings('head_scripts') !!}
    @endif

@elseif($position === 'body')
    {{-- Google Tag Manager - Body --}}
    @if($trackingCodes['google_tag_manager'])
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $trackingCodes['google_tag_manager'] }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    @endif

    {{-- Custom Body Scripts --}}
    @if(settings('body_scripts'))
    {!! settings('body_scripts') !!}
    @endif
@endif