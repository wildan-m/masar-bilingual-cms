<x-layouts.site :seo="$seo">
    <x-site.page-hero :title="__('site.news.title')" :intro="__('site.news.intro')" />

    <section class="py-16 sm:py-20">
        <div class="shell">
            @if ($categories->isNotEmpty())
                <nav aria-label="{{ __('site.news.filter_label') }}">
                    <ul class="flex flex-wrap gap-2">
                        <li>
                            <a href="{{ route('site.news.index') }}" class="chip" @unless ($activeCategory) aria-current="page" @endunless>
                                {{ __('site.news.all_categories') }}
                            </a>
                        </li>
                        @foreach ($categories as $category)
                            <li>
                                <a href="{{ route('site.news.index', ['category' => $category->translate('slug')]) }}" class="chip"
                                   @if ($activeCategory?->is($category)) aria-current="page" @endif>
                                    <x-localized :text="$category->localizedText('name')" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endif

            @if ($articles->isEmpty())
                <p class="mt-12 rounded-2xl bg-slate-50 p-10 text-center text-slate-600">{{ __('site.news.empty') }}</p>
            @else
                <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($articles as $article)
                        <x-site.article-card :article="$article" />
                    @endforeach
                </div>

                {{ $articles->links('pagination.site') }}
            @endif
        </div>
    </section>
</x-layouts.site>
