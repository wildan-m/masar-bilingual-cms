@props(['navigation', 'settings'])

@php
    $aboutUrl = $navigation->pageUrl(\App\Models\Page::ABOUT);
    $contactUrl = $navigation->pageUrl(\App\Models\Page::CONTACT);
    $address = $settings->text('address');
    $hours = $settings->text('office_hours');
@endphp

<footer class="blueprint bg-brand-950 text-brand-100">
    <div class="shell grid gap-12 py-16 md:grid-cols-2 lg:grid-cols-4">
        <div class="lg:col-span-2">
            <x-site.logo inverted />
            <p class="mt-5 max-w-md text-sm leading-relaxed text-brand-200">
                <x-localized :text="$settings->text('tagline')" />
            </p>

            <dl class="mt-8 grid gap-4 text-sm sm:grid-cols-2">
                @unless ($address->isEmpty())
                    <div>
                        <dt class="font-semibold text-white">{{ __('site.contact.address') }}</dt>
                        <dd class="mt-1"><x-localized :text="$address" /></dd>
                    </div>
                @endunless
                @unless ($hours->isEmpty())
                    <div>
                        <dt class="font-semibold text-white">{{ __('site.contact.hours') }}</dt>
                        <dd class="mt-1"><x-localized :text="$hours" /></dd>
                    </div>
                @endunless
                @if ($settings->get('phone'))
                    <div>
                        <dt class="font-semibold text-white">{{ __('site.contact.phone') }}</dt>
                        <dd class="mt-1"><a href="tel:{{ preg_replace('/[^0-9+]/', '', $settings->get('phone')) }}" dir="ltr" class="hover:text-white">{{ $settings->get('phone') }}</a></dd>
                    </div>
                @endif
                @if ($settings->get('email'))
                    <div>
                        <dt class="font-semibold text-white">{{ __('site.contact.email') }}</dt>
                        <dd class="mt-1"><a href="mailto:{{ $settings->get('email') }}" dir="ltr" class="hover:text-white">{{ $settings->get('email') }}</a></dd>
                    </div>
                @endif
            </dl>
        </div>

        <nav aria-label="{{ __('site.footer.explore') }}">
            <h2 class="text-sm font-semibold text-white">{{ __('site.footer.explore') }}</h2>
            <ul class="mt-4 space-y-3 text-sm">
                <li><a href="{{ route('site.disciplines.index') }}" class="hover:text-white">{{ __('site.nav.disciplines') }}</a></li>
                <li><a href="{{ route('site.projects.index') }}" class="hover:text-white">{{ __('site.nav.projects') }}</a></li>
                <li><a href="{{ route('site.news.index') }}" class="hover:text-white">{{ __('site.nav.news') }}</a></li>
            </ul>
        </nav>

        <div>
            <nav aria-label="{{ __('site.footer.company') }}">
                <h2 class="text-sm font-semibold text-white">{{ __('site.footer.company') }}</h2>
                <ul class="mt-4 space-y-3 text-sm">
                    @if ($aboutUrl)
                        <li><a href="{{ $aboutUrl }}" class="hover:text-white">{{ __('site.nav.about') }}</a></li>
                    @endif
                    @if ($contactUrl)
                        <li><a href="{{ $contactUrl }}" class="hover:text-white">{{ __('site.nav.contact') }}</a></li>
                    @endif
                    @foreach ($navigation->secondaryPages() as $page)
                        <li><a href="{{ $page->url() }}" class="hover:text-white"><x-localized :text="$page->localizedText('title')" /></a></li>
                    @endforeach
                </ul>
            </nav>

            @if ($settings->socialLinks())
                <h2 class="mt-8 text-sm font-semibold text-white">{{ __('site.footer.follow') }}</h2>
                <ul class="mt-4 flex flex-wrap gap-x-4 gap-y-2 text-sm">
                    @foreach ($settings->socialLinks() as $network => $url)
                        <li><a href="{{ $url }}" rel="noopener" target="_blank" class="hover:text-white">{{ __("site.social.{$network}") }}</a></li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="shell flex flex-col gap-2 py-6 text-xs text-brand-300 sm:flex-row sm:justify-between">
            <p>&copy; {{ now()->year }} <x-localized :text="$settings->text('company_name')" />. {{ __('site.footer.rights') }}</p>
            <p><a href="{{ route('sitemap') }}" class="hover:text-white" dir="ltr">sitemap.xml</a></p>
        </div>
    </div>
</footer>
