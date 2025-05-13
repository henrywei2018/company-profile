<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Anyone can submit a contact form
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
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|min:10',
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
            'message.required' => 'Please enter a message',
            'message.min' => 'Your message must be at least 10 characters',
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
        
        // Add type
        $validated['type'] = 'contact_form';
        
        // Add IP address for security and tracking
        $validated['ip_address'] = $this->ip();
        
        // Set is_read to false by default
        $validated['is_read'] = false;
        
        return $validated;
    }
}