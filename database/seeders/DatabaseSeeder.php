<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Support\Activity\ActivityLogger;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database. Safe to run more than once: accounts
     * are updated in place and content is only created on an empty database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);

        if (Project::query()->exists()) {
            $this->command?->warn('Website content already exists, skipping content seeders.');

            return;
        }

        // Initial content is not an editorial change, so it stays out of the activity log.
        ActivityLogger::withoutLogging(fn () => $this->call([
            SiteSettingsSeeder::class,
            PageSeeder::class,
            DisciplineSeeder::class,
            SectorSeeder::class,
            ProjectSeeder::class,
            NewsSeeder::class,
        ]));

        $this->call(ProposalRequestSeeder::class);
    }
}
