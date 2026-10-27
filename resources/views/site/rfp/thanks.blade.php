<x-layouts.site :seo="$seo">
    <section class="py-20 sm:py-28">
        <div class="shell max-w-2xl text-center">
            <span class="mx-auto inline-flex size-16 items-center justify-center rounded-full bg-brand-50 text-brand-700">
                <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
            </span>
            <h1 class="mt-8 text-3xl font-semibold tracking-tight text-brand-950 sm:text-4xl">{{ __('site.rfp.thanks.heading') }}</h1>
            <p class="mt-5 text-lg leading-relaxed text-slate-600">{{ __('site.rfp.thanks.text') }}</p>

            @if ($reference)
                <p class="mt-6 rounded-xl bg-slate-50 px-5 py-4 text-slate-700 ring-1 ring-slate-200">
                    {!! __('site.rfp.thanks.reference', [
                        'reference' => '<strong dir="ltr" class="font-semibold text-brand-950">'.e($reference).'</strong>',
                    ]) !!}
                </p>
            @endif

            <a href="{{ route('site.home') }}" class="btn btn-outline mt-10">{{ __('site.rfp.thanks.back_home') }}</a>
        </div>
    </section>
</x-layouts.site>
