<x-mail::message>
# Thank You for Your Quotation Request

Dear {{ $quotation->name }},

Thank you for reaching out to CV Usaha Prima Lestari. We have received your quotation request and our team is currently reviewing your requirements.

## Your Request Details

**Project Type:** {{ $quotation->project_type ?: 'General Inquiry' }}  
**Service:** {{ $quotation->service?->title ?: 'Not specified' }}  
**Location:** {{ $quotation->location ?: 'Not specified' }}  
**Budget Range:** {{ $quotation->budget_range ?: 'Not specified' }}  
**Request ID:** #{{ $quotation->id }}

## What Happens Next?

1. Our team will review your requirements within 24-48 hours
2. We may contact you for additional details if needed
3. You will receive a detailed quotation response via email
4. We'll schedule a consultation to discuss your project further

## Contact Information

If you have any questions or need to provide additional information, please don't hesitate to contact us:

- **Email:** {{ config('mail.from.address', 'info@usahaprimalestari.com') }}
- **Phone:** +62 xxx-xxxx-xxxx
- **Website:** {{ config('app.url') }}

We appreciate your interest in our services and look forward to working with you.

Best regards,  
**CV Usaha Prima Lestari Team**

<x-mail::button :url="config('app.url')">
Visit Our Website
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>