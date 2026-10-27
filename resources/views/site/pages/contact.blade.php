@php
    $settings = app(\App\Support\Settings\SiteSettings::class);
    $address = $settings->text('address');
    $hours = $settings->text('office_hours');
@endphp

<x-layouts.site :seo="$seo">
    <x-site.page-hero :title="$page->localizedText('title')" />

    <section class="py-16 sm:py-20">
        <div class="shell grid gap-14 lg:grid-cols-5">
            <div class="lg:col-span-2">
                <x-rich-text :text="$page->localizedText('body')" />

                <div class="mt-10 rounded-2xl bg-slate-50 p-7 ring-1 ring-slate-200">
                    <h2 class="text-base font-semibold text-brand-950">{{ __('site.contact.details') }}</h2>
                    <dl class="mt-5 space-y-5 text-sm">
                        @unless ($address->isEmpty())
                            <div>
                                <dt class="font-medium text-slate-500">{{ __('site.contact.address') }}</dt>
                                <dd class="mt-1 text-slate-900"><x-localized :text="$address" /></dd>
                                @if ($settings->get('map_url'))
                                    <dd class="mt-1">
                                        <a href="{{ $settings->get('map_url') }}" target="_blank" rel="noopener" class="font-medium text-brand-700 hover:text-brand-900">{{ __('site.contact.map') }}</a>
                                    </dd>
                                @endif
                            </div>
                        @endunless
                        @if ($settings->get('phone'))
                            <div>
                                <dt class="font-medium text-slate-500">{{ __('site.contact.phone') }}</dt>
                                <dd class="mt-1"><a href="tel:{{ preg_replace('/[^0-9+]/', '', $settings->get('phone')) }}" dir="ltr" class="text-slate-900 hover:text-brand-700">{{ $settings->get('phone') }}</a></dd>
                            </div>
                        @endif
                        @if ($settings->get('email'))
                            <div>
                                <dt class="font-medium text-slate-500">{{ __('site.contact.email') }}</dt>
                                <dd class="mt-1"><a href="mailto:{{ $settings->get('email') }}" dir="ltr" class="text-slate-900 hover:text-brand-700">{{ $settings->get('email') }}</a></dd>
                            </div>
                        @endif
                        @if ($settings->get('proposals_email'))
                            <div>
                                <dt class="font-medium text-slate-500">{{ __('site.contact.proposals_email') }}</dt>
                                <dd class="mt-1"><a href="mailto:{{ $settings->get('proposals_email') }}" dir="ltr" class="text-slate-900 hover:text-brand-700">{{ $settings->get('proposals_email') }}</a></dd>
                            </div>
                        @endif
                        @unless ($hours->isEmpty())
                            <div>
                                <dt class="font-medium text-slate-500">{{ __('site.contact.hours') }}</dt>
                                <dd class="mt-1 text-slate-900"><x-localized :text="$hours" /></dd>
                            </div>
                        @endunless
                    </dl>
                </div>
            </div>

            <div class="lg:col-span-3">
                @include('site.partials.rfp-form', ['sectors' => $sectors])
            </div>
        </div>
    </section>
</x-layouts.site>
