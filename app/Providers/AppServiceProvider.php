<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Discipline;
use App\Models\NewsCategory;
use App\Models\Page;
use App\Models\Project;
use App\Models\ProposalRequest;
use App\Models\Sector;
use App\Models\Setting;
use App\Models\User;
use App\Support\Localization\Locale;
use App\Support\Settings\SiteSettings;
use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(SiteSettings::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

        // Short, stable names for polymorphic columns (activity log subjects).
        Relation::enforceMorphMap([
            'activity_log' => ActivityLog::class,
            'article' => Article::class,
            'discipline' => Discipline::class,
            'news_category' => NewsCategory::class,
            'page' => Page::class,
            'project' => Project::class,
            'proposal_request' => ProposalRequest::class,
            'sector' => Sector::class,
            'setting' => Setting::class,
            'user' => User::class,
        ]);

        // Routes outside the {locale} tree (error pages, emails) still link
        // to the public site in the default language.
        URL::defaults(['locale' => Locale::fallback()->value]);

        RateLimiter::for('proposal-requests', fn (Request $request): Limit => Limit::perMinute(
            config('masar.rfp.rate_limit_per_minute'),
        )->by($request->ip()));

        Event::listen(Login::class, function (Login $event): void {
            if ($event->user instanceof User) {
                $event->user->forceFill(['last_login_at' => now()])->saveQuietly();
            }
        });
    }
}
