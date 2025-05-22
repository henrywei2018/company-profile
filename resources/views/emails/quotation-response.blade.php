<x-mail::message>
# {{ $emailSubject }}

Dear {{ $quotation->name }},

{{ $emailMessage }}

@if($includeQuotation)
## Project Details

**Request ID:** #{{ $quotation->id }}  
**Project Type:** {{ $quotation->project_type ?: 'General Inquiry' }}  
**Service:** {{ $quotation->service?->title ?: 'Not specified' }}  
**Location:** {{ $quotation->location ?: 'Not specified' }}

@if($quotation->estimated_cost)
**Estimated Cost:** {{ $quotation->estimated_cost }}
@endif

@if($quotation->estimated_timeline)
**Estimated Timeline:** {{ $quotation->estimated_timeline }}
@endif

@if($quotation->requirements)
## Your Requirements

{{ $quotation->requirements }}
@endif
@endif

## Contact Information

For any questions or to proceed with your project:

- **Email:** {{ config('mail.from.address', 'info@usahaprimalestari.com') }}
- **Phone:** +62 xxx-xxxx-xxxx
- **Website:** {{ config('app.url') }}

<x-mail::button :url="config('app.url')">
Visit Our Website
</x-mail::button>

Best regards,  
**CV Usaha Prima Lestari Team**

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>