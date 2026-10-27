<x-layouts.site :seo="$seo">
    <x-site.page-hero :title="$page->localizedText('title')" />

    <section class="py-16 sm:py-20">
        <div class="shell max-w-4xl">
            <x-rich-text :text="$page->localizedText('body')" />
        </div>
    </section>
</x-layouts.site>
