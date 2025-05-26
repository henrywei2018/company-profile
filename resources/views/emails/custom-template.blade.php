{{-- resources/views/emails/custom-template.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ settings('company_name', 'CV Usaha Prima Lestari') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .email-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
        }
        .company-name {
            color: {{ settings('email_primary_color', '#1f2937') }};
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .company-tagline {
            color: #6b7280;
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        .email-body {
            color: #4b5563;
            line-height: 1.6;
        }
        .email-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #9ca3af;
            font-size: 12px;
        }
        .contact-info {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .contact-info h3 {
            color: {{ settings('email_primary_color', '#1f2937') }};
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        .contact-info p {
            margin: 5px 0;
            font-size: 14px;
            color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-content">
            <div class="email-header">
                @if(settings('email_header_logo'))
                    <img src="{{ asset('storage/' . settings('email_header_logo')) }}" alt="{{ settings('company_name', 'CV Usaha Prima Lestari') }}" style="max-height: 60px; margin-bottom: 10px;">
                @endif
                <h1 class="company-name">{{ settings('company_name', 'CV Usaha Prima Lestari') }}</h1>
                @if(settings('company_tagline'))
                    <p class="company-tagline">{{ settings('company_tagline') }}</p>
                @endif
            </div>
            
            <div class="email-body">
                {!! $content !!}
            </div>
            
            @if(settings('contact_email') || settings('contact_phone') || settings('company_website'))
            <div class="contact-info">
                <h3>Contact Information</h3>
                @if(settings('contact_email'))
                    <p>📧 Email: {{ settings('contact_email') }}</p>
                @endif
                @if(settings('contact_phone'))
                    <p>📞 Phone: {{ settings('contact_phone') }}</p>
                @endif
                @if(settings('company_website'))
                    <p>🌐 Website: {{ settings('company_website') }}</p>
                @endif
            </div>
            @endif
            
            <div class="email-footer">
                <p>{{ settings('email_footer_text', settings('company_name', 'CV Usaha Prima Lestari') . ' - Professional Construction & General Supplier') }}</p>
                <p>This email was sent from {{ settings('company_name', 'CV Usaha Prima Lestari') }}. Please do not reply directly to this automated message.</p>
            </div>
        </div>
    </div>
</body>
</html>