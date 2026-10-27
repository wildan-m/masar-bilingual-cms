<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Article;
use App\Models\NewsCategory;
use App\Models\User;
use Database\Seeders\Support\CoverArt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            'company-news' => [['en' => 'Company news', 'ar' => 'أخبار الشركة'], ['en' => 'company-news', 'ar' => 'أخبار-الشركة']],
            'project-updates' => [['en' => 'Project updates', 'ar' => 'مستجدات المشاريع'], ['en' => 'project-updates', 'ar' => 'مستجدات-المشاريع']],
            'insights' => [['en' => 'Insights', 'ar' => 'رؤى هندسية'], ['en' => 'insights', 'ar' => 'رؤى-هندسية']],
        ])->map(fn (array $category, string $key): NewsCategory => NewsCategory::query()->create([
            'name' => $category[0],
            'slug' => $category[1],
            'sort_order' => array_search($key, ['company-news', 'project-updates', 'insights'], true) + 1,
        ]));

        $author = User::query()->where('role', Role::Editor)->first();

        foreach ($this->articles() as $index => $data) {
            $cover = null;

            if (isset($data['scene'])) {
                $cover = 'news/'.$data['slug']['en'].'.jpg';
                Storage::disk('public')->put($cover, CoverArt::render($data['scene'], $data['palette'], $index + 51));
            }

            Article::query()->create([
                ...collect($data)->except(['scene', 'palette', 'category', 'date'])->all(),
                'news_category_id' => $categories[$data['category']]->getKey(),
                'author_id' => $author?->getKey(),
                'cover_image' => $cover,
                'is_published' => true,
                'published_at' => Carbon::parse($data['date'].' 09:00', config('masar.display_timezone'))->utc(),
            ]);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function articles(): array
    {
        return [
            [
                // Published in English first; the Arabic translation is pending.
                'category' => 'project-updates', 'date' => '2026-10-06', 'scene' => 'bridge', 'palette' => 'night',
                'title' => ['en' => 'Yas Waterfront Pedestrian Bridge wins regional structural award'],
                'slug' => ['en' => 'yas-waterfront-bridge-wins-structural-award'],
                'excerpt' => ['en' => 'The 96-metre steel arch footbridge was recognised for its slender design and its night-time installation over a live marina channel.'],
                'body' => ['en' => '<p>The Yas Waterfront Pedestrian Bridge has received the pedestrian structures award at a regional structural engineering awards ceremony in Dubai.</p><p>The judges noted the slenderness of the 96-metre arch and the planning behind the installation, which lifted the steel segments into place during two night-time closures of the marina channel.</p>'],
            ],
            [
                'category' => 'company-news', 'date' => '2026-09-14', 'scene' => 'towers', 'palette' => 'sea',
                'title' => [
                    'en' => 'Masar opens a Dubai office to support growing work in the northern emirates',
                    'ar' => 'مسار تفتتح مكتبًا في دبي لمواكبة تنامي أعمالها في الإمارات الشمالية',
                ],
                'slug' => ['en' => 'masar-opens-dubai-office', 'ar' => 'مسار-تفتتح-مكتبا-في-دبي'],
                'excerpt' => [
                    'en' => 'The new office in Dubai Hills brings our design and site supervision teams closer to clients in Dubai and the northern emirates.',
                    'ar' => 'يقرّب المكتب الجديد في دبي هيلز فرق التصميم والإشراف على التنفيذ لدينا من عملائنا في دبي والإمارات الشمالية.',
                ],
                'body' => [
                    'en' => '<p>Masar Engineering Consultants has opened its second office, in Dubai Hills, to support a growing number of projects in Dubai, Sharjah and Ras Al Khaimah. The office will host 40 engineers and site supervisors by the end of the year.</p><p>"Half of our current project hours are now north of Abu Dhabi," said the firm\'s managing director. "Being closer to our clients and sites means faster decisions and more time on site for our supervision teams."</p><p>The Abu Dhabi studio remains the firm\'s headquarters and the home of its design disciplines.</p>',
                    'ar' => '<p>افتتحت مسار للاستشارات الهندسية مكتبها الثاني في دبي هيلز لمواكبة العدد المتزايد من المشاريع في دبي والشارقة ورأس الخيمة. وسيضم المكتب 40 مهندسًا ومشرفًا للمواقع بحلول نهاية العام.</p><p>وقال المدير العام للشركة: «باتت نصف ساعات العمل في مشاريعنا الحالية شمال أبوظبي، والقرب من عملائنا ومواقعنا يعني قرارات أسرع ووقتًا أطول في المواقع لفرق الإشراف لدينا».</p><p>ويبقى استوديو أبوظبي المقر الرئيسي للشركة وموطن تخصصاتها التصميمية.</p>',
                ],
            ],
            [
                'category' => 'project-updates', 'date' => '2026-07-02', 'scene' => 'solar', 'palette' => 'dusk',
                'title' => [
                    'en' => 'First phase of the Ruwais solar carports energised',
                    'ar' => 'تشغيل المرحلة الأولى من مظلات المواقف الشمسية في الرويس',
                ],
                'slug' => ['en' => 'ruwais-solar-carports-first-phase-energised', 'ar' => 'تشغيل-المرحلة-الأولى-من-مظلات-الرويس'],
                'excerpt' => [
                    'en' => 'Eleven car parks now generate 1.6 MWp of solar power for staff housing in Ruwais, the first of three phases.',
                    'ar' => 'باتت 11 ساحة مواقف تولّد 1.6 ميغاواط ذروة من الطاقة الشمسية لمساكن الموظفين في الرويس، ضمن المرحلة الأولى من ثلاث مراحل.',
                ],
                'body' => [
                    'en' => '<p>The first phase of the Ruwais Solar Carport Programme has been energised. Eleven car parks serving staff housing now shade 1,450 vehicles and generate 1.6 MWp of solar power.</p><p>Masar designed a standard family of steel carport structures that are fabricated off site and bolted together on prepared foundations, allowing each car park to be completed in about six weeks.</p><p>Phases two and three will extend the programme to office and community car parks, bringing total capacity to 4.8 MWp.</p>',
                    'ar' => '<p>شُغّلت المرحلة الأولى من برنامج مظلات المواقف الشمسية في الرويس، إذ باتت 11 ساحة مواقف تخدم مساكن الموظفين توفّر الظل لـ 1450 مركبة وتولّد 1.6 ميغاواط ذروة من الطاقة الشمسية.</p><p>وصمّمت مسار مجموعة قياسية من هياكل المظلات الفولاذية تُصنَّع خارج الموقع وتُجمَّع بالبراغي على أساسات مُعدّة مسبقًا، ما يتيح إنجاز كل ساحة مواقف في نحو ستة أسابيع.</p><p>وستوسّع المرحلتان الثانية والثالثة البرنامج ليشمل مواقف المكاتب والمرافق المجتمعية، لتصل القدرة الإجمالية إلى 4.8 ميغاواط ذروة.</p>',
                ],
            ],
            [
                'category' => 'insights', 'date' => '2026-05-18', 'scene' => 'pipes', 'palette' => 'sand',
                'title' => [
                    'en' => 'Designing foundations on coastal sabkha: five lessons from the field',
                    'ar' => 'تصميم الأساسات على السبخات الساحلية: خمسة دروس من الميدان',
                ],
                'slug' => ['en' => 'foundations-on-coastal-sabkha', 'ar' => 'الأساسات-على-السبخات-الساحلية'],
                'excerpt' => [
                    'en' => 'Our geotechnical team shares what twenty years of projects on the Gulf coast have taught us about building on sabkha.',
                    'ar' => 'يشارك فريقنا الجيوتقني ما تعلّمناه من عشرين عامًا من المشاريع على ساحل الخليج حول البناء على السبخات.',
                ],
                'body' => [
                    'en' => '<p>Sabkha, the salt-flat soil found along much of the Gulf coast, combines low strength, aggressive salts and a water table close to the surface. These are five lessons our geotechnical engineers apply on every coastal project.</p><ol><li><strong>Investigate early.</strong> A well-planned site investigation costs a fraction of a foundation redesign during construction.</li><li><strong>Plan for the chemistry.</strong> Chlorides and sulphates call for sulphate-resisting concrete, increased cover and, in some cases, protective membranes.</li><li><strong>Treat dewatering as a design item.</strong> Excavation support and dewatering often decide the programme of a basement.</li><li><strong>Consider ground improvement.</strong> For light industrial buildings, vibro-compaction or dynamic compaction can replace piling altogether.</li><li><strong>Test, then optimise.</strong> Preliminary pile load tests let us refine pile lengths before hundreds of piles are installed.</li></ol>',
                    'ar' => '<p>تجمع السبخة، وهي التربة الملحية المنتشرة على امتداد معظم ساحل الخليج، بين ضعف قدرة التحمّل والأملاح العدوانية وقرب منسوب المياه الجوفية من السطح. وفيما يلي خمسة دروس يطبّقها مهندسونا الجيوتقنيون في كل مشروع ساحلي.</p><ol><li><strong>افحص التربة مبكرًا.</strong> تكلفة برنامج فحص تربة مخطط جيدًا جزء يسير من تكلفة إعادة تصميم الأساسات أثناء التنفيذ.</li><li><strong>خطّط لكيمياء التربة.</strong> تتطلب الكلوريدات والكبريتات خرسانة مقاومة للكبريتات وغطاءً خرسانيًا أكبر، وفي بعض الحالات أغشية حماية.</li><li><strong>تعامل مع نزح المياه بوصفه بندًا تصميميًا.</strong> كثيرًا ما يحدّد سند جوانب الحفر ونزح المياه الجدول الزمني لتنفيذ الطوابق السفلية.</li><li><strong>فكّر في تحسين التربة.</strong> في المباني الصناعية الخفيفة، قد يغني الدمك بالاهتزاز أو الدمك الديناميكي عن الخوازيق تمامًا.</li><li><strong>اختبر ثم حسّن.</strong> تتيح اختبارات التحميل المبدئية للخوازيق ضبط أطوالها قبل تنفيذ المئات منها.</li></ol>',
                ],
            ],
            [
                'category' => 'company-news', 'date' => '2026-03-02',
                'title' => [
                    'en' => 'Masar renews ISO 9001, 14001 and 45001 certification',
                    'ar' => 'مسار تجدّد شهادات الأيزو 9001 و14001 و45001',
                ],
                'slug' => ['en' => 'iso-certification-renewed', 'ar' => 'تجديد-شهادات-الأيزو'],
                'excerpt' => [
                    'en' => 'An external audit has renewed the certification of our integrated management system for quality, environment and occupational health and safety.',
                    'ar' => 'جدّد تدقيق خارجي اعتماد نظام الإدارة المتكامل لدينا في مجالات الجودة والبيئة والصحة والسلامة المهنية.',
                ],
                'body' => [
                    'en' => '<p>Following a five-day external audit of our Abu Dhabi and Dubai offices and three active construction sites, Masar\'s integrated management system has been recertified to ISO 9001, ISO 14001 and ISO 45001.</p><p>The auditors highlighted the firm\'s digital site inspection records and the near-miss reporting culture on its supervision projects.</p>',
                    'ar' => '<p>بعد تدقيق خارجي امتد خمسة أيام وشمل مكتبينا في أبوظبي ودبي وثلاثة مواقع إنشائية قيد التنفيذ، أُعيد اعتماد نظام الإدارة المتكامل في مسار وفق معايير الأيزو 9001 و14001 و45001.</p><p>وأشاد المدققون بسجلات التفتيش الرقمية في المواقع، وبثقافة الإبلاغ عن الحوادث الوشيكة في مشاريع الإشراف.</p>',
                ],
            ],
            [
                'category' => 'insights', 'date' => '2026-01-20', 'scene' => 'campus', 'palette' => 'sand',
                'title' => [
                    'en' => 'What the updated Estidama requirements mean for school projects',
                    'ar' => 'ماذا تعني متطلبات «استدامة» المحدّثة لمشاريع المدارس؟',
                ],
                'slug' => ['en' => 'estidama-requirements-for-schools', 'ar' => 'متطلبات-استدامة-لمشاريع-المدارس'],
                'excerpt' => [
                    'en' => 'Tighter energy and water targets are best met at concept stage. Our sustainability team explains where the savings come from.',
                    'ar' => 'أفضل طريقة لتحقيق مستهدفات الطاقة والمياه الأكثر صرامة هي معالجتها في مرحلة الفكرة. ويوضح فريق الاستدامة لدينا مصادر هذه الوفورات.',
                ],
                'body' => [
                    'en' => '<p>School buildings are occupied during the hottest hours of the day, which makes cooling the dominant energy load. The updated Estidama requirements raise the bar for both energy and water performance.</p><h2>Where the savings come from</h2><ul><li>Courtyard layouts and shading that reduce solar gains before any equipment is sized</li><li>Demand-controlled ventilation linked to classroom occupancy</li><li>Rooftop solar sized to the school\'s daytime load profile</li><li>Condensate recovery and low-flow fixtures for irrigation and washrooms</li></ul><p>On our Khalifa City campus these measures achieved a 3 Pearl rating with no increase in the construction budget.</p>',
                    'ar' => '<p>تُشغَل المباني المدرسية خلال أشد ساعات النهار حرارة، ما يجعل التبريد الحمل الأكبر على استهلاك الطاقة. وترفع متطلبات «استدامة» المحدّثة مستوى الأداء المطلوب في استهلاك الطاقة والمياه على حد سواء.</p><h2>من أين تأتي الوفورات؟</h2><ul><li>تخطيط يعتمد على الأفنية والتظليل لتقليل اكتساب الحرارة الشمسية قبل تحديد أحجام المعدات</li><li>تهوية مرتبطة بمستوى إشغال الفصول الدراسية</li><li>منظومة طاقة شمسية على الأسطح تتناسب مع أحمال المدرسة خلال النهار</li><li>استعادة مياه التكثيف وتركيبات صحية منخفضة التدفق للري ودورات المياه</li></ul><p>وقد حققت هذه الإجراءات في حرمنا المدرسي بمدينة خليفة تقييم ثلاث لآلئ دون أي زيادة في ميزانية الإنشاء.</p>',
                ],
            ],
        ];
    }
}
