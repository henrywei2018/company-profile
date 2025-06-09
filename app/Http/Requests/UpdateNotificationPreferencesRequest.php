<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationPreferencesRequest extends FormRequest
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
            // General notification preferences
            'email_notifications' => ['nullable', 'boolean'],
            
            // Specific notification types
            'project_update_notifications' => ['nullable', 'boolean'],
            'quotation_update_notifications' => ['nullable', 'boolean'],
            'message_reply_notifications' => ['nullable', 'boolean'],
            'deadline_alert_notifications' => ['nullable', 'boolean'],
            'chat_notifications' => ['nullable', 'boolean'],
            'system_notifications' => ['nullable', 'boolean'],
            'marketing_notifications' => ['nullable', 'boolean'],
            'testimonial_notifications' => ['nullable', 'boolean'],
            
            // Admin-specific preferences
            'urgent_notifications' => ['nullable', 'boolean'],
            'user_registration_notifications' => ['nullable', 'boolean'],
            'security_alert_notifications' => ['nullable', 'boolean'],
            
            // Notification frequency and timing
            'notification_frequency' => [
                'nullable', 
                'string', 
                'in:immediate,hourly,daily,weekly'
            ],
            'quiet_hours' => ['nullable', 'array'],
            'quiet_hours.start' => ['nullable', 'string', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'quiet_hours.end' => ['nullable', 'string', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'quiet_hours.enabled' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'notification_frequency.in' => 'Please select a valid notification frequency.',
            'quiet_hours.start.regex' => 'Quiet hours start time must be in HH:MM format.',
            'quiet_hours.end.regex' => 'Quiet hours end time must be in HH:MM format.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'email_notifications' => 'email notifications',
            'project_update_notifications' => 'project update notifications',
            'quotation_update_notifications' => 'quotation update notifications',
            'message_reply_notifications' => 'message reply notifications',
            'deadline_alert_notifications' => 'deadline alert notifications',
            'chat_notifications' => 'chat notifications',
            'system_notifications' => 'system notifications',
            'marketing_notifications' => 'marketing notifications',
            'testimonial_notifications' => 'testimonial notifications',
            'urgent_notifications' => 'urgent notifications',
            'user_registration_notifications' => 'user registration notifications',
            'security_alert_notifications' => 'security alert notifications',
            'notification_frequency' => 'notification frequency',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        // Convert checkbox values to booleans
        $booleanFields = [
            'email_notifications',
            'project_update_notifications',
            'quotation_update_notifications',
            'message_reply_notifications',
            'deadline_alert_notifications',
            'chat_notifications',
            'system_notifications',
            'marketing_notifications',
            'testimonial_notifications',
            'urgent_notifications',
            'user_registration_notifications',
            'security_alert_notifications',
        ];

        foreach ($booleanFields as $field) {
            if (isset($input[$field])) {
                $input[$field] = $this->boolean($field);
            }
        }

        // Handle quiet hours array
        if (isset($input['quiet_hours'])) {
            $quietHours = $input['quiet_hours'];
            
            // Ensure enabled is boolean
            if (isset($quietHours['enabled'])) {
                $quietHours['enabled'] = filter_var($quietHours['enabled'], FILTER_VALIDATE_BOOLEAN);
            }
            
            // If not enabled, clear start and end times
            if (empty($quietHours['enabled'])) {
                $quietHours = ['enabled' => false];
            }
            
            $input['quiet_hours'] = $quietHours;
        }

        $this->replace($input);
    }
}