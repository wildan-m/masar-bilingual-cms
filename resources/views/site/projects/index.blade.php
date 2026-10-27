<x-layouts.site :seo="$seo">
    <x-site.page-hero :title="__('site.projects.title')" :intro="__('site.projects.intro')" />

    <section class="py-16 sm:py-20">
        <div class="shell">
            @if ($sectors->isNotEmpty())
                <nav aria-label="{{ __('site.projects.filter_label') }}">
                    <ul class="flex flex-wrap gap-2">
                        <li>
                            <a href="{{ route('site.projects.index') }}" class="chip" @unless ($activeSector) aria-current="page" @endunless>
                                {{ __('site.projects.all_sectors') }}
                            </a>
                        </li>
                        @foreach ($sectors as $sector)
                            <li>
                                <a href="{{ route('site.projects.index', ['sector' => $sector->translate('slug')]) }}" class="chip"
                                   @if ($activeSector?->is($sector)) aria-current="page" @endif>
                                    <x-localized :text="$sector->localizedText('name')" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endif

            @if ($projects->isEmpty())
                <p class="mt-12 rounded-2xl bg-slate-50 p-10 text-center text-slate-600">{{ __('site.projects.empty') }}</p>
            @else
                <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        <x-site.project-card :project="$project" />
                    @endforeach
                </div>

                {{ $projects->links('pagination.site') }}
            @endif
        </div>
    </section>
</x-layouts.site>
