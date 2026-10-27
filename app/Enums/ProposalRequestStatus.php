<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

/**
 * Workflow of a request for proposal: new -> in review -> proposal sent -> closed.
 * Closed requests can be reopened, and a sent proposal can go back to review
 * when the client asks for a revision.
 */
enum ProposalRequestStatus: string implements HasColor, HasIcon, HasLabel
{
    case New = 'new';
    case InReview = 'in_review';
    case ProposalSent = 'proposal_sent';
    case Closed = 'closed';

    /**
     * @return list<self>
     */
    public function transitions(): array
    {
        return match ($this) {
            self::New => [self::InReview, self::Closed],
            self::InReview => [self::ProposalSent, self::Closed],
            self::ProposalSent => [self::Closed, self::InReview],
            self::Closed => [self::InReview],
        };
    }

    public function canTransitionTo(self $status): bool
    {
        return in_array($status, $this->transitions(), true);
    }

    public function isOpen(): bool
    {
        return $this !== self::Closed;
    }

    /**
     * Label of the action that moves a request into this status.
     */
    public function actionLabel(self $from): string
    {
        return match (true) {
            $this === self::InReview && $from === self::Closed => 'Reopen',
            $this === self::InReview && $from === self::ProposalSent => 'Back to review',
            $this === self::InReview => 'Start review',
            $this === self::ProposalSent => 'Mark proposal sent',
            $this === self::Closed => 'Close',
            default => $this->getLabel(),
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::New => 'New',
            self::InReview => 'In review',
            self::ProposalSent => 'Proposal sent',
            self::Closed => 'Closed',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New => 'info',
            self::InReview => 'warning',
            self::ProposalSent => 'success',
            self::Closed => 'gray',
        };
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::New => Heroicon::OutlinedInboxArrowDown,
            self::InReview => Heroicon::OutlinedEye,
            self::ProposalSent => Heroicon::OutlinedPaperAirplane,
            self::Closed => Heroicon::OutlinedArchiveBox,
        };
    }
}
