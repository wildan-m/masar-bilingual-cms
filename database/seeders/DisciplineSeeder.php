<?php

namespace Database\Seeders;

use App\Models\Discipline;
use Illuminate\Database\Seeder;

class DisciplineSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->disciplines() as $order => $discipline) {
            Discipline::query()->create([...$discipline, 'sort_order' => $order + 1, 'is_published' => true]);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function disciplines(): array
    {
        return [
            [
                'icon' => 'building-office-2',
                'name' => ['en' => 'Structural engineering', 'ar' => 'الهندسة الإنشائية'],
                'slug' => ['en' => 'structural-engineering', 'ar' => 'الهندسة-الإنشائية'],
                'summary' => [
                    'en' => 'Analysis and design of buildings, bridges and special structures in concrete, steel and composite construction.',
                    'ar' => 'تحليل وتصميم المباني والجسور والمنشآت الخاصة باستخدام الخرسانة والحديد والإنشاءات المركّبة.',
                ],
                'body' => [
                    'en' => '<p>Our structural team designs high-rise towers, long-span roofs, bridges and industrial structures, and assesses existing buildings for refurbishment and change of use.</p><h2>Services</h2><ul><li>Concept and detailed structural design</li><li>Post-tensioned slabs and precast concrete systems</li><li>Wind, seismic and dynamic analysis</li><li>Condition surveys and strengthening of existing structures</li><li>Independent design checks for authorities and developers</li></ul><p>Every design is developed in a shared BIM model with the architectural and MEP teams, which keeps openings, levels and loads coordinated through each issue of drawings.</p>',
                    'ar' => '<p>يصمّم فريقنا الإنشائي الأبراج الشاهقة والأسقف ذات البحور الطويلة والجسور والمنشآت الصناعية، ويقيّم المباني القائمة لأغراض التجديد وتغيير الاستخدام.</p><h2>خدماتنا</h2><ul><li>التصميم الإنشائي المبدئي والتفصيلي</li><li>البلاطات لاحقة الشد وأنظمة الخرسانة سابقة الصب</li><li>تحليل أحمال الرياح والزلازل والتحليل الديناميكي</li><li>مسوحات حالة المنشآت القائمة وتدعيمها</li><li>التدقيق المستقل على التصاميم لصالح الجهات الحكومية والمطورين</li></ul><p>نطوّر كل تصميم ضمن نموذج معلومات بناء مشترك مع الفريقين المعماري والكهروميكانيكي، ما يضمن تنسيق الفتحات والمناسيب والأحمال في كل إصدار من المخططات.</p>',
                ],
            ],
            [
                'icon' => 'bolt',
                'name' => ['en' => 'MEP engineering', 'ar' => 'الهندسة الكهروميكانيكية'],
                'slug' => ['en' => 'mep-engineering', 'ar' => 'الهندسة-الكهروميكانيكية'],
                'summary' => [
                    'en' => 'Mechanical, electrical, plumbing and fire protection systems designed for comfort, safety and low running costs.',
                    'ar' => 'أنظمة ميكانيكية وكهربائية وصحية وأنظمة حماية من الحريق مصمّمة لتحقيق الراحة والسلامة وخفض تكاليف التشغيل.',
                ],
                'body' => [
                    'en' => '<p>In a climate where cooling can account for most of a building\'s energy use, MEP design has a direct effect on operating costs. Our engineers size systems with dynamic energy models rather than rules of thumb.</p><h2>Services</h2><ul><li>HVAC design and district cooling connections</li><li>Power distribution, lighting and low-current systems</li><li>Water supply, drainage and irrigation</li><li>Fire detection, alarm and suppression design</li><li>Testing and commissioning management</li></ul>',
                    'ar' => '<p>في مناخ قد يستهلك فيه التبريد معظم طاقة المبنى، يؤثر التصميم الكهروميكانيكي مباشرة في تكاليف التشغيل. لذلك يحدّد مهندسونا أحجام الأنظمة اعتمادًا على نماذج طاقة ديناميكية لا على التقديرات التقريبية.</p><h2>خدماتنا</h2><ul><li>تصميم أنظمة التكييف والتهوية والربط بشبكات تبريد المناطق</li><li>توزيع الطاقة والإنارة وأنظمة التيار المنخفض</li><li>التغذية بالمياه والصرف والري</li><li>تصميم أنظمة كشف الحريق والإنذار والإطفاء</li><li>إدارة الاختبار والتشغيل التجريبي</li></ul>',
                ],
            ],
            [
                'icon' => 'map',
                'name' => ['en' => 'Infrastructure and roads', 'ar' => 'البنية التحتية والطرق'],
                'slug' => ['en' => 'infrastructure-and-roads', 'ar' => 'البنية-التحتية-والطرق'],
                'summary' => [
                    'en' => 'Roads, interchanges, utility corridors and stormwater networks, from master planning to construction supervision.',
                    'ar' => 'الطرق والتقاطعات وممرات المرافق وشبكات تصريف مياه الأمطار، من التخطيط العام حتى الإشراف على التنفيذ.',
                ],
                'body' => [
                    'en' => '<p>We plan and design the networks that connect communities: urban streets, highway interchanges, utility corridors and drainage systems. Traffic, utilities and construction phasing are considered together so that roads can be built while the city keeps moving.</p><h2>Services</h2><ul><li>Traffic impact studies and junction design</li><li>Highway and urban street geometric design</li><li>Utility corridor planning and diversions</li><li>Stormwater, sewerage and treated effluent networks</li><li>Traffic management during construction</li></ul>',
                    'ar' => '<p>نخطّط ونصمّم الشبكات التي تربط المجتمعات: الشوارع الحضرية وتقاطعات الطرق السريعة وممرات المرافق وأنظمة التصريف. ونأخذ الحركة المرورية والمرافق ومراحل التنفيذ في الحسبان معًا، كي تُبنى الطرق دون أن تتوقف حركة المدينة.</p><h2>خدماتنا</h2><ul><li>دراسات الأثر المروري وتصميم التقاطعات</li><li>التصميم الهندسي للطرق السريعة والشوارع الحضرية</li><li>تخطيط ممرات المرافق وتحويلها</li><li>شبكات تصريف مياه الأمطار والصرف الصحي والمياه المعالجة</li><li>إدارة الحركة المرورية أثناء التنفيذ</li></ul>',
                ],
            ],
            [
                'icon' => 'globe-alt',
                'name' => ['en' => 'Geotechnical engineering', 'ar' => 'الهندسة الجيوتقنية'],
                'slug' => ['en' => 'geotechnical-engineering', 'ar' => 'الهندسة-الجيوتقنية'],
                'summary' => [
                    'en' => 'Site investigations, foundation design and ground improvement for the coastal soils of the Gulf.',
                    'ar' => 'فحص التربة وتصميم الأساسات وتحسين التربة في البيئات الساحلية لمنطقة الخليج.',
                ],
                'body' => [
                    'en' => '<p>Coastal sabkha, loose sands and high groundwater make foundations one of the main cost and risk items on Gulf projects. Our geotechnical engineers plan the site investigation, interpret the results and design foundations that suit both the ground and the construction programme.</p><h2>Services</h2><ul><li>Planning and supervision of site investigations</li><li>Shallow, raft and piled foundation design</li><li>Dewatering and excavation support systems</li><li>Ground improvement design and verification</li><li>Pile load test specification and interpretation</li></ul>',
                    'ar' => '<p>تجعل السبخات الساحلية والرمال المفككة وارتفاع منسوب المياه الجوفية من الأساسات أحد أهم بنود التكلفة والمخاطر في مشاريع الخليج. ويتولى مهندسونا الجيوتقنيون تخطيط أعمال فحص التربة وتفسير نتائجها وتصميم أساسات تلائم طبيعة الأرض والجدول الزمني للتنفيذ معًا.</p><h2>خدماتنا</h2><ul><li>تخطيط أعمال فحص التربة والإشراف عليها</li><li>تصميم الأساسات السطحية واللبشات والخوازيق</li><li>أنظمة نزح المياه وسند جوانب الحفر</li><li>تصميم أعمال تحسين التربة والتحقق منها</li><li>إعداد مواصفات اختبارات تحميل الخوازيق وتفسير نتائجها</li></ul>',
                ],
            ],
            [
                'icon' => 'sun',
                'name' => ['en' => 'Sustainability and energy', 'ar' => 'الاستدامة والطاقة'],
                'slug' => ['en' => 'sustainability-and-energy', 'ar' => 'الاستدامة-والطاقة'],
                'summary' => [
                    'en' => 'Green building certification, energy modelling and renewable energy studies that cut operating costs.',
                    'ar' => 'شهادات المباني الخضراء ونمذجة الطاقة ودراسات الطاقة المتجددة التي تخفّض تكاليف التشغيل.',
                ],
                'body' => [
                    'en' => '<p>We help clients meet Estidama Pearl Rating and LEED requirements without adding cost late in the design. Our sustainability engineers join the project at concept stage, when orientation, façade and system choices still have the largest effect.</p><h2>Services</h2><ul><li>Estidama Pearl Rating and LEED certification management</li><li>Building energy and daylight modelling</li><li>Solar photovoltaic feasibility and design</li><li>Energy audits and retrofit studies for existing buildings</li><li>Water efficiency and reuse strategies</li></ul>',
                    'ar' => '<p>نساعد عملاءنا على استيفاء متطلبات نظام التقييم بدرجات اللؤلؤ «استدامة» وشهادة LEED دون تكاليف إضافية في المراحل المتأخرة من التصميم. وينضم مهندسو الاستدامة لدينا إلى المشروع منذ مرحلة الفكرة، حين يكون لاختيارات التوجيه والواجهات والأنظمة أكبر الأثر.</p><h2>خدماتنا</h2><ul><li>إدارة الحصول على تقييم «استدامة» بدرجات اللؤلؤ وشهادة LEED</li><li>نمذجة الطاقة والإضاءة الطبيعية للمباني</li><li>دراسات جدوى أنظمة الطاقة الشمسية الكهروضوئية وتصميمها</li><li>تدقيق استهلاك الطاقة ودراسات تحديث المباني القائمة</li><li>استراتيجيات ترشيد المياه وإعادة استخدامها</li></ul>',
                ],
            ],
            [
                'icon' => 'clipboard-document-check',
                'name' => ['en' => 'Project and construction management', 'ar' => 'إدارة المشاريع والإشراف على التنفيذ'],
                'slug' => ['en' => 'project-and-construction-management', 'ar' => 'إدارة-المشاريع-والإشراف'],
                'summary' => [
                    'en' => 'Project management, cost control and site supervision that keep projects on programme and on budget.',
                    'ar' => 'إدارة المشاريع وضبط التكاليف والإشراف على المواقع للحفاظ على الجدول الزمني والميزانية.',
                ],
                'body' => [
                    'en' => '<p>Our project managers and resident engineers represent the client on site. They check that work is built as designed, keep the programme and budget under control and give owners clear, regular reporting.</p><h2>Services</h2><ul><li>Project management consultancy (PMC)</li><li>Tender documents, bid evaluation and contract award</li><li>Resident engineering and site supervision</li><li>Cost control, variations and claims assessment</li><li>Handover, snagging and defects liability management</li></ul>',
                    'ar' => '<p>يمثّل مديرو المشاريع والمهندسون المقيمون لدينا العميلَ في الموقع، فيتحققون من تنفيذ الأعمال وفق التصميم، ويضبطون الجدول الزمني والميزانية، ويزوّدون الملّاك بتقارير واضحة ومنتظمة.</p><h2>خدماتنا</h2><ul><li>الاستشارات في إدارة المشاريع</li><li>إعداد وثائق المناقصات وتقييم العطاءات وترسية العقود</li><li>الهندسة المقيمة والإشراف على التنفيذ</li><li>ضبط التكاليف وتقييم الأوامر التغييرية والمطالبات</li><li>إدارة التسليم وقوائم الملاحظات وفترة ضمان العيوب</li></ul>',
                ],
            ],
        ];
    }
}
