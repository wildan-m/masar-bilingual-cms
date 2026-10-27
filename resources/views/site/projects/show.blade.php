<x-layouts.site :seo="$seo">
    <article>
        <header class="blueprint relative overflow-hidden bg-brand-950 text-white">
            <div class="shell grid gap-10 py-14 lg:grid-cols-2 lg:items-center lg:py-20">
                <div>
                    <a href="{{ route('site.projects.index') }}" class="eyebrow inline-flex items-center gap-2 hover:text-sand-300">
                        <x-site.arrow direction="back" />
                        {{ __('site.projects.title') }}
                    </a>
                    <h1 class="mt-4 text-3xl font-semibold tracking-tight sm:text-5xl">
                        <x-localized :text="$project->localizedText('title')" />
                    </h1>
                    <p class="mt-5 text-lg leading-relaxed text-brand-100">
                        <x-localized :text="$project->localizedText('summary')" />
                    </p>
                </div>

                <div class="aspect-[4/3] overflow-hidden rounded-2xl bg-brand-900 ring-1 ring-white/10">
                    @if ($project->coverImageUrl())
                        <img src="{{ $project->coverImageUrl() }}" alt="{{ $project->translate('title') }}" class="size-full object-cover">
                    @else
                        <div class="blueprint size-full"></div>
                    @endif
                </div>
            </div>
        </header>

        <div class="shell grid gap-14 py-16 lg:grid-cols-3 lg:py-20">
            <x-rich-text :text="$project->localizedText('body')" class="lg:col-span-2" />

            <aside>
                <h2 class="text-sm font-semibold text-brand-950">{{ __('site.projects.facts.title') }}</h2>
                <dl class="mt-4 divide-y divide-slate-200 rounded-2xl text-sm ring-1 ring-slate-200">
                    @if ($project->sector)
                        <div class="flex justify-between gap-4 px-5 py-4">
                            <dt class="text-slate-500">{{ __('site.projects.facts.sector') }}</dt>
                            <dd class="text-end font-medium text-slate-900">
                                <a href="{{ route('site.projects.index', ['sector' => $project->sector->translate('slug')]) }}" class="hover:text-brand-700">
                                    <x-localized :text="$project->sector->localizedText('name')" />
                                </a>
                            </dd>
                        </div>
                    @endif
                    @unless ($project->localizedText('location')->isEmpty())
                        <div class="flex justify-between gap-4 px-5 py-4">
                            <dt class="text-slate-500">{{ __('site.projects.facts.location') }}</dt>
                            <dd class="text-end font-medium text-slate-900"><x-localized :text="$project->localizedText('location')" /></dd>
                        </div>
                    @endunless
                    @if ($project->client_type)
                        <div class="flex justify-between gap-4 px-5 py-4">
                            <dt class="text-slate-500">{{ __('site.projects.facts.client') }}</dt>
                            <dd class="text-end font-medium text-slate-900">{{ $project->client_type->label() }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between gap-4 px-5 py-4">
                        <dt class="text-slate-500">{{ __('site.projects.facts.completion') }}</dt>
                        <dd class="text-end font-medium text-slate-900">{{ $project->isOngoing() ? __('site.projects.ongoing') : $project->completion_year }}</dd>
                    </div>
                    @if ($disciplines->isNotEmpty())
                        <div class="px-5 py-4">
                            <dt class="text-slate-500">{{ __('site.projects.facts.disciplines') }}</dt>
                            <dd class="mt-3 flex flex-wrap gap-2">
                                @foreach ($disciplines as $discipline)
                                    <a href="{{ $discipline->url() }}" class="rounded-full bg-brand-50 px-3 py-1 text-xs font-medium text-brand-800 hover:bg-brand-100">
                                        <x-localized :text="$discipline->localizedText('name')" />
                                    </a>
                                @endforeach
                            </dd>
                        </div>
                    @endif
                </dl>

                <a href="{{ route('site.rfp.create') }}" class="btn btn-primary mt-8 w-full">{{ __('site.cta.request_proposal') }}</a>
            </aside>
        </div>
    </article>

    @if ($relatedProjects->isNotEmpty())
        <section class="bg-slate-50 py-16 sm:py-20">
            <div class="shell">
                <h2 class="section-title">{{ __('site.projects.related') }}</h2>
                <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($relatedProjects as $related)
                        <x-site.project-card :project="$related" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.site>
