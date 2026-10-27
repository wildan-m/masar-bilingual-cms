<?php

namespace Database\Seeders;

use App\Enums\ClientType;
use App\Models\Discipline;
use App\Models\Project;
use App\Models\Sector;
use Database\Seeders\Support\CoverArt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $sectors = Sector::all()->keyBy(fn (Sector $sector): string => $sector->translate('slug', 'en'));
        $disciplines = Discipline::all()->keyBy(fn (Discipline $discipline): string => $discipline->translate('slug', 'en'));

        foreach ($this->projects() as $index => $data) {
            $cover = 'projects/'.$data['slug']['en'].'.jpg';
            Storage::disk('public')->put($cover, CoverArt::render($data['scene'], $data['palette'], $index + 11));

            $project = Project::query()->create([
                ...collect($data)->except(['scene', 'palette', 'sector', 'disciplines'])->all(),
                'sector_id' => $sectors[$data['sector']]->getKey(),
                'cover_image' => $cover,
                'is_published' => true,
            ]);

            $project->disciplines()->attach(collect($data['disciplines'])->map(fn (string $slug): int => $disciplines[$slug]->getKey()));
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function projects(): array
    {
        return [
            [
                'scene' => 'towers', 'palette' => 'dusk',
                'sector' => 'buildings-and-real-estate',
                'disciplines' => ['structural-engineering', 'mep-engineering', 'sustainability-and-energy'],
                'client_type' => ClientType::Developer, 'completion_year' => 2023, 'is_featured' => true,
                'title' => ['en' => 'Corniche Residences Tower', 'ar' => 'برج كورنيش ريزيدنسز'],
                'slug' => ['en' => 'corniche-residences-tower', 'ar' => 'برج-كورنيش-ريزيدنسز'],
                'location' => ['en' => 'Corniche Road, Abu Dhabi', 'ar' => 'شارع الكورنيش، أبوظبي'],
                'summary' => [
                    'en' => 'A 42-storey residential tower with 310 apartments, a podium car park and a rooftop amenity deck overlooking the Corniche.',
                    'ar' => 'برج سكني من 42 طابقًا يضم 310 شقق ومواقف سيارات في المنصة وسطحًا للمرافق الترفيهية يطل على الكورنيش.',
                ],
                'body' => [
                    'en' => '<p>Masar was appointed lead design consultant and engineer of record for the tower, which replaced a low-rise office block on a tight waterfront plot. The structure uses a reinforced concrete core and outrigger walls to control wind sway, with post-tensioned flat slabs that kept floor-to-floor heights to 3.2 metres.</p><p>The MEP design connects the building to the district cooling network and recovers condensate for irrigation. The project achieved a 2 Pearl Estidama rating.</p><h2>Our role</h2><ul><li>Structural and MEP design from concept to construction issue</li><li>Wind tunnel testing coordination</li><li>Estidama certification management</li><li>Site supervision during the superstructure works</li></ul>',
                    'ar' => '<p>عُيّنت مسار استشاريًا رئيسيًا للتصميم ومهندسًا مسؤولًا عن البرج الذي حلّ محل مبنى مكاتب منخفض على قطعة أرض ضيقة على الواجهة البحرية. ويعتمد النظام الإنشائي على نواة من الخرسانة المسلحة وجدران ربط تحدّ من تمايل المبنى بفعل الرياح، مع بلاطات مسطحة لاحقة الشد حافظت على ارتفاع الطابق عند 3.2 أمتار.</p><p>ويربط التصميم الكهروميكانيكي المبنى بشبكة تبريد المناطق، ويستعيد مياه التكثيف لاستخدامها في الري. وحصل المشروع على تقييم لؤلؤتين ضمن نظام «استدامة».</p><h2>دورنا في المشروع</h2><ul><li>التصميم الإنشائي والكهروميكانيكي من الفكرة حتى مخططات التنفيذ</li><li>تنسيق اختبارات نفق الرياح</li><li>إدارة الحصول على تقييم «استدامة»</li><li>الإشراف على التنفيذ خلال أعمال الهيكل العلوي</li></ul>',
                ],
            ],
            [
                'scene' => 'flyover', 'palette' => 'sand',
                'sector' => 'transport',
                'disciplines' => ['infrastructure-and-roads', 'geotechnical-engineering', 'project-and-construction-management'],
                'client_type' => ClientType::Government, 'completion_year' => 2022, 'is_featured' => true,
                'title' => ['en' => 'Al Falah Interchange Upgrade', 'ar' => 'تطوير تقاطع الفلاح'],
                'slug' => ['en' => 'al-falah-interchange-upgrade', 'ar' => 'تطوير-تقاطع-الفلاح'],
                'location' => ['en' => 'Al Falah, Abu Dhabi', 'ar' => 'الفلاح، أبوظبي'],
                'summary' => [
                    'en' => 'Conversion of a congested roundabout into a two-level interchange with new service roads, cycle lanes and relocated utilities.',
                    'ar' => 'تحويل دوار مزدحم إلى تقاطع من مستويين مع طرق خدمة جديدة ومسارات للدراجات ونقل للمرافق.',
                ],
                'body' => [
                    'en' => '<p>The upgrade replaced a signal-controlled roundabout serving a fast-growing residential community. Our design moved through traffic onto a 420-metre flyover and reorganised local movements on a signalised junction at grade.</p><p>More than 30 utility crossings had to be diverted. We sequenced the diversions and the traffic management so that the junction stayed open throughout construction.</p><h2>Our role</h2><ul><li>Traffic modelling and preliminary design</li><li>Detailed road, structure and drainage design</li><li>Utility diversion design and authority approvals</li><li>Construction supervision</li></ul>',
                    'ar' => '<p>حلّ التطوير محل دوار تنظّمه الإشارات الضوئية ويخدم مجتمعًا سكنيًا سريع النمو. وفصل تصميمنا حركة المرور العابرة على جسر علوي بطول 420 مترًا، وأعاد تنظيم الحركة المحلية على تقاطع سطحي مزوّد بإشارات ضوئية.</p><p>تطلّب المشروع تحويل أكثر من 30 تقاطعًا للمرافق، فوضعنا تسلسلًا لأعمال التحويل وإدارة الحركة المرورية يُبقي التقاطع مفتوحًا طوال فترة التنفيذ.</p><h2>دورنا في المشروع</h2><ul><li>النمذجة المرورية والتصميم الأولي</li><li>التصميم التفصيلي للطرق والمنشآت والتصريف</li><li>تصميم تحويل المرافق والحصول على موافقات الجهات المعنية</li><li>الإشراف على التنفيذ</li></ul>',
                ],
            ],
            [
                'scene' => 'campus', 'palette' => 'sea',
                'sector' => 'education-and-healthcare',
                'disciplines' => ['structural-engineering', 'mep-engineering', 'sustainability-and-energy', 'project-and-construction-management'],
                'client_type' => ClientType::SemiGovernment, 'completion_year' => 2024, 'is_featured' => true,
                'title' => ['en' => 'Khalifa City School Campus', 'ar' => 'حرم مدرسي في مدينة خليفة'],
                'slug' => ['en' => 'khalifa-city-school-campus', 'ar' => 'حرم-مدرسي-في-مدينة-خليفة'],
                'location' => ['en' => 'Khalifa City, Abu Dhabi', 'ar' => 'مدينة خليفة، أبوظبي'],
                'summary' => [
                    'en' => 'A K-12 campus for 1,800 students with shaded courtyards, a sports hall and a 3 Pearl Estidama rating.',
                    'ar' => 'حرم مدرسي لجميع المراحل يتسع لـ 1800 طالب، يضم أفنية مظللة وصالة رياضية، وحاصل على تقييم ثلاث لآلئ ضمن نظام «استدامة».',
                ],
                'body' => [
                    'en' => '<p>The campus is organised around shaded courtyards that let students move between buildings in comfort for most of the year. Teaching blocks use precast concrete frames, which allowed the structure to be completed within a single summer break.</p><p>High-performance glazing, demand-controlled ventilation and a 450 kWp rooftop solar system reduce energy use by 38% compared with the reference building.</p>',
                    'ar' => '<p>يتوزّع الحرم المدرسي حول أفنية مظللة تتيح للطلاب التنقل بين المباني براحة معظم أيام السنة. واستُخدمت هياكل خرسانية سابقة الصب في المباني التعليمية، ما سمح بإنجاز الهيكل الإنشائي خلال عطلة صيفية واحدة.</p><p>ويسهم الزجاج عالي الأداء والتهوية المرتبطة بالإشغال ومنظومة طاقة شمسية بقدرة 450 كيلوواط ذروة على الأسطح في خفض استهلاك الطاقة بنسبة 38% مقارنة بالمبنى المرجعي.</p>',
                ],
            ],
            [
                'scene' => 'pipes', 'palette' => 'dusk',
                'sector' => 'utilities-and-water',
                'disciplines' => ['infrastructure-and-roads', 'geotechnical-engineering'],
                'client_type' => ClientType::Government, 'completion_year' => 2021,
                'title' => ['en' => 'Mussafah Industrial Stormwater Network', 'ar' => 'شبكة تصريف مياه الأمطار في مصفح الصناعية'],
                'slug' => ['en' => 'mussafah-industrial-stormwater-network', 'ar' => 'شبكة-تصريف-مياه-الأمطار-في-مصفح'],
                'location' => ['en' => 'Mussafah, Abu Dhabi', 'ar' => 'مصفح، أبوظبي'],
                'summary' => [
                    'en' => 'A 14-kilometre gravity and pumped drainage network that ended seasonal flooding across an industrial district.',
                    'ar' => 'شبكة تصريف بالجاذبية والضخ بطول 14 كيلومترًا أنهت تجمّع مياه الأمطار الموسمي في منطقة صناعية.',
                ],
                'body' => [
                    'en' => '<p>Short, intense storms regularly flooded the industrial blocks of Mussafah, disrupting businesses and damaging roads. Masar modelled the catchment, identified the bottlenecks and designed a network of gravity pipes, two pumping stations and an outfall.</p><p>Because of the high groundwater table, the deep sections were designed for construction inside sheet-piled trenches with continuous dewatering.</p>',
                    'ar' => '<p>كانت العواصف المطرية القصيرة والغزيرة تُغرق المربعات الصناعية في مصفح بانتظام، فتعطّل الأعمال وتلحق الضرر بالطرق. فأعدّت مسار نموذجًا لمنطقة التجميع، وحدّدت مواضع الاختناق، وصمّمت شبكة من الأنابيب العاملة بالجاذبية ومحطتي ضخ ومصبًّا نهائيًا.</p><p>ونظرًا لارتفاع منسوب المياه الجوفية، صُمّمت المقاطع العميقة لتُنفَّذ داخل خنادق مدعّمة بالألواح الصفائحية مع نزح مستمر للمياه.</p>',
                ],
            ],
            [
                'scene' => 'bridge', 'palette' => 'sea',
                'sector' => 'transport',
                'disciplines' => ['structural-engineering', 'geotechnical-engineering'],
                'client_type' => ClientType::Developer, 'completion_year' => 2023,
                'title' => ['en' => 'Yas Waterfront Pedestrian Bridge', 'ar' => 'جسر المشاة على واجهة ياس البحرية'],
                'slug' => ['en' => 'yas-waterfront-pedestrian-bridge', 'ar' => 'جسر-المشاة-على-واجهة-ياس'],
                'location' => ['en' => 'Yas Island, Abu Dhabi', 'ar' => 'جزيرة ياس، أبوظبي'],
                'summary' => [
                    'en' => 'A 96-metre steel arch footbridge linking a hotel district to the marina promenade.',
                    'ar' => 'جسر مشاة بقوس فولاذي بطول 96 مترًا يربط منطقة الفنادق بممشى المرسى.',
                ],
                'body' => [
                    'en' => '<p>The bridge crosses a navigation channel with a single steel arch, leaving the water clear for marina traffic. Dynamic analysis was used to tune the deck so that it stays comfortable for pedestrians during events with large crowds.</p><p>The steel segments were fabricated in Abu Dhabi and lifted into place during two night-time closures of the channel.</p>',
                    'ar' => '<p>يعبر الجسر قناة ملاحية بقوس فولاذي واحد، ما يُبقي المجرى المائي مفتوحًا أمام حركة القوارب في المرسى. واستُخدم التحليل الديناميكي لضبط سطح الجسر بحيث يبقى مريحًا للمشاة خلال الفعاليات التي تشهد حشودًا كبيرة.</p><p>وصُنّعت الأجزاء الفولاذية في أبوظبي ورُفعت إلى مواقعها خلال إغلاقين ليليين للقناة.</p>',
                ],
            ],
            [
                'scene' => 'industrial', 'palette' => 'night',
                'sector' => 'industry-and-energy',
                'disciplines' => ['structural-engineering', 'geotechnical-engineering', 'project-and-construction-management'],
                'client_type' => ClientType::Private, 'completion_year' => 2022,
                'title' => ['en' => 'Taweelah Logistics Park', 'ar' => 'مجمع الطويلة اللوجستي'],
                'slug' => ['en' => 'taweelah-logistics-park', 'ar' => 'مجمع-الطويلة-اللوجستي'],
                'location' => ['en' => 'Taweelah, Abu Dhabi', 'ar' => 'الطويلة، أبوظبي'],
                'summary' => [
                    'en' => 'Six steel-framed warehouses totalling 62,000 m² on reclaimed land, with a ground improvement scheme that avoided piling.',
                    'ar' => 'ستة مستودعات بهياكل فولاذية بمساحة إجمالية تبلغ 62,000 متر مربع على أرض مستصلحة، مع حل لتحسين التربة أغنى عن الخوازيق.',
                ],
                'body' => [
                    'en' => '<p>The park was built on recently reclaimed land with loose fill several metres deep. Instead of piling every column, our geotechnical team designed a vibro-compaction scheme verified by cone penetration tests, saving four months on the programme.</p><p>The portal frame warehouses have 12-metre clear heights, dock levellers and roofs designed to carry solar panels in a later phase.</p>',
                    'ar' => '<p>أُنشئ المجمع على أرض مستصلحة حديثًا تعلوها طبقة ردم مفككة بعمق عدة أمتار. وبدلًا من تنفيذ خوازيق أسفل كل عمود، صمّم فريقنا الجيوتقني حلًا لدمك التربة بالاهتزاز جرى التحقق منه باختبارات الاختراق المخروطي، ما وفّر أربعة أشهر من الجدول الزمني.</p><p>وتتميز المستودعات ذات الإطارات البوابية بارتفاع صافٍ يبلغ 12 مترًا، ومنصات تحميل قابلة للتعديل، وأسقف صُمّمت لتحمّل ألواح الطاقة الشمسية في مرحلة لاحقة.</p>',
                ],
            ],
            [
                // Arabic summary and body are still being translated: the
                // Arabic site falls back to English and the CMS flags the gap.
                'scene' => 'solar', 'palette' => 'sand',
                'sector' => 'industry-and-energy',
                'disciplines' => ['sustainability-and-energy', 'structural-engineering', 'mep-engineering'],
                'client_type' => ClientType::SemiGovernment, 'completion_year' => null,
                'title' => ['en' => 'Ruwais Solar Carport Programme', 'ar' => 'برنامج مظلات المواقف الشمسية في الرويس'],
                'slug' => ['en' => 'ruwais-solar-carport-programme', 'ar' => 'مظلات-المواقف-الشمسية-في-الرويس'],
                'location' => ['en' => 'Ruwais, Al Dhafra', 'ar' => 'الرويس، الظفرة'],
                'summary' => [
                    'en' => 'Design and supervision of 4.8 MWp of solar carports across staff housing and office car parks.',
                ],
                'body' => [
                    'en' => '<p>The programme converts open car parks into shaded parking that also generates power for the surrounding buildings. Masar designed a standard family of steel carports that are fabricated off site and adapted to each car park layout.</p><p>The first phase is complete and the remaining car parks are under construction, with each one connected to the grid as soon as it is finished.</p>',
                ],
            ],
            [
                'scene' => 'campus', 'palette' => 'dusk',
                'sector' => 'education-and-healthcare',
                'disciplines' => ['structural-engineering', 'mep-engineering'],
                'client_type' => ClientType::Government, 'completion_year' => 2020,
                'title' => ['en' => 'Al Ain Community Health Centre', 'ar' => 'مركز صحي مجتمعي في العين'],
                'slug' => ['en' => 'al-ain-community-health-centre', 'ar' => 'مركز-صحي-مجتمعي-في-العين'],
                'location' => ['en' => 'Al Ain', 'ar' => 'العين'],
                'summary' => [
                    'en' => 'A primary care centre with clinics, diagnostic imaging and a pharmacy, designed for phased expansion.',
                    'ar' => 'مركز للرعاية الصحية الأولية يضم عيادات وقسمًا للتصوير التشخيصي وصيدلية، مصمّم للتوسع على مراحل.',
                ],
                'body' => [
                    'en' => '<p>The two-storey centre serves a growing residential area on the edge of Al Ain. Medical gas, infection control and imaging shielding requirements were coordinated in the structural and MEP design from the outset.</p><p>The structure and plant rooms were sized for a future third floor so the centre can expand without interrupting patient services.</p>',
                    'ar' => '<p>يخدم المركز المكوّن من طابقين منطقة سكنية متنامية على أطراف مدينة العين. ونُسّقت متطلبات الغازات الطبية ومكافحة العدوى وتدريع غرف التصوير ضمن التصميمين الإنشائي والكهروميكانيكي منذ البداية.</p><p>وصُمّم الهيكل الإنشائي وغرف المعدات لاستيعاب طابق ثالث مستقبلًا، كي يتوسع المركز دون أن تتوقف خدمات المرضى.</p>',
                ],
            ],
            [
                'scene' => 'towers', 'palette' => 'night',
                'sector' => 'buildings-and-real-estate',
                'disciplines' => ['structural-engineering', 'mep-engineering', 'sustainability-and-energy'],
                'client_type' => ClientType::Private, 'completion_year' => 2019,
                'title' => ['en' => 'Creekside Office Retrofit', 'ar' => 'إعادة تأهيل مبنى مكاتب على الخور'],
                'slug' => ['en' => 'creekside-office-retrofit', 'ar' => 'إعادة-تأهيل-مبنى-مكاتب-على-الخور'],
                'location' => ['en' => 'Deira, Dubai', 'ar' => 'ديرة، دبي'],
                'summary' => [
                    'en' => 'Structural strengthening and a full MEP replacement for a 1990s office building, cutting its energy use by 45%.',
                    'ar' => 'تدعيم إنشائي واستبدال كامل للأنظمة الكهروميكانيكية في مبنى مكاتب يعود إلى تسعينيات القرن الماضي، ما خفّض استهلاكه للطاقة بنسبة 45%.',
                ],
                'body' => [
                    'en' => '<p>Rather than replace an ageing office block with a new building, the owner asked Masar to assess whether it could be brought up to current standards. Condition surveys showed that the frame was sound apart from corrosion damage at roof level, which was repaired and strengthened with carbon fibre wrapping.</p><p>All mechanical and electrical systems were replaced while two floors remained occupied, and the building was connected to the district cooling network.</p>',
                    'ar' => '<p>بدلًا من استبدال مبنى المكاتب المتقادم بمبنى جديد، طلب المالك من مسار تقييم إمكانية تحديثه وفق المعايير الحالية. وأظهرت مسوحات الحالة أن الهيكل سليم باستثناء أضرار ناتجة عن التآكل على مستوى السطح، جرى إصلاحها وتدعيمها بلفائف ألياف الكربون.</p><p>واستُبدلت جميع الأنظمة الميكانيكية والكهربائية مع استمرار إشغال طابقين من المبنى، ورُبط المبنى بشبكة تبريد المناطق.</p>',
                ],
            ],
        ];
    }
}
