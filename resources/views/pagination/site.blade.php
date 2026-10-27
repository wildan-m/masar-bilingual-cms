@if ($paginator->hasPages())
    <nav aria-label="{{ __('site.pagination.label') }}" class="mt-14 flex items-center justify-between gap-4 border-t border-slate-200 pt-6">
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center gap-2 text-sm text-slate-400">
                <x-site.arrow direction="back" />
                {{ __('site.pagination.previous') }}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-2 text-sm font-medium text-brand-700 hover:text-brand-900">
                <x-site.arrow direction="back" />
                {{ __('site.pagination.previous') }}
            </a>
        @endif

        <ul class="hidden items-center gap-1 sm:flex">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="px-3 text-sm text-slate-400">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="inline-flex size-9 items-center justify-center rounded-full bg-brand-900 text-sm font-semibold text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" aria-label="{{ __('site.pagination.page', ['page' => $page]) }}" class="inline-flex size-9 items-center justify-center rounded-full text-sm text-slate-700 hover:bg-brand-50">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach
        </ul>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-2 text-sm font-medium text-brand-700 hover:text-brand-900">
                {{ __('site.pagination.next') }}
                <x-site.arrow />
            </a>
        @else
            <span class="inline-flex items-center gap-2 text-sm text-slate-400">
                {{ __('site.pagination.next') }}
                <x-site.arrow />
            </span>
        @endif
    </nav>
@endif
