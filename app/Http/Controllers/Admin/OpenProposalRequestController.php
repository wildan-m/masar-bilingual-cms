<?php

namespace App\Http\Controllers\Admin;

use App\Filament\Resources\ProposalRequests\ProposalRequestResource;
use App\Models\ProposalRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

/**
 * Target of the signed link in the "new RFP" email. The signature proves the
 * link was issued by the application and has not expired; the visitor must
 * still be signed in and allowed to view the request.
 */
final class OpenProposalRequestController
{
    public function __invoke(ProposalRequest $proposalRequest): RedirectResponse
    {
        Gate::authorize('view', $proposalRequest);

        return redirect()->to(ProposalRequestResource::getUrl('view', ['record' => $proposalRequest]));
    }
}
