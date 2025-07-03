{{-- resources/views/components/admin/message-type.blade.php --}}

@props(['type'])

@php
$typeLabels = [
    'contact_form' => 'Contact Form',
    'client_to_admin' => 'Client Message',
    'client_reply' => 'Client Reply',
    'general' => 'General',
    'support' => 'Support',
    'project_inquiry' => 'Project',
    'complaint' => 'Complaint',
    'feedback' => 'Feedback'
];
@endphp

<span class="text-sm text-gray-600 dark:text-gray-300">
    {{ $typeLabels[$type] ?? ucfirst(str_replace('_', ' ', $type)) }}
</span>