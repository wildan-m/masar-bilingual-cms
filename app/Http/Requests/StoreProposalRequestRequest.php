<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreProposalRequestRequest extends FormRequest
{
    /**
     * Anchor of the form on the contact page, so validation errors scroll
     * the visitor back to it.
     */
    public const FORM_ANCHOR = 'request-for-proposal';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Visitors writing in Arabic often type Arabic-Indic digits in phone
     * numbers; normalise them to ASCII digits before validating.
     */
    protected function prepareForValidation(): void
    {
        if (is_string($phone = $this->input('phone'))) {
            $this->merge([
                'phone' => strtr($phone, [
                    '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
                    '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
                    '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
                    '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
                ]),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $extensions = config('masar.rfp.attachment_extensions');

        return [
            'name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'string', 'email', 'max:190'],
            'phone' => ['required', 'string', 'max:40', 'regex:/^\+?[0-9\s\-().]{7,40}$/'],
            'sector_id' => ['nullable', 'integer', Rule::exists('sectors', 'id')],
            'project_location' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
            'attachment' => [
                'nullable',
                File::types($extensions)
                    ->extensions($extensions)
                    ->max(config('masar.rfp.attachment_max_kilobytes')),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('site.rfp.attributes.name'),
            'company' => __('site.rfp.attributes.company'),
            'email' => __('site.rfp.attributes.email'),
            'phone' => __('site.rfp.attributes.phone'),
            'sector_id' => __('site.rfp.attributes.sector'),
            'project_location' => __('site.rfp.attributes.project_location'),
            'message' => __('site.rfp.attributes.message'),
            'attachment' => __('site.rfp.attributes.attachment'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.regex' => __('site.rfp.validation.phone'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return parent::getRedirectUrl().'#'.self::FORM_ANCHOR;
    }
}
