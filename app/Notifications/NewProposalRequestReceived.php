<?php

namespace App\Notifications;

use App\Models\ProposalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

/**
 * Emails intake staff about a new request for proposal.
 *
 * The email deliberately leaves out the message and the attachment: it links
 * to the record in the admin instead, through a signed, expiring URL that
 * still requires signing in.
 */
class NewProposalRequestReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly ProposalRequest $proposalRequest)
    {
        $this->afterCommit();
    }

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $request = $this->proposalRequest->loadMissing('sector');
        $days = config('masar.rfp.signed_link_ttl_days');

        return (new MailMessage)
            ->subject("New request for proposal {$request->reference}")
            ->greeting('Hello '.self::escape((string) ($notifiable->name ?? 'team')).',')
            ->line('A new request for proposal was submitted on the website.')
            ->line('**Reference:** '.$request->reference)
            ->line('**From:** '.self::escape($request->name).($request->company ? ' ('.self::escape($request->company).')' : ''))
            ->line('**Sector:** '.($request->sector?->translate('name', 'en') ?? 'Not specified'))
            ->line('**Project location:** '.self::escape($request->project_location ?: 'Not specified'))
            ->line('**Language:** '.($request->locale === 'ar' ? 'Arabic' : 'English'))
            ->line($request->hasAttachment() ? 'The client attached a document.' : 'No document was attached.')
            ->action('Open the request', $this->signedUrl())
            ->line("This link expires in {$days} days and requires you to sign in.");
    }

    public function signedUrl(): string
    {
        return URL::temporarySignedRoute(
            'admin.proposal-requests.open',
            now()->addDays(config('masar.rfp.signed_link_ttl_days')),
            ['proposalRequest' => $this->proposalRequest->getKey()],
        );
    }

    /**
     * Visitor input is shown in a Markdown email; escape it so it cannot
     * inject links or formatting.
     */
    private static function escape(string $value): string
    {
        return preg_replace('/([\\\\`*_{}\[\]()#+\-.!|<>~])/', '\\\\$1', $value) ?? '';
    }
}
