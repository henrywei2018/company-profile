<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Configuration Test</title>
</head>
<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
        <h2 style="color: #28a745; margin-top: 0;">✅ Email Configuration Test</h2>
        
        <p>This is a test email to verify that your email configuration is working correctly.</p>
        
        <div style="background: white; padding: 15px; border-radius: 4px; margin: 20px 0;">
            <h3 style="margin-top: 0;">Test Details:</h3>
            <ul>
                <li><strong>Sent at:</strong> {{ $timestamp }}</li>
                <li><strong>Application:</strong> {{ config('app.name') }}</li>
                <li><strong>Environment:</strong> {{ config('app.env') }}</li>
                <li><strong>Mail Driver:</strong> {{ config('mail.default') }}</li>
            </ul>
        </div>
        
        <p>If you received this email, your notification system is working correctly!</p>
        
        <hr style="border: none; border-top: 1px solid #dee2e6; margin: 30px 0;">
        
        <p style="color: #6c757d; font-size: 14px; margin-bottom: 0;">
            This is an automated test email from {{ config('app.name') }}.
        </p>
    </div>
</body>
</html>