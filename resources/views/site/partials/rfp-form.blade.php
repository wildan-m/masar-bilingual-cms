@php
    $anchor = \App\Http\Requests\StoreProposalRequestRequest::FORM_ANCHOR;
    $honeypot = \App\Http\Middleware\BlockHoneypotSubmissions::FIELD;

    $describedBy = fn (string $field, ?string $help = null): ?string => trim(
        ($help ? $help.' ' : '').($errors->has($field) ? "{$field}-error" : '')
    ) ?: null;
@endphp

<div id="{{ $anchor }}" class="rounded-3xl bg-white p-7 ring-1 ring-slate-200 sm:p-10">
    <h2 class="text-2xl font-semibold tracking-tight text-brand-950">{{ __('site.rfp.title') }}</h2>
    <p class="mt-2 text-slate-600">{{ __('site.rfp.intro') }}</p>

    @if ($errors->any())
        <div role="alert" class="mt-6 rounded-lg bg-red-50 px-4 py-3 text-sm font-medium text-red-800 ring-1 ring-red-200 ring-inset">
            {{ __('site.rfp.errors_title') }}
        </div>
    @endif

    <form method="POST" action="{{ route('site.rfp.store') }}" enctype="multipart/form-data" class="mt-8 grid gap-6 sm:grid-cols-2" novalidate>
        @csrf

        {{-- Honeypot: invisible to people and assistive technology, tempting to bots. --}}
        <div class="absolute -start-[9999px] size-px overflow-hidden" aria-hidden="true">
            <label for="{{ $honeypot }}">{{ __('site.rfp.honeypot') }}</label>
            <input type="text" name="{{ $honeypot }}" id="{{ $honeypot }}" value="" tabindex="-1" autocomplete="off">
        </div>

        <div>
            <label for="name" class="form-label">{{ __('site.rfp.fields.name') }}</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name"
                   class="form-control" @error('name') aria-invalid="true" @enderror aria-describedby="{{ $describedBy('name') }}">
            @error('name') <p id="name-error" class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="company" class="form-label">
                {{ __('site.rfp.fields.company') }}
                <span class="font-normal text-slate-500">({{ __('site.rfp.optional') }})</span>
            </label>
            <input type="text" name="company" id="company" value="{{ old('company') }}" maxlength="160" autocomplete="organization"
                   class="form-control" @error('company') aria-invalid="true" @enderror aria-describedby="{{ $describedBy('company') }}">
            @error('company') <p id="company-error" class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="form-label">{{ __('site.rfp.fields.email') }}</label>
            {{-- Emails and phone numbers read left to right in both languages. --}}
            <input type="email" name="email" id="email" value="{{ old('email') }}" required maxlength="190" autocomplete="email" dir="ltr"
                   class="form-control rtl:text-end" @error('email') aria-invalid="true" @enderror aria-describedby="{{ $describedBy('email') }}">
            @error('email') <p id="email-error" class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="phone" class="form-label">{{ __('site.rfp.fields.phone') }}</label>
            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required maxlength="40" autocomplete="tel" dir="ltr"
                   placeholder="{{ __('site.rfp.placeholders.phone') }}"
                   class="form-control rtl:text-end" @error('phone') aria-invalid="true" @enderror aria-describedby="{{ $describedBy('phone') }}">
            @error('phone') <p id="phone-error" class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="sector_id" class="form-label">
                {{ __('site.rfp.fields.sector') }}
                <span class="font-normal text-slate-500">({{ __('site.rfp.optional') }})</span>
            </label>
            <select name="sector_id" id="sector_id" class="form-control" @error('sector_id') aria-invalid="true" @enderror aria-describedby="{{ $describedBy('sector_id') }}">
                <option value="">{{ __('site.rfp.select_sector') }}</option>
                @foreach ($sectors as $sector)
                    <option value="{{ $sector->getKey() }}" @selected((string) old('sector_id') === (string) $sector->getKey())>{{ $sector->translate('name') }}</option>
                @endforeach
            </select>
            @error('sector_id') <p id="sector_id-error" class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="project_location" class="form-label">
                {{ __('site.rfp.fields.project_location') }}
                <span class="font-normal text-slate-500">({{ __('site.rfp.optional') }})</span>
            </label>
            <input type="text" name="project_location" id="project_location" value="{{ old('project_location') }}" maxlength="160"
                   placeholder="{{ __('site.rfp.placeholders.project_location') }}"
                   class="form-control" @error('project_location') aria-invalid="true" @enderror aria-describedby="{{ $describedBy('project_location') }}">
            @error('project_location') <p id="project_location-error" class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label for="message" class="form-label">{{ __('site.rfp.fields.message') }}</label>
            <textarea name="message" id="message" rows="6" required minlength="20" maxlength="5000"
                      placeholder="{{ __('site.rfp.placeholders.message') }}"
                      class="form-control" @error('message') aria-invalid="true" @enderror aria-describedby="{{ $describedBy('message') }}">{{ old('message') }}</textarea>
            @error('message') <p id="message-error" class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <span class="form-label" id="attachment-label">
                {{ __('site.rfp.fields.attachment') }}
                <span class="font-normal text-slate-500">({{ __('site.rfp.optional') }})</span>
            </span>
            <label for="attachment" class="flex cursor-pointer flex-wrap items-center gap-3 rounded-lg border border-dashed border-slate-300 px-4 py-4 hover:border-brand-400 hover:bg-brand-50/40">
                <svg class="size-5 text-brand-700" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M15.621 4.379a3 3 0 0 0-4.242 0l-7 7a3 3 0 0 0 4.241 4.243h.001l.497-.5a.75.75 0 0 1 1.064 1.057l-.498.501-.002.002a4.5 4.5 0 0 1-6.364-6.364l7-7a4.5 4.5 0 0 1 6.368 6.36l-3.455 3.553A2.625 2.625 0 1 1 9.52 9.52l3.45-3.451a.75.75 0 1 1 1.061 1.06l-3.45 3.451a1.125 1.125 0 0 0 1.587 1.595l3.454-3.553a3 3 0 0 0 0-4.243Z" clip-rule="evenodd" /></svg>
                <span id="attachment-name" class="text-sm text-slate-600" data-empty="{{ __('site.rfp.attachment_help') }}">{{ __('site.rfp.attachment_help') }}</span>
            </label>
            <input type="file" name="attachment" id="attachment" class="sr-only" accept=".pdf,.docx,.xlsx"
                   data-file-label="attachment-name" aria-labelledby="attachment-label"
                   @error('attachment') aria-invalid="true" @enderror aria-describedby="{{ $describedBy('attachment') }}">
            @error('attachment') <p id="attachment-error" class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2 flex flex-col gap-5 border-t border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-between">
            <p class="max-w-md text-xs leading-relaxed text-slate-500">{{ __('site.rfp.privacy') }}</p>
            <button type="submit" class="btn btn-primary shrink-0 px-7 py-3">
                {{ __('site.rfp.submit') }}
                <x-site.arrow />
            </button>
        </div>
    </form>
</div>
