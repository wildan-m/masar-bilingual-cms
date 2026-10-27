@props(['code'])

<x-layouts.site :seo="\App\Support\Seo\SeoData::fallback(__('site.errors.'.$code.'.title'))">
    <section class="blueprint bg-brand-950 py-24 text-white sm:py-32">
        <div class="shell max-w-2xl text-center">
            <p class="eyebrow" dir="ltr">{{ $code }}</p>
            <h1 class="mt-4 text-3xl font-semibold tracking-tight sm:text-5xl">{{ __('site.errors.'.$code.'.title') }}</h1>
            <p class="mt-6 text-lg text-brand-100">{{ __('site.errors.'.$code.'.text') }}</p>
            <a href="{{ route('site.home') }}" class="btn btn-primary mt-10">{{ __('site.errors.home') }}</a>
        </div>
    </section>
</x-layouts.site>
