<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuotationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * Based on actual quotations table migration fields.
     */
    public function rules(): array
    {
        return [
            // Client Information (matches quotations table)
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            
            // Project Information (matches quotations table)
            'service_id' => 'nullable|exists:services,id',
            'project_type' => 'nullable|string|max:255',
            'location' => 'nullable|string',
            'requirements' => 'nullable|string',
            'budget_range' => 'nullable|string|max:255',
            'start_date' => 'nullable|date|after:today',
            
            // Additional fields (matches quotations table)
            'source' => 'nullable|string|max:255',
            'additional_info' => 'nullable|string',
            
            // File Attachments (for quotation_attachments table)
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,dwg,zip|max:10240', // 10MB max per file
            
            // Legal and Privacy (not in DB but needed for form)
            'terms_accepted' => 'required|accepted',
            'privacy_accepted' => 'required|accepted',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'full name',
            'email' => 'email address',
            'phone' => 'phone number',
            'company' => 'company name',
            'service_id' => 'service selection',
            'project_type' => 'project type',
            'location' => 'project location',
            'requirements' => 'project requirements',
            'budget_range' => 'budget range',
            'start_date' => 'preferred start date',
            'source' => 'source of information',
            'additional_info' => 'additional information',
            'attachments.*' => 'attachment file',
            'terms_accepted' => 'terms and conditions',
            'privacy_accepted' => 'privacy policy',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please provide your full name.',
            'email.required' => 'Email address is required for communication.',
            'email.email' => 'Please provide a valid email address.',
            'service_id.exists' => 'The selected service is not available.',
            'start_date.after' => 'Start date must be in the future.',
            'attachments.*.file' => 'Each attachment must be a valid file.',
            'attachments.*.mimes' => 'Attachments must be PDF, DOC, DOCX, JPG, JPEG, PNG, DWG, or ZIP files.',
            'attachments.*.max' => 'Each attachment must be smaller than 10MB.',
            'terms_accepted.required' => 'You must accept the terms and conditions to proceed.',
            'terms_accepted.accepted' => 'Please accept the terms and conditions.',
            'privacy_accepted.required' => 'You must accept the privacy policy to proceed.',
            'privacy_accepted.accepted' => 'Please accept the privacy policy.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert checkbox values
        $this->merge([
            'terms_accepted' => $this->boolean('terms_accepted'),
            'privacy_accepted' => $this->boolean('privacy_accepted'),
        ]);

        // Clean and format phone number
        if ($this->filled('phone')) {
            $phone = preg_replace('/[^0-9+]/', '', $this->phone);
            $this->merge(['phone' => $phone]);
        }

        // Set source if not provided
        if (!$this->filled('source')) {
            $this->merge(['source' => 'website']);
        }
    }
}