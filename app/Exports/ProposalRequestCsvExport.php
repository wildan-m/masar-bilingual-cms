<?php

namespace App\Exports;

use App\Enums\ActivityEvent;
use App\Models\ProposalRequest;
use App\Support\Activity\ActivityLogger;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams requests for proposal as CSV.
 *
 * The file starts with a UTF-8 byte order mark so that spreadsheet software
 * shows Arabic text correctly, and cells that a spreadsheet would treat as a
 * formula are neutralised (CSV injection).
 */
final class ProposalRequestCsvExport
{
    private const HEADINGS = [
        'Reference', 'Received', 'Status', 'Language', 'Name', 'Company', 'Email', 'Phone',
        'Sector', 'Project location', 'Assigned to', 'Attachment', 'Message',
    ];

    /**
     * @param  Builder<ProposalRequest>  $query
     */
    public function download(Builder $query): StreamedResponse
    {
        $query = $query->clone()->with(['sector', 'assignee']);

        ActivityLogger::log(ActivityEvent::Exported, ProposalRequest::class, [
            'format' => 'csv',
            'count' => $query->clone()->count(),
        ]);

        return response()->streamDownload(function () use ($query): void {
            $output = fopen('php://output', 'w');

            fwrite($output, "\u{FEFF}");
            fputcsv($output, self::HEADINGS, escape: '');

            $query->lazy(200)->each(function (ProposalRequest $request) use ($output): void {
                fputcsv($output, array_map(self::sanitize(...), $this->row($request)), escape: '');
            });

            fclose($output);
        }, 'proposal-requests-'.now()->format('Y-m-d-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return list<string|null>
     */
    public function row(ProposalRequest $request): array
    {
        return [
            $request->reference,
            $request->created_at?->copy()->setTimezone(config('masar.display_timezone'))->toIso8601String(),
            $request->status->getLabel(),
            $request->locale === 'ar' ? 'Arabic' : 'English',
            $request->name,
            $request->company,
            $request->email,
            $request->phone,
            $request->sector?->translate('name', 'en'),
            $request->project_location,
            $request->assignee?->name,
            $request->attachment_original_name,
            $request->message,
        ];
    }

    /**
     * Prefix values that spreadsheets would evaluate as formulas.
     */
    public static function sanitize(?string $value): string
    {
        $value ??= '';

        return preg_match('/^[=+\-@\t\r]/', $value) === 1 ? "'".$value : $value;
    }
}
