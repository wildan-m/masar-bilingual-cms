@props(['navigation', 'alternates' => []])

@php
    $locale = \App\Support\Localization\Locale::current();
    $contactUrl = $navigation->pageUrl(\App\Models\Page::CONTACT);
@endphp

<header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 backdrop-blur">
    <div class="shell flex h-18 items-center justify-between gap-6">
        <a href="{{ route('site.home') }}" class="shrink-0" aria-label="{{ __('site.nav.home') }}">
            <x-site.logo />
        </a>

        <nav id="site-menu" aria-label="{{ __('site.nav.main') }}"
             class="absolute inset-x-0 top-full hidden border-b border-slate-200 bg-white px-5 pb-6 shadow-lg lg:static lg:flex lg:flex-1 lg:items-center lg:justify-end lg:gap-8 lg:border-0 lg:p-0 lg:shadow-none">
            <ul class="flex flex-col gap-1 pt-2 lg:flex-row lg:gap-7 lg:pt-0">
                @foreach ($navigation->mainItems() as $item)
                    <li>
                        <a href="{{ $item['url'] }}"
                           @if ($item['active']) aria-current="page" @endif
                           class="block py-2 text-sm font-medium transition-colors lg:py-0 {{ $item['active'] ? 'text-brand-700' : 'text-slate-700 hover:text-brand-700' }}">
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="mt-4 flex items-center gap-4 lg:mt-0">
                @foreach ($locale->others() as $other)
                    <a href="{{ $alternates[$other->value] ?? route('site.home', ['locale' => $other->value]) }}"
                       hreflang="{{ $other->value }}" lang="{{ $other->value }}" dir="{{ $other->direction() }}"
                       title="{{ __('site.nav.switch_language', ['language' => $other->nativeName()]) }}"
                       class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-700 hover:text-brand-700"
                       data-language-switch>
                        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM4.332 8.027a6.012 6.012 0 0 1 1.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 0 1 9 7.5V8a2 2 0 0 0 4 0 2 2 0 0 1 1.523-1.943A5.977 5.977 0 0 1 16 10c0 .34-.028.675-.083 1H15a2 2 0 0 0-2 2v2.197A5.973 5.973 0 0 1 10 16v-2a2 2 0 0 0-2-2 2 2 0 0 1-2-2 2 2 0 0 0-1.668-1.973Z" clip-rule="evenodd" /></svg>
                        {{ $other->nativeName() }}
                    </a>
                @endforeach

                @if ($contactUrl)
                    <a href="{{ $contactUrl }}#{{ \App\Http\Requests\StoreProposalRequestRequest::FORM_ANCHOR }}" class="btn btn-primary">
                        {{ __('site.cta.request_proposal') }}
                    </a>
                @endif
            </div>
        </nav>

        <button type="button" class="inline-flex items-center gap-2 rounded-md p-2 text-slate-700 lg:hidden"
                data-menu-toggle aria-controls="site-menu" aria-expanded="false">
            <span class="sr-only">{{ __('site.nav.menu') }}</span>
            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
    </div>
</header>
