<x-mail::message>
# New Quotation Request Received

A new quotation request has been submitted and requires your attention.

## Client Information

**Name:** {{ $quotation->name }}  
**Email:** {{ $quotation->email }}  
**Phone:** {{ $quotation->phone ?: 'Not provided' }}  
**Company:** {{ $quotation->company ?: 'Not provided' }}

## Project Details

**Project Type:** {{ $quotation->project_type ?: 'General Inquiry' }}  
**Service:** {{ $quotation->service?->title ?: 'Not specified' }}  
**Location:** {{ $quotation->location ?: 'Not specified' }}  
**Budget Range:** {{ $quotation->budget_range ?: 'Not specified' }}  
**Desired Start Date:** {{ $quotation->start_date?->format('F j, Y') ?: 'Not specified' }}

@if($quotation->requirements)
## Requirements

{{ $quotation->requirements }}
@endif
@if($quotation->attachments && $quotation->attachments->count() > 0)
<div class="section">
    <h3 style="color: #7c2d12; margin-top: 0;">Attachments ({{ $quotation->attachments->count() }})</h3>
    @foreach($quotation->attachments as $attachment)
    <div style="background-color: #f1f5f9; padding: 12px; margin: 8px 0; border-radius: 4px; display: flex; align-items: center;">
        <div style="background-color: #dbeafe; width: 40px; height: 40px; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
            📄
        </div>
        <div>
            <div style="font-weight: bold; color: #1e293b;">{{ $attachment->file_name }}</div>
            <div style="font-size: 12px; color: #6b7280;">
                {{ $attachment->formatted_file_size }} • {{ strtoupper($attachment->file_extension) }}
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
## Quick Actions

<x-mail::button :url="route('admin.quotations.show', $quotation)">
View Full Details
</x-mail::button>

<x-mail::button :url="route('admin.quotations.edit', $quotation)" color="success">
Respond to Client
</x-mail::button>

**Request ID:** #{{ $quotation->id }}  
**Received:** {{ $quotation->created_at->format('F j, Y \a\t g:i A') }}

Thanks,<br>
{{ config('app.name') }} System
</x-mail::message>