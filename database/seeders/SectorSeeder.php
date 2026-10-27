<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    public function run(): void
    {
        $sectors = [
            [['en' => 'Buildings and real estate', 'ar' => 'المباني والتطوير العقاري'], ['en' => 'buildings-and-real-estate', 'ar' => 'المباني-والتطوير-العقاري']],
            [['en' => 'Transport', 'ar' => 'النقل'], ['en' => 'transport', 'ar' => 'النقل']],
            [['en' => 'Utilities and water', 'ar' => 'المرافق والمياه'], ['en' => 'utilities-and-water', 'ar' => 'المرافق-والمياه']],
            [['en' => 'Education and healthcare', 'ar' => 'التعليم والرعاية الصحية'], ['en' => 'education-and-healthcare', 'ar' => 'التعليم-والرعاية-الصحية']],
            [['en' => 'Industry and energy', 'ar' => 'الصناعة والطاقة'], ['en' => 'industry-and-energy', 'ar' => 'الصناعة-والطاقة']],
        ];

        foreach ($sectors as $order => [$name, $slug]) {
            Sector::query()->create(['name' => $name, 'slug' => $slug, 'sort_order' => $order + 1]);
        }
    }
}
