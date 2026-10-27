{{--
    Prints a translated value. When the current language has no translation
    and the fallback text is shown, the element is marked with the language
    it is actually written in, so browsers and screen readers handle it
    correctly (e.g. English text left-to-right inside an Arabic page).
--}}
@props(['text', 'as' => 'span'])

@php($resolved = $text->resolveLocale())

@if ($text->isFallback() && $resolved)
    <{{ $as }} {{ $attributes->merge(['lang' => $resolved->value, 'dir' => $resolved->direction()]) }}>{{ $text->get() }}</{{ $as }}>
@elseif ($attributes->isNotEmpty() || $as !== 'span')
    <{{ $as }} {{ $attributes }}>{{ $text->get() }}</{{ $as }}>
@else
    {{ $text->get() }}
@endif
