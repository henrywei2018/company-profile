<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuotationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Anyone can submit a quotation request
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'service_id' => 'nullable|exists:services,id',
            'project_type' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'requirements' => 'required|string|min:10',
            'budget_range' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'file' => 'nullable|file|max:10240', // Max 10MB
            'g-recaptcha-response' => config('app.env') !== 'testing' ? 'required|recaptcha' : '',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Please enter your name',
            'email.required' => 'Please enter your email address',
            'email.email' => 'Please enter a valid email address',
            'project_type.required' => 'Please specify the project type',
            'requirements.required' => 'Please describe your requirements',
            'requirements.min' => 'Your requirements must be at least 10 characters',
            'file.max' => 'The file may not be greater than 10MB',
            'g-recaptcha-response.required' => 'Please verify that you are not a robot',
            'g-recaptcha-response.recaptcha' => 'Captcha verification failed. Please try again.',
        ];
    }

    /**
     * Get validated data with additional fields.
     *
     * @return array
     */
    public function validated()
    {
        $validated = parent::validated();
        
        // Set status to pending by default
        $validated['status'] = 'pending';
        
        // Link to authenticated user if available
        if (auth()->check()) {
            $validated['client_id'] = auth()->id();
        }
        
        return $validated;
    }
}