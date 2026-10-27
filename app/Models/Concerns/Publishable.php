<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

/**
 * Records with an `is_published` flag and, optionally, a `published_at`
 * timestamp that schedules them.
 */
trait Publishable
{
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where($this->qualifyColumn('is_published'), true);

        if ($this->usesPublicationDate()) {
            $query->whereNotNull($this->qualifyColumn('published_at'))
                ->where($this->qualifyColumn('published_at'), '<=', now());
        }
    }

    public function isPublished(): bool
    {
        if (! $this->is_published) {
            return false;
        }

        return ! $this->usesPublicationDate()
            || ($this->published_at !== null && $this->published_at->lte(now()));
    }

    protected function usesPublicationDate(): bool
    {
        return false;
    }
}
