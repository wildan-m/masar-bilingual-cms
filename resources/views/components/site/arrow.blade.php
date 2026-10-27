{{-- Arrow that follows the reading direction: "forward" points right in English and left in Arabic. --}}
@props(['direction' => 'forward'])

<svg {{ $attributes->class(['size-4 shrink-0', 'rtl:-scale-x-100' => $direction === 'forward', '-scale-x-100 rtl:scale-x-100' => $direction === 'back']) }} viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
    <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
</svg>
