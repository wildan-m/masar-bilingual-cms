@props(['discipline'])

@php($icon = array_key_exists((string) $discipline->icon, \App\Models\Discipline::ICONS) ? $discipline->icon : 'cube-transparent')

<article class="group relative flex flex-col rounded-2xl bg-white p-7 ring-1 ring-slate-200 transition hover:ring-brand-300 hover:shadow-xl hover:shadow-brand-950/5">
    <span class="inline-flex size-12 items-center justify-center rounded-xl bg-brand-50 text-brand-700 transition group-hover:bg-brand-900 group-hover:text-sand-300">
        {{ svg('heroicon-o-'.$icon, 'size-6') }}
    </span>

    <h3 class="mt-6 text-lg font-semibold text-brand-950">
        <a href="{{ $discipline->url() }}" class="after:absolute after:inset-0 focus:outline-none">
            <x-localized :text="$discipline->localizedText('name')" />
        </a>
    </h3>

    <p class="mt-3 text-sm leading-relaxed text-slate-600">
        <x-localized :text="$discipline->localizedText('summary')" />
    </p>

    <span class="mt-auto inline-flex items-center gap-1.5 pt-6 text-sm font-semibold text-brand-700">
        {{ __('site.cta.learn_more') }}
        <x-site.arrow class="transition group-hover:translate-x-0.5 rtl:group-hover:-translate-x-0.5" />
    </span>
</article>
