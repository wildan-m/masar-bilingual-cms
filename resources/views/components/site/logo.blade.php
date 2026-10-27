@props(['inverted' => false])

@php($name = app(\App\Support\Settings\SiteSettings::class)->text('company_name'))

<span {{ $attributes->class('inline-flex items-center gap-3') }}>
    <svg viewBox="0 0 40 40" class="size-9 shrink-0" aria-hidden="true">
        <rect width="40" height="40" rx="10" class="{{ $inverted ? 'fill-white/10' : 'fill-brand-900' }}" />
        <path d="M9 29 L17 15 L23 24 L31 11" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="stroke-sand-400" />
        <circle cx="9" cy="29" r="2.5" class="fill-white" />
        <circle cx="31" cy="11" r="2.5" class="fill-white" />
    </svg>
    <span class="text-base leading-tight font-semibold {{ $inverted ? 'text-white' : 'text-brand-950' }}">
        <x-localized :text="$name" />
    </span>
</span>
