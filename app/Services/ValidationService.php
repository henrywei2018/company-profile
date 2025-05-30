<?php


namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidationService
{
    public function validateQuotationData(array $data): array
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'service_id' => 'nullable|exists:services,id',
            'project_type' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'requirements' => 'required|string',
            'budget_range' => 'nullable|string|max:100',
            'start_date' => 'nullable|date|after:today',
        ])->validate();
    }

    public function validateProjectData(array $data): array
    {
        return Validator::make($data, [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'nullable|exists:users,id',
            'project_category_id' => 'nullable|exists:project_categories,id',
            'service_id' => 'nullable|exists:services,id',
            'status' => 'required|in:planning,in_progress,on_hold,completed,cancelled',
            'priority' => 'required|in:low,normal,high,urgent',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
        ])->validate();
    }

    public function validateMessageData(array $data): array
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
            'attachments.*' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,jpeg,png',
        ])->validate();
    }

    public function validateFileUpload(mixed $file, array $rules = []): bool
    {
        $defaultRules = [
            'file' => 'required|file|max:2048',
        ];

        $validationRules = array_merge($defaultRules, $rules);

        try {
            Validator::make(['file' => $file], $validationRules)->validate();
            return true;
        } catch (ValidationException $e) {
            return false;
        }
    }

    public function getCustomMessages(): array
    {
        return [
            'required' => 'The :attribute field is required.',
            'email' => 'Please enter a valid email address.',
            'max' => 'The :attribute may not be greater than :max characters.',
            'min' => 'The :attribute must be at least :min characters.',
            'file.max' => 'File size must not exceed 2MB.',
            'mimes' => 'File must be of type: :values.',
        ];
    }
}