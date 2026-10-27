{{-- Sanitised CMS rich text, with a notice when it falls back to another language. --}}
@props(['text'])

@php
    $resolved = $text->resolveLocale();
    $isFallback = $text->isFallback() && $resolved;
@endphp

<div {{ $attributes }}>
    @if ($isFallback)
        <p class="fallback-notice">
            {{ __('site.fallback_notice', [
                'language' => __('site.languages.'.app()->getLocale()),
                'fallback' => __('site.languages.'.$resolved->value),
            ]) }}
        </p>
    @endif

    <div class="rich-text" @if ($isFallback) lang="{{ $resolved->value }}" dir="{{ $resolved->direction() }}" @endif>
        {{ \App\Support\Content\RichText::render($text->get()) }}
    </div>
</div>
