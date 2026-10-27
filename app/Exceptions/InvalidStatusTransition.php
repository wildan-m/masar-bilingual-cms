<?php

namespace App\Exceptions;

use App\Enums\ProposalRequestStatus;
use DomainException;

final class InvalidStatusTransition extends DomainException
{
    public static function between(ProposalRequestStatus $from, ProposalRequestStatus $to): self
    {
        return new self(sprintf(
            'A request for proposal cannot move from "%s" to "%s".',
            $from->getLabel(),
            $to->getLabel(),
        ));
    }
}
