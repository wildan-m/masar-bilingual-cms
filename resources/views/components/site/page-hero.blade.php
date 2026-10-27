@props(['title', 'eyebrow' => null, 'intro' => null])

<section class="blueprint relative overflow-hidden bg-brand-950 text-white">
    <div class="shell py-16 sm:py-20">
        @if ($eyebrow)
            <p class="eyebrow">{{ $eyebrow }}</p>
        @endif
        <h1 class="mt-3 max-w-3xl text-3xl font-semibold tracking-tight sm:text-5xl">
            @if ($title instanceof \App\Support\Localization\LocalizedText)
                <x-localized :text="$title" />
            @else
                {{ $title }}
            @endif
        </h1>
        @if ($intro)
            <p class="mt-5 max-w-2xl text-lg text-brand-100">
                @if ($intro instanceof \App\Support\Localization\LocalizedText)
                    <x-localized :text="$intro" />
                @else
                    {{ $intro }}
                @endif
            </p>
        @endif
        {{ $slot }}
    </div>
</section>
