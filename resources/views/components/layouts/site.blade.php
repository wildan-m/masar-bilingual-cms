@props(['seo' => null])

@php
    $seo ??= \App\Support\Seo\SeoData::fallback();
    $locale = \App\Support\Localization\Locale::current();
    $settings = app(\App\Support\Settings\SiteSettings::class);
    $navigation = new \App\Support\Site\Navigation;
    $companyName = $settings->text('company_name')->get();

    $organization = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $companyName,
        'url' => route('site.home'),
        'logo' => asset('images/og-default.png'),
        'email' => $settings->get('email'),
        'telephone' => $settings->get('phone'),
        'address' => $settings->text('address')->get() ?: null,
        'sameAs' => array_values($settings->socialLinks()) ?: null,
    ]);
@endphp

<!DOCTYPE html>
<html lang="{{ $locale->value }}" dir="{{ $locale->direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $seo->documentTitle() }}</title>
    <meta name="description" content="{{ $seo->description }}">
    @unless ($seo->indexable)
        <meta name="robots" content="noindex, follow">
    @endunless

    <link rel="canonical" href="{{ $seo->canonical() }}">
    @foreach ($seo->alternates as $code => $url)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ $url }}">
    @endforeach
    @if ($seo->defaultAlternate())
        <link rel="alternate" hreflang="x-default" href="{{ $seo->defaultAlternate() }}">
    @endif

    <meta property="og:site_name" content="{{ $companyName }}">
    <meta property="og:type" content="{{ $seo->type }}">
    <meta property="og:title" content="{{ $seo->title ?: $companyName }}">
    <meta property="og:description" content="{{ $seo->description }}">
    <meta property="og:url" content="{{ $seo->canonical() }}">
    <meta property="og:image" content="{{ $seo->imageUrl() }}">
    <meta property="og:locale" content="{{ $locale->openGraphLocale() }}">
    @foreach ($locale->others() as $other)
        <meta property="og:locale:alternate" content="{{ $other->openGraphLocale() }}">
    @endforeach
    @if ($seo->publishedTime)
        <meta property="article:published_time" content="{{ $seo->publishedTime }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <meta name="theme-color" content="#0a222c">

    @fonts($locale->isRtl() ? ['plex-arabic', 'plex-sans'] : ['plex-sans'])
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script type="application/ld+json">@json($organization)</script>
</head>
<body class="flex min-h-screen flex-col bg-white text-slate-800 antialiased">
    <a href="#main" class="skip-link">{{ __('site.skip_to_content') }}</a>

    <x-site.header :navigation="$navigation" :alternates="$seo->alternates" />

    <main id="main" class="flex-1">
        {{ $slot }}
    </main>

    <x-site.footer :navigation="$navigation" :settings="$settings" />
</body>
</html>
