<?php

namespace Database\Seeders;

use App\Enums\ProposalRequestStatus;
use App\Enums\Role;
use App\Models\ProposalRequest;
use App\Models\Sector;
use App\Models\User;
use Database\Seeders\Support\BriefPdf;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Requests for proposal at different stages of the workflow. Status changes
 * are replayed as the intake user so the activity log shows a realistic
 * history.
 */
class ProposalRequestSeeder extends Seeder
{
    public function run(): void
    {
        $intake = User::query()->where('role', Role::Intake)->firstOrFail();
        $sectors = Sector::all()->keyBy(fn (Sector $sector): string => $sector->translate('slug', 'en'));
        $disk = config('masar.rfp.attachment_disk');

        try {
            foreach ($this->requests() as $data) {
                Carbon::setTestNow(Carbon::parse($data['received'], config('masar.display_timezone')));
                Auth::forgetUser();

                $attachment = [];
                if (isset($data['attachment'])) {
                    $path = config('masar.rfp.attachment_directory').'/'.now()->format('Y/m').'/'.Str::random(40).'.pdf';
                    $contents = BriefPdf::make($data['attachment']['title'], $data['attachment']['lines']);
                    Storage::disk($disk)->put($path, $contents);

                    $attachment = [
                        'attachment_disk' => $disk,
                        'attachment_path' => $path,
                        'attachment_original_name' => $data['attachment']['name'],
                        'attachment_mime' => 'application/pdf',
                        'attachment_size' => strlen($contents),
                    ];
                }

                $proposalRequest = ProposalRequest::query()->create([
                    ...collect($data)->only(['name', 'company', 'email', 'phone', 'project_location', 'message', 'locale'])->all(),
                    ...$attachment,
                    'sector_id' => $sectors[$data['sector']]->getKey(),
                ]);

                Auth::setUser($intake);

                foreach ($data['history'] as [$at, $status]) {
                    Carbon::setTestNow(Carbon::parse($at, config('masar.display_timezone')));

                    if ($proposalRequest->assigned_to === null) {
                        $proposalRequest->update(['assigned_to' => $intake->getKey()]);
                    }

                    $proposalRequest->transitionTo($status);
                }

                if (isset($data['notes'])) {
                    $proposalRequest->update(['internal_notes' => $data['notes']]);
                }
            }
        } finally {
            Carbon::setTestNow();
            Auth::forgetUser();
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function requests(): array
    {
        return [
            [
                'received' => '2026-10-26 10:42', 'locale' => 'ar', 'sector' => 'buildings-and-real-estate',
                'name' => 'عائشة الكعبي', 'company' => 'واحة السدر للتطوير العقاري',
                'email' => 'aisha.alkaabi@clients.masar.test', 'phone' => '+971 50 000 2231',
                'project_location' => 'الفوعة، العين',
                'message' => 'نخطط لتطوير مجمع سكني من 120 فيلا في منطقة الفوعة بالعين، ونرغب في الحصول على عرض فني ومالي للتصميم الإنشائي والكهروميكانيكي والإشراف على التنفيذ. التصميم المعماري المبدئي جاهز، ونستهدف طرح المناقصة في الربع الأول من العام المقبل.',
                'attachment' => [
                    'name' => 'مخطط الموقع العام.pdf',
                    'title' => 'Al Foah villa community - site layout',
                    'lines' => ['120 villas, plot area 38 hectares', 'Concept architecture complete', 'Tender planned for Q1'],
                ],
                'history' => [],
            ],
            [
                'received' => '2026-10-24 15:08', 'locale' => 'en', 'sector' => 'industry-and-energy',
                'name' => 'James Holloway', 'company' => 'Harbourline Marine Services',
                'email' => 'james.holloway@clients.masar.test', 'phone' => '+971 2 000 7710',
                'project_location' => 'Mina Zayed, Abu Dhabi',
                'message' => 'We are expanding our boatyard and need a structural assessment of an existing slipway, plus the design of a new pier for a 40-tonne travel lift. Please let us know your availability for a site visit in the coming weeks.',
                'history' => [],
            ],
            [
                'received' => '2026-10-19 09:15', 'locale' => 'en', 'sector' => 'education-and-healthcare',
                'name' => 'Priya Raman', 'company' => 'Greenfield International School',
                'email' => 'priya.raman@clients.masar.test', 'phone' => '+971 55 000 4182',
                'project_location' => 'Mohammed Bin Zayed City, Abu Dhabi',
                'message' => 'We would like a proposal for the design of a two-storey extension with eight classrooms and a multipurpose hall. The school stays open during construction, so phasing and site safety are key concerns for us.',
                'attachment' => [
                    'name' => 'Extension brief v2.pdf',
                    'title' => 'Greenfield International School - extension brief',
                    'lines' => ['Eight classrooms and a multipurpose hall', 'Two storeys, approx. 2,400 m2', 'Works phased around the school calendar'],
                ],
                'history' => [['2026-10-20 11:30', ProposalRequestStatus::InReview]],
                'notes' => "Called the client on 22 Oct. The board has approved the budget and expects proposals by mid-November.\nSite visit booked for 29 Oct with the structural and MEP leads.",
            ],
            [
                'received' => '2026-10-14 13:50', 'locale' => 'ar', 'sector' => 'utilities-and-water',
                'name' => 'خالد المنصوري', 'company' => 'مجموعة سنام للمقاولات',
                'email' => 'k.almansoori@clients.masar.test', 'phone' => '+971 50 000 6634',
                'project_location' => 'مدينة زايد، الظفرة',
                'message' => 'نحتاج إلى استشاري لتصميم شبكة تصريف مياه الأمطار ومحطة ضخ لمشروع سكني جديد، مع إعداد وثائق المناقصة والإشراف على التنفيذ. نرجو تزويدنا بعرضكم وبخبراتكم في مشاريع مماثلة.',
                'history' => [['2026-10-15 10:05', ProposalRequestStatus::InReview]],
                'notes' => 'Client asked for drainage references. Sent the Mussafah stormwater case study on 16 Oct.',
            ],
            [
                'received' => '2026-10-02 08:40', 'locale' => 'en', 'sector' => 'buildings-and-real-estate',
                'name' => 'Michael Grant', 'company' => 'Sunline Hospitality',
                'email' => 'michael.grant@clients.masar.test', 'phone' => '+971 4 000 3390',
                'project_location' => 'Saadiyat Island, Abu Dhabi',
                'message' => 'Refurbishment of a 180-key hotel, including full replacement of the MEP systems while the hotel remains partly operational. We need an energy audit as well as design and site supervision.',
                'history' => [
                    ['2026-10-03 09:20', ProposalRequestStatus::InReview],
                    ['2026-10-20 16:45', ProposalRequestStatus::ProposalSent],
                ],
                'notes' => "Proposal P-2026-118 sent on 20 Oct.\nFollow up with the client on 3 Nov.",
            ],
            [
                'received' => '2026-09-21 12:10', 'locale' => 'ar', 'sector' => 'industry-and-energy',
                'name' => 'مريم الشامسي', 'company' => 'دروب الخليج للخدمات اللوجستية',
                'email' => 'maryam.alshamsi@clients.masar.test', 'phone' => '+971 56 000 9021',
                'project_location' => 'مصفح، أبوظبي',
                'message' => 'نرغب في الحصول على دراسة لتحسين التربة وتصميم الأساسات لموقع مستودع جديد بمساحة 15 ألف متر مربع.',
                'history' => [
                    ['2026-09-22 10:00', ProposalRequestStatus::InReview],
                    ['2026-10-05 14:30', ProposalRequestStatus::Closed],
                ],
                'notes' => 'The client decided to proceed with the contractor\'s in-house design team. Closed on 5 Oct.',
            ],
        ];
    }
}
