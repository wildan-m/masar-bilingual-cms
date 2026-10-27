<?php

namespace App\Http\Controllers\Site;

use App\Actions\SubmitProposalRequest;
use App\Http\Requests\StoreProposalRequestRequest;
use App\Models\Page;
use App\Support\Localization\Locale;
use App\Support\Seo\SeoData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ProposalRequestController
{
    /**
     * The form lives on the contact page; direct visits are sent there.
     */
    public function create(): RedirectResponse
    {
        $contactUrl = Page::findByKey(Page::CONTACT)?->url() ?? route('site.home');

        return redirect()->to($contactUrl.'#'.StoreProposalRequestRequest::FORM_ANCHOR);
    }

    public function store(StoreProposalRequestRequest $request, SubmitProposalRequest $submit): RedirectResponse
    {
        $proposalRequest = $submit->handle(
            $request->safe()->except('attachment'),
            $request->file('attachment'),
            Locale::current(),
        );

        return redirect()
            ->route('site.rfp.thanks')
            ->with('rfp_reference', $proposalRequest->reference);
    }

    public function thanks(Request $request): View
    {
        return view('site.rfp.thanks', [
            'reference' => $request->session()->get('rfp_reference'),
            'seo' => SeoData::make(title: __('site.rfp.thanks.title'), indexable: false),
        ]);
    }
}
