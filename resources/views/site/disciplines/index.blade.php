<x-layouts.site :seo="$seo">
    <x-site.page-hero :title="__('site.disciplines.title')" :intro="__('site.disciplines.intro')" />

    <section class="py-16 sm:py-20">
        <div class="shell grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($disciplines as $discipline)
                <x-site.discipline-card :discipline="$discipline" />
            @endforeach
        </div>
    </section>
</x-layouts.site>
