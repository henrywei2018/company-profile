<x-mail::message>
# Quotation Status Update

Dear {{ $quotation->name }},

We wanted to update you on the status of your quotation request for **{{ $quotation->project_type ?: 'your project' }}**.

## Status Update

Your quotation request has been **{{ ucfirst($quotation->status) }}**.

@if($quotation->status === 'approved')
🎉 **Great news!** We're excited to work with you on this project.

## Next Steps

1. We will contact you within 24 hours to discuss project details
2. We'll schedule a meeting to finalize requirements
3. A detailed project proposal will be prepared
4. Contract and timeline will be established

@if($quotation->estimated_cost)
**Estimated Cost:** {{ $quotation->estimated_cost }}
@endif

@if($quotation->estimated_timeline)
**Estimated Timeline:** {{ $quotation->estimated_timeline }}
@endif

@elseif($quotation->status === 'rejected')
Unfortunately, we cannot proceed with your request at this time.

@if($quotation->admin_notes)
## Additional Information

{{ $quotation->admin_notes }}
@endif

We appreciate your interest and encourage you to reach out for future projects.

@elseif($quotation->status === 'reviewed')
Your quotation is currently under review by our team. We will provide a detailed response shortly.

@endif

## Your Request Details

**Request ID:** #{{ $quotation->id }}  
**Project Type:** {{ $quotation->project_type ?: 'General Inquiry' }}  
**Service:** {{ $quotation->service?->title ?: 'Not specified' }}  
**Submitted:** {{ $quotation->created_at->format('F j, Y') }}

@if($quotation->admin_notes && $quotation->status !== 'rejected')
## Additional Notes

{{ $quotation->admin_notes }}
@endif

## Contact Us

If you have any questions, please don't hesitate to contact us:

- **Email:** {{ config('mail.from.address', 'info@usahaprimalestari.com') }}
- **Phone:** +62 xxx-xxxx-xxxx

<x-mail::button :url="config('app.url')">
Visit Our Website
</x-mail::button>

Best regards,  
**CV Usaha Prima Lestari Team**

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>