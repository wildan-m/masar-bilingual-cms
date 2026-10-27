<?php

namespace App\Http\Controllers\Site\Concerns;

use App\Support\Localization\Locale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;

trait ResolvesLocalizedSlugs
{
    /**
     * Find a record by its slug in the current locale.
     *
     * A slug from another locale (an English slug under /ar/, say)
     * permanently redirects to the record's canonical URL for the current
     * locale, so every page has exactly one address per language.
     *
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @return TModel
     */
    protected function findBySlugOrRedirect(Builder $query, string $slug): Model
    {
        $locale = Locale::current();

        $record = (clone $query)->whereLocalizedSlug($slug, $locale)->first()
            ?? (clone $query)->whereAnySlug($slug)->first();

        abort_if($record === null, 404);

        if ($record->slugFor($locale) !== $slug) {
            throw new HttpResponseException(redirect()->to($record->url($locale), 301));
        }

        return $record;
    }
}
