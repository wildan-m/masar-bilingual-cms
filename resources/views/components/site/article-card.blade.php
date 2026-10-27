@props(['article'])

<article class="group relative flex flex-col overflow-hidden rounded-2xl bg-white ring-1 ring-slate-200 transition hover:shadow-xl hover:shadow-brand-950/5">
    @if ($article->coverImageUrl())
        <div class="aspect-[16/9] overflow-hidden bg-brand-900">
            <img src="{{ $article->coverImageUrl() }}" alt="" loading="lazy"
                 class="size-full object-cover transition duration-500 group-hover:scale-105">
        </div>
    @endif

    <div class="flex flex-1 flex-col gap-3 p-6">
        <p class="flex flex-wrap items-center gap-x-2 text-xs font-semibold text-sand-600">
            <time datetime="{{ \App\Support\Localization\Dates::iso($article->published_at) }}">{{ \App\Support\Localization\Dates::long($article->published_at) }}</time>
            @if ($article->category)
                <span aria-hidden="true">&middot;</span>
                <x-localized :text="$article->category->localizedText('name')" />
            @endif
        </p>

        <h3 class="text-lg leading-snug font-semibold text-brand-950">
            <a href="{{ $article->url() }}" class="after:absolute after:inset-0 focus:outline-none">
                <x-localized :text="$article->localizedText('title')" />
            </a>
        </h3>

        <p class="line-clamp-3 text-sm leading-relaxed text-slate-600">
            <x-localized :text="$article->localizedText('excerpt')" />
        </p>

        <span class="mt-auto inline-flex items-center gap-1.5 pt-2 text-sm font-semibold text-brand-700">
            {{ __('site.cta.read_article') }}
            <x-site.arrow />
        </span>
    </div>
</article>
