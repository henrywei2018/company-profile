<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTeamRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'department_id' => ['nullable', 'exists:team_member_departments,id'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'email' => ['nullable', 'email', 'max:255', 'unique:team_members,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'twitter' => ['nullable', 'url', 'max:255'],
            'facebook' => ['nullable', 'url', 'max:255'],
            'instagram' => ['nullable', 'url', 'max:255'],
            'is_active' => ['boolean'],
            'featured' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:team_members,slug'],
            
            // SEO fields
            'meta_title' => ['nullable', 'string', 'max:60'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The team member name is required.',
            'name.max' => 'The team member name may not be greater than 255 characters.',
            'position.required' => 'The position is required.',
            'position.max' => 'The position may not be greater than 255 characters.',
            'department_id.exists' => 'The selected department does not exist.',
            'bio.max' => 'The bio may not be greater than 2000 characters.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already taken by another team member.',
            'phone.max' => 'The phone number may not be greater than 20 characters.',
            'linkedin.url' => 'Please enter a valid LinkedIn URL.',
            'twitter.url' => 'Please enter a valid Twitter URL.',
            'facebook.url' => 'Please enter a valid Facebook URL.',
            'instagram.url' => 'Please enter a valid Instagram URL.',
            'sort_order.min' => 'The sort order must be at least 0.',
            'slug.unique' => 'This slug is already taken.',
            'meta_title.max' => 'The meta title may not be greater than 60 characters.',
            'meta_description.max' => 'The meta description may not be greater than 160 characters.',
            'meta_keywords.max' => 'The meta keywords may not be greater than 255 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'team member name',
            'department_id' => 'department',
            'is_active' => 'active status',
            'featured' => 'featured status',
            'sort_order' => 'sort order',
            'meta_title' => 'meta title',
            'meta_description' => 'meta description',
            'meta_keywords' => 'meta keywords',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        // Force boolean values (default false if not present)
        $input['is_active'] = $this->boolean('is_active', false);
        $input['featured'] = $this->boolean('featured', false);

        // Set default sort order if not provided
        if (empty($input['sort_order'])) {
            $maxSortOrder = \App\Models\TeamMember::max('sort_order') ?? 0;
            $input['sort_order'] = $maxSortOrder + 1;
        }

        // Clean up URLs - ensure they have proper protocol
        foreach (['linkedin', 'twitter', 'facebook', 'instagram'] as $field) {
            if (!empty($input[$field]) && !str_starts_with($input[$field], 'http')) {
                $input[$field] = 'https://' . $input[$field];
            }
        }

        $this->replace($input);
    }
}