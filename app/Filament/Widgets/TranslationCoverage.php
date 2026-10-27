<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\Discipline;
use App\Models\Page;
use App\Models\Project;
use App\Support\Localization\Locale;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

/**
 * How much of the website content is fully translated into Arabic.
 */
class TranslationCoverage extends StatsOverviewWidget
{
    protected static ?int $sort = 20;

    protected ?string $heading = 'Arabic translation coverage';

    public static function canView(): bool
    {
        return Gate::allows('viewAny', Project::class);
    }

    protected function getStats(): array
    {
        return [
            $this->stat('Pages', Page::class),
            $this->stat('Disciplines', Discipline::class),
            $this->stat('Projects', Project::class),
            $this->stat('News articles', Article::class),
        ];
    }

    /**
     * @param  class-string<Model>  $model
     */
    private function stat(string $label, string $model): Stat
    {
        $total = $model::query()->count();
        $missing = $model::query()->missingTranslation(Locale::Arabic)->count();

        return Stat::make($label, ($total - $missing).' / '.$total)
            ->description($missing === 0 ? 'Fully translated' : "{$missing} need Arabic")
            ->color($missing === 0 ? 'success' : 'warning');
    }
}
