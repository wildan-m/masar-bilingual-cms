<x-layouts.site :seo="$seo">
    <x-site.page-hero :eyebrow="__('site.disciplines.title')"
                      :title="$discipline->localizedText('name')"
                      :intro="$discipline->localizedText('summary')" />

    <section class="py-16 sm:py-20">
        <div class="shell grid gap-14 lg:grid-cols-3">
            <x-rich-text :text="$discipline->localizedText('body')" class="lg:col-span-2" />

            @if ($otherDisciplines->isNotEmpty())
                <aside>
                    <h2 class="text-sm font-semibold text-brand-950">{{ __('site.disciplines.other') }}</h2>
                    <ul class="mt-4 divide-y divide-slate-200 rounded-2xl ring-1 ring-slate-200">
                        @foreach ($otherDisciplines as $other)
                            <li>
                                <a href="{{ $other->url() }}" class="flex items-center justify-between gap-3 px-5 py-4 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-brand-700">
                                    <x-localized :text="$other->localizedText('name')" />
                                    <x-site.arrow />
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <a href="{{ route('site.rfp.create') }}" class="btn btn-primary mt-8 w-full">{{ __('site.cta.request_proposal') }}</a>
                </aside>
            @endif
        </div>
    </section>

    @if ($projects->isNotEmpty())
        <section class="bg-slate-50 py-16 sm:py-20">
            <div class="shell">
                <h2 class="section-title">{{ __('site.disciplines.related_projects') }}</h2>
                <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        <x-site.project-card :project="$project" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.site>
