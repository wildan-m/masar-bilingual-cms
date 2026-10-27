<x-layouts.site :seo="$seo">
    <article>
        <header class="blueprint bg-brand-950 text-white">
            <div class="shell max-w-4xl py-14 lg:py-20">
                <a href="{{ route('site.news.index') }}" class="eyebrow inline-flex items-center gap-2 hover:text-sand-300">
                    <x-site.arrow direction="back" />
                    {{ __('site.news.title') }}
                </a>
                <h1 class="mt-4 text-3xl font-semibold tracking-tight sm:text-5xl">
                    <x-localized :text="$article->localizedText('title')" />
                </h1>
                <p class="mt-6 flex flex-wrap items-center gap-x-2 text-sm text-brand-200">
                    <time datetime="{{ \App\Support\Localization\Dates::iso($article->published_at) }}">{{ \App\Support\Localization\Dates::long($article->published_at) }}</time>
                    @if ($article->category)
                        <span aria-hidden="true">&middot;</span>
                        <a href="{{ route('site.news.index', ['category' => $article->category->translate('slug')]) }}" class="hover:text-white">
                            <x-localized :text="$article->category->localizedText('name')" />
                        </a>
                    @endif
                </p>
            </div>
        </header>

        <div class="shell max-w-4xl py-14 lg:py-16">
            @if ($article->coverImageUrl())
                <img src="{{ $article->coverImageUrl() }}" alt="" class="mb-12 aspect-[16/9] w-full rounded-2xl object-cover">
            @endif

            <p class="mb-8 text-xl leading-relaxed text-slate-700">
                <x-localized :text="$article->localizedText('excerpt')" />
            </p>

            <x-rich-text :text="$article->localizedText('body')" />
        </div>
    </article>

    @if ($moreArticles->isNotEmpty())
        <section class="bg-slate-50 py-16 sm:py-20">
            <div class="shell">
                <h2 class="section-title">{{ __('site.news.more') }}</h2>
                <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($moreArticles as $more)
                        <x-site.article-card :article="$more" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.site>
