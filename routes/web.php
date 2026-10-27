<?php

use App\Http\Controllers\Admin\OpenProposalRequestController;
use App\Http\Controllers\Admin\ProposalRequestAttachmentController;
use App\Http\Controllers\LocaleRedirectController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\Site\DisciplineController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\NewsController;
use App\Http\Controllers\Site\PageController;
use App\Http\Controllers\Site\ProjectController;
use App\Http\Controllers\Site\ProposalRequestController;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\BlockHoneypotSubmissions;
use App\Http\Middleware\SetLocale;
use App\Support\Localization\Locale;
use Illuminate\Support\Facades\Route;

Route::get('/', LocaleRedirectController::class)->name('root');
Route::get('sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('robots.txt', RobotsController::class)->name('robots');

// Staff-only endpoints that live next to the Filament panel.
Route::prefix('admin/proposal-requests/{proposalRequest}')
    ->name('admin.proposal-requests.')
    ->group(function (): void {
        Route::get('attachment', ProposalRequestAttachmentController::class)
            ->middleware('auth')
            ->name('attachment');

        Route::get('open', OpenProposalRequestController::class)
            ->middleware(['signed', 'auth'])
            ->name('open');
    });

// Public website, one URL tree per language: /en/... and /ar/...
Route::prefix('{locale}')
    ->where(['locale' => Locale::routePattern()])
    ->middleware(SetLocale::class)
    ->name('site.')
    ->group(function (): void {
        Route::get('/', HomeController::class)->name('home');

        Route::get('disciplines', [DisciplineController::class, 'index'])->name('disciplines.index');
        Route::get('disciplines/{slug}', [DisciplineController::class, 'show'])->name('disciplines.show');

        Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');

        Route::get('news', [NewsController::class, 'index'])->name('news.index');
        Route::get('news/{slug}', [NewsController::class, 'show'])->name('news.show');

        Route::get('request-for-proposal', [ProposalRequestController::class, 'create'])->name('rfp.create');
        Route::post('request-for-proposal', [ProposalRequestController::class, 'store'])
            ->middleware(['throttle:proposal-requests', BlockHoneypotSubmissions::class.':site.rfp.thanks'])
            ->name('rfp.store');
        Route::get('request-for-proposal/thank-you', [ProposalRequestController::class, 'thanks'])->name('rfp.thanks');

        // CMS pages by localized slug; keep this route last.
        Route::get('{slug}', PageController::class)->name('pages.show');
    });
