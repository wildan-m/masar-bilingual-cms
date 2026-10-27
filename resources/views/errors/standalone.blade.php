{{--
    Standalone error page for server errors: it must render even when the
    database or the asset build is unavailable, so it uses no layout.
--}}
@php($locale = \App\Support\Localization\Locale::current())
<!DOCTYPE html>
<html lang="{{ $locale->value }}" dir="{{ $locale->direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ __('site.errors.'.$code.'.title') }}</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #0a222c; color: #d5ecf1;
               font-family: 'IBM Plex Sans Arabic', 'IBM Plex Sans', system-ui, sans-serif; text-align: center; }
        main { max-width: 32rem; padding: 2rem; }
        p.code { color: #cf9c5c; font-weight: 600; letter-spacing: .2em; }
        h1 { color: #fff; font-size: 2rem; margin: .5rem 0 1rem; }
        a { color: #dbb886; }
    </style>
</head>
<body>
    <main>
        <p class="code" dir="ltr">{{ $code }}</p>
        <h1>{{ __('site.errors.'.$code.'.title') }}</h1>
        <p>{{ __('site.errors.'.$code.'.text') }}</p>
        <p><a href="/">{{ __('site.errors.home') }}</a></p>
    </main>
</body>
</html>
