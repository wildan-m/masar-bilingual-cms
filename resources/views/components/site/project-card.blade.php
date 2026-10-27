@props(['project'])

<article class="group relative flex flex-col overflow-hidden rounded-2xl bg-white ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-xl hover:shadow-brand-950/5">
    <div class="aspect-[4/3] overflow-hidden bg-brand-900">
        @if ($project->coverImageUrl())
            <img src="{{ $project->coverImageUrl() }}" alt="" loading="lazy"
                 class="size-full object-cover transition duration-500 group-hover:scale-105">
        @else
            <div class="blueprint size-full"></div>
        @endif
    </div>

    <div class="flex flex-1 flex-col gap-3 p-6">
        <p class="flex flex-wrap items-center gap-x-2 text-xs font-semibold text-sand-600">
            @if ($project->sector)
                <x-localized :text="$project->sector->localizedText('name')" />
                <span aria-hidden="true">&middot;</span>
            @endif
            <span>{{ $project->isOngoing() ? __('site.projects.ongoing') : $project->completion_year }}</span>
        </p>

        <h3 class="text-lg leading-snug font-semibold text-brand-950">
            <a href="{{ $project->url() }}" class="after:absolute after:inset-0 focus:outline-none">
                <x-localized :text="$project->localizedText('title')" />
            </a>
        </h3>

        <p class="line-clamp-3 text-sm leading-relaxed text-slate-600">
            <x-localized :text="$project->localizedText('summary')" />
        </p>

        @unless ($project->localizedText('location')->isEmpty())
            <p class="mt-auto flex items-center gap-1.5 pt-2 text-sm text-slate-500">
                <svg class="size-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="m9.69 18.933.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 0 0 .281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 1 0 3 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 0 0 2.273 1.765 11.842 11.842 0 0 0 .976.544l.062.029.018.008.006.003ZM10 11.25a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" clip-rule="evenodd" /></svg>
                <x-localized :text="$project->localizedText('location')" />
            </p>
        @endunless
    </div>
</article>
