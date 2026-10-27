<?php

namespace Database\Seeders;

use App\Enums\PageTemplate;
use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        Page::query()->create([
            'key' => Page::ABOUT,
            'template' => PageTemplate::Standard,
            'is_published' => true,
            'title' => ['en' => 'About us', 'ar' => 'من نحن'],
            'slug' => ['en' => 'about', 'ar' => 'من-نحن'],
            'meta_description' => [
                'en' => 'Founded in Abu Dhabi in 2006, Masar is a multidisciplinary engineering consultancy with one accountable team from feasibility to handover.',
                'ar' => 'تأسست مسار في أبوظبي عام 2006، وهي شركة استشارات هندسية متعددة التخصصات تقدّم فريقًا واحدًا مسؤولًا من دراسة الجدوى حتى التسليم.',
            ],
            'body' => [
                'en' => <<<'HTML'
                    <p>Masar Engineering Consultants was founded in Abu Dhabi in 2006 by a group of structural and infrastructure engineers who wanted to give clients one accountable design team from the first sketch to the final handover. Today our engineers, architects and site supervisors work on buildings, transport links, utilities and industrial facilities across the United Arab Emirates.</p>
                    <h2>How we work</h2>
                    <p>Every project is led by a project director who stays with it from concept to completion. Our design disciplines share one studio, so structural, MEP and civil decisions are coordinated before they reach the drawings rather than on site.</p>
                    <ul>
                    <li>One accountable team from feasibility to handover</li>
                    <li>Design reviews at every stage by senior engineers from outside the project team</li>
                    <li>Building information modelling (BIM) on every design project</li>
                    <li>Site supervision led by the engineers who produced the design</li>
                    </ul>
                    <h2>Quality, health and safety</h2>
                    <p>Our integrated management system is certified to ISO 9001, ISO 14001 and ISO 45001, and the firm is registered as an engineering consultancy with the relevant authorities in Abu Dhabi and Dubai.</p>
                    HTML,
                'ar' => <<<'HTML'
                    <p>تأسست مسار للاستشارات الهندسية في أبوظبي عام 2006 على يد مجموعة من المهندسين الإنشائيين ومهندسي البنية التحتية الذين أرادوا أن يقدّموا لعملائهم فريق تصميم واحدًا يتحمّل المسؤولية كاملة من الرسم الأول حتى التسليم النهائي. ويعمل مهندسونا ومعماريونا ومشرفو المواقع لدينا اليوم على مشاريع المباني وشبكات النقل والمرافق والمنشآت الصناعية في مختلف أنحاء دولة الإمارات العربية المتحدة.</p>
                    <h2>منهجية عملنا</h2>
                    <p>يقود كل مشروع مدير مشروع يرافقه من مرحلة الفكرة حتى الإنجاز. وتعمل تخصصاتنا التصميمية في استوديو واحد، فتُنسَّق القرارات الإنشائية والكهروميكانيكية والمدنية قبل أن تصل إلى المخططات، لا في الموقع.</p>
                    <ul>
                    <li>فريق واحد مسؤول من دراسة الجدوى حتى التسليم</li>
                    <li>مراجعات للتصميم في كل مرحلة يجريها مهندسون أوائل من خارج فريق المشروع</li>
                    <li>نمذجة معلومات البناء (BIM) في جميع مشاريع التصميم</li>
                    <li>إشراف على التنفيذ يقوده المهندسون الذين أعدّوا التصميم</li>
                    </ul>
                    <h2>الجودة والصحة والسلامة</h2>
                    <p>نظام الإدارة المتكامل لدينا حاصل على شهادات الأيزو 9001 و14001 و45001، والشركة مسجّلة بصفتها مكتبًا استشاريًا هندسيًا لدى الجهات المختصة في أبوظبي ودبي.</p>
                    HTML,
            ],
        ]);

        Page::query()->create([
            'key' => Page::CONTACT,
            'template' => PageTemplate::Contact,
            'is_published' => true,
            'title' => ['en' => 'Contact us', 'ar' => 'تواصل معنا'],
            'slug' => ['en' => 'contact', 'ar' => 'تواصل-معنا'],
            'meta_description' => [
                'en' => 'Contact Masar Engineering Consultants in Abu Dhabi or send a request for proposal for your project.',
                'ar' => 'تواصل مع مسار للاستشارات الهندسية في أبوظبي أو أرسل طلب عرض فني ومالي لمشروعك.',
            ],
            'body' => [
                'en' => '<p>Whether your project is at feasibility stage or ready to tender, our proposals team can help you define the scope and the right mix of disciplines. Send us a request for proposal using the form on this page, or call our Abu Dhabi office during working hours.</p>',
                'ar' => '<p>سواء كان مشروعك في مرحلة دراسة الجدوى أو جاهزًا للطرح، يستطيع فريق العروض لدينا مساعدتك في تحديد نطاق العمل والتخصصات المناسبة. أرسل إلينا طلب عرض عبر النموذج في هذه الصفحة، أو اتصل بمكتبنا في أبوظبي خلال ساعات العمل.</p>',
            ],
        ]);

        Page::query()->create([
            'key' => Page::PRIVACY,
            'template' => PageTemplate::Standard,
            'is_published' => true,
            'title' => ['en' => 'Privacy notice', 'ar' => 'إشعار الخصوصية'],
            'slug' => ['en' => 'privacy', 'ar' => 'الخصوصية'],
            'body' => [
                'en' => <<<'HTML'
                    <p>This notice explains how Masar Engineering Consultants handles the personal data you share with us through this website.</p>
                    <h2>Requests for proposal</h2>
                    <p>When you submit a request for proposal we collect your name, company, email address, phone number, project details and any document you attach. We use this information only to assess your request and to prepare and follow up on a proposal.</p>
                    <p>Attachments are stored in a private storage area that cannot be reached from the internet. Only our proposals team and system administrators can open them, and every download is recorded.</p>
                    <h2>Retention</h2>
                    <p>Requests that do not lead to an appointment are deleted within 24 months. You may ask us at any time to access, correct or delete the information you sent us by writing to info@masar.test.</p>
                    <h2>Cookies</h2>
                    <p>The website uses a session cookie that keeps forms secure and a cookie that remembers your preferred language. We do not use advertising or tracking cookies.</p>
                    HTML,
                'ar' => <<<'HTML'
                    <p>يوضّح هذا الإشعار كيفية تعامل مسار للاستشارات الهندسية مع البيانات الشخصية التي تشاركها معنا عبر هذا الموقع.</p>
                    <h2>طلبات العروض</h2>
                    <p>عند إرسال طلب عرض، نجمع اسمك واسم شركتك وبريدك الإلكتروني ورقم هاتفك وتفاصيل المشروع وأي مستند ترفقه. ونستخدم هذه المعلومات فقط لدراسة طلبك وإعداد العرض ومتابعته.</p>
                    <p>تُحفظ المرفقات في مساحة تخزين خاصة لا يمكن الوصول إليها من الإنترنت، ولا يطّلع عليها سوى فريق العروض ومديري النظام، ويُسجَّل كل تنزيل لها.</p>
                    <h2>مدة الاحتفاظ بالبيانات</h2>
                    <p>تُحذف الطلبات التي لا تنتهي بتكليف خلال 24 شهرًا. ويمكنك في أي وقت أن تطلب الاطلاع على المعلومات التي أرسلتها إلينا أو تصحيحها أو حذفها عبر مراسلتنا على info@masar.test.</p>
                    <h2>ملفات تعريف الارتباط</h2>
                    <p>يستخدم الموقع ملف تعريف ارتباط للجلسة يحافظ على أمان النماذج، وملفًا آخر يتذكّر لغتك المفضلة. ولا نستخدم ملفات تعريف ارتباط إعلانية أو للتتبع.</p>
                    HTML,
            ],
        ]);
    }
}
