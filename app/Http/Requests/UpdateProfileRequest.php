<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $user = $this->user();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('users')->ignore($user->id)
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'company' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'allow_testimonials' => ['nullable', 'boolean'],
            'allow_public_profile' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Full name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already taken.',
            'phone.max' => 'Phone number must not exceed 20 characters.',
            'company.max' => 'Company name must not exceed 255 characters.',
            'position.max' => 'Position must not exceed 255 characters.',
            'website.url' => 'Please enter a valid website URL.',
            'address.max' => 'Address must not exceed 500 characters.',
            'bio.max' => 'Bio must not exceed 1000 characters.',
            'avatar.image' => 'Avatar must be an image file.',
            'avatar.mimes' => 'Avatar must be a JPEG, PNG, JPG, or GIF file.',
            'avatar.max' => 'Avatar file size must not exceed 2MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'full name',
            'email' => 'email address',
            'phone' => 'phone number',
            'company' => 'company name',
            'position' => 'job position',
            'website' => 'website URL',
            'address' => 'address',
            'city' => 'city',
            'state' => 'state/province',
            'postal_code' => 'postal code',
            'country' => 'country',
            'bio' => 'biography',
            'avatar' => 'profile picture',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        // Clean and normalize data
        if (isset($input['email'])) {
            $input['email'] = strtolower(trim($input['email']));
        }

        if (isset($input['phone'])) {
            $input['phone'] = trim($input['phone']);
        }

        if (isset($input['website']) && !empty($input['website'])) {
            $website = trim($input['website']);
            if (!str_starts_with($website, 'http://') && !str_starts_with($website, 'https://')) {
                $input['website'] = 'https://' . $website;
            }
        }

        // Convert checkbox values to booleans
        $input['allow_testimonials'] = $this->boolean('allow_testimonials');
        $input['allow_public_profile'] = $this->boolean('allow_public_profile');

        $this->replace($input);
    }
}