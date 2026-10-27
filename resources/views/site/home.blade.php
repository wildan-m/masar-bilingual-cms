@php($settings = app(\App\Support\Settings\SiteSettings::class))

<x-layouts.site :seo="$seo">
    <section class="blueprint relative overflow-hidden bg-brand-950 text-white">
        <div class="pointer-events-none absolute -end-32 -top-32 size-[32rem] rounded-full bg-brand-700/30 blur-3xl" aria-hidden="true"></div>

        <div class="shell relative py-20 sm:py-28">
            <p class="eyebrow"><x-localized :text="$settings->text('tagline')" /></p>
            <h1 class="mt-5 max-w-4xl text-4xl leading-tight font-semibold tracking-tight sm:text-6xl">
                <x-localized :text="$settings->text('hero_title')" />
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-relaxed text-brand-100">
                <x-localized :text="$settings->text('hero_subtitle')" />
            </p>

            <div class="mt-10 flex flex-wrap gap-3">
                <a href="{{ route('site.projects.index') }}" class="btn btn-primary">
                    {{ __('site.cta.explore_projects') }}
                    <x-site.arrow />
                </a>
                <a href="{{ route('site.rfp.create') }}" class="btn btn-light">{{ __('site.cta.request_proposal') }}</a>
            </div>

            <dl class="mt-16 grid max-w-3xl gap-8 border-t border-white/10 pt-10 sm:grid-cols-3">
                @if ($stats['years'])
                    <div class="flex flex-col gap-1">
                        <dt class="text-sm text-brand-200">{{ __('site.home.stats.years') }}</dt>
                        <dd class="order-first text-4xl font-semibold text-sand-300">{{ $stats['years'] }}+</dd>
                    </div>
                @endif
                <div class="flex flex-col gap-1">
                    <dt class="text-sm text-brand-200">{{ __('site.home.stats.projects') }}</dt>
                    <dd class="order-first text-4xl font-semibold text-sand-300">{{ $stats['projects'] }}</dd>
                </div>
                <div class="flex flex-col gap-1">
                    <dt class="text-sm text-brand-200">{{ __('site.home.stats.disciplines') }}</dt>
                    <dd class="order-first text-4xl font-semibold text-sand-300">{{ $stats['disciplines'] }}</dd>
                </div>
            </dl>
        </div>
    </section>

    @if ($disciplines->isNotEmpty())
        <section class="bg-slate-50 py-20">
            <div class="shell">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <div class="max-w-2xl">
                        <h2 class="section-title">{{ __('site.home.disciplines_title') }}</h2>
                        <p class="mt-3 text-slate-600">{{ __('site.home.disciplines_intro') }}</p>
                    </div>
                    <a href="{{ route('site.disciplines.index') }}" class="btn btn-outline">
                        {{ __('site.cta.view_all_disciplines') }}
                        <x-site.arrow />
                    </a>
                </div>

                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($disciplines as $discipline)
                        <x-site.discipline-card :discipline="$discipline" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($featuredProjects->isNotEmpty())
        <section class="py-20">
            <div class="shell">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <div class="max-w-2xl">
                        <h2 class="section-title">{{ __('site.home.projects_title') }}</h2>
                        <p class="mt-3 text-slate-600">{{ __('site.home.projects_intro') }}</p>
                    </div>
                    <a href="{{ route('site.projects.index') }}" class="btn btn-outline">
                        {{ __('site.cta.view_all_projects') }}
                        <x-site.arrow />
                    </a>
                </div>

                <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($featuredProjects as $project)
                        <x-site.project-card :project="$project" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($latestArticles->isNotEmpty())
        <section class="bg-slate-50 py-20">
            <div class="shell">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <h2 class="section-title">{{ __('site.home.news_title') }}</h2>
                    <a href="{{ route('site.news.index') }}" class="btn btn-outline">
                        {{ __('site.cta.view_all_news') }}
                        <x-site.arrow />
                    </a>
                </div>

                <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($latestArticles as $article)
                        <x-site.article-card :article="$article" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="py-20">
        <div class="shell">
            <div class="blueprint relative overflow-hidden rounded-3xl bg-brand-900 px-8 py-14 text-white sm:px-14">
                <div class="max-w-2xl">
                    <h2 class="text-3xl font-semibold tracking-tight">{{ __('site.home.cta_title') }}</h2>
                    <p class="mt-4 text-lg text-brand-100">{{ __('site.home.cta_text') }}</p>
                </div>
                <a href="{{ route('site.rfp.create') }}" class="btn btn-primary mt-8">
                    {{ __('site.cta.request_proposal') }}
                    <x-site.arrow />
                </a>
            </div>
        </div>
    </section>
</x-layouts.site>
