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