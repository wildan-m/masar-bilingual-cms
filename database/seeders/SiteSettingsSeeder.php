<?php

namespace Database\Seeders;

use App\Support\Settings\SiteSettings;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(SiteSettings $settings): void
    {
        $settings->save([
            'company_name' => [
                'en' => 'Masar Engineering Consultants',
                'ar' => 'مسار للاستشارات الهندسية',
            ],
            'tagline' => [
                'en' => 'Engineering consultancy · Abu Dhabi since 2006',
                'ar' => 'استشارات هندسية · أبوظبي منذ 2006',
            ],
            'hero_title' => [
                'en' => 'Engineering the paths that cities grow along',
                'ar' => 'نصمّم المسارات التي تنمو عليها المدن',
            ],
            'hero_subtitle' => [
                'en' => 'Masar is an independent, multidisciplinary engineering consultancy. We design and supervise buildings, infrastructure and utilities for public and private clients across the UAE.',
                'ar' => 'مسار شركة استشارات هندسية مستقلة متعددة التخصصات، نصمّم المباني ومشاريع البنية التحتية والمرافق ونشرف على تنفيذها لصالح عملاء من القطاعين العام والخاص في مختلف أنحاء دولة الإمارات.',
            ],
            'address' => [
                'en' => 'Level 12, Masar House, Hamdan Bin Mohammed Street, Abu Dhabi, United Arab Emirates',
                'ar' => 'الطابق 12، مبنى مسار، شارع حمدان بن محمد، أبوظبي، الإمارات العربية المتحدة',
            ],
            'office_hours' => [
                'en' => 'Monday to Friday, 8:00 to 17:00',
                'ar' => 'من الاثنين إلى الجمعة، من 8:00 صباحًا حتى 5:00 مساءً',
            ],
            'default_meta_description' => [
                'en' => 'Masar Engineering Consultants is an Abu Dhabi engineering consultancy providing structural, MEP, infrastructure, geotechnical and sustainability design and construction supervision.',
                'ar' => 'مسار للاستشارات الهندسية شركة استشارات هندسية في أبوظبي تقدّم خدمات التصميم الإنشائي والكهروميكانيكي وتصميم البنية التحتية والهندسة الجيوتقنية والاستدامة والإشراف على التنفيذ.',
            ],
            'phone' => '+971 2 000 0148',
            'email' => 'info@masar.test',
            'proposals_email' => 'proposals@masar.test',
            'founded_year' => 2006,
            'map_url' => 'https://www.google.com/maps/search/?api=1&query=Hamdan+Bin+Mohammed+Street+Abu+Dhabi',
        ]);
    }
}
