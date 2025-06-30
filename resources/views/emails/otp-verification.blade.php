<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $companyName }}</title>
    <style>
        /* Same styles as before but with improvements */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            line-height: 1.6;
        }
        
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .email-container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .email-header {
            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        
        .email-header p {
            margin: 8px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        
        .email-content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            color: #1f2937;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .intro-text {
            color: #4b5563;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .verification-section {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #0ea5e9;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        
        .verification-label {
            color: #0369a1;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }
        
        .otp-code {
            background-color: #ffffff;
            border: 2px dashed #0ea5e9;
            border-radius: 8px;
            padding: 20px;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 6px;
            color: #0369a1;
            font-family: 'Courier New', monospace;
            margin: 15px 0;
            display: inline-block;
            min-width: 200px;
        }
        
        .expiry-notice {
            color: #dc2626;
            font-size: 13px;
            font-weight: 600;
            margin-top: 15px;
        }
        
        .instructions {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin: 24px 0;
        }
        
        .instructions h3 {
            color: #1f2937;
            font-size: 16px;
            margin-bottom: 12px;
        }
        
        .trust-indicators {
            background: linear-gradient(135deg, #ecfdf5 0%, #dcfce7 100%);
            border: 1px solid #86efac;
            border-radius: 8px;
            padding: 20px;
            margin: 24px 0;
            text-align: center;
        }
        
        .trust-indicators h4 {
            color: #166534;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .trust-indicators h4::before {
            content: '✅';
            margin-right: 8px;
        }
        
        .email-footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer-text {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 16px;
        }
        
        .company-info {
            color: #374151;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <h1>Welcome to {{ $companyName }}!</h1>
                <p>Almost there - just one more step</p>
            </div>
            
            <!-- Content -->
            <div class="email-content">
                <div class="greeting">
                    Hello {{ $userName }}! 👋
                </div>
                
                <div class="intro-text">
                    Thank you for joining <strong>{{ $companyName }}</strong>! We're excited to have you on board. To get started and secure your account, please verify your email address using the code below.
                </div>
                
                <!-- Verification Section -->
                <div class="verification-section">
                    <div class="verification-label">Your Verification Code</div>
                    <div class="otp-code">{{ $otpCode }}</div>
                    <div class="expiry-notice">⏰ Expires in {{ $expiresInMinutes }} minutes</div>
                </div>
                
                <!-- Instructions -->
                <div class="instructions">
                    <h3>Quick verification steps:</h3>
                    <ol>
                        <li>Go back to the verification page</li>
                        <li>Enter the 6-digit code above</li>
                        <li>Click "Verify Email"</li>
                        <li>Start exploring your dashboard!</li>
                    </ol>
                </div>
                
                <!-- Trust Indicators -->
                <div class="trust-indicators">
                    <h4>Why verify your email?</h4>
                    <p>Email verification helps us protect your account and ensure you receive important updates about your projects and services.</p>
                </div>
                
                <div style="text-align: center; margin: 32px 0;">
                    <p style="color: #6b7280; font-size: 14px;">
                        Need assistance? Our support team is here to help!<br>
                        <a href="mailto:{{ $supportEmail }}" style="color: #ea580c; font-weight: 600;">{{ $supportEmail }}</a>
                    </p>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="email-footer">
                <div class="company-info">
                    {{ $companyName }}
                </div>
                
                <div class="footer-text">
                    This email was sent to <strong>{{ $userEmail }}</strong> because you created an account with us.<br>
                    © {{ $currentYear }} {{ $companyName }}. All rights reserved.
                </div>
                
                <div style="margin-top: 16px;">
                    <a href="{{ $appUrl }}" style="color: #ea580c; text-decoration: none; font-size: 13px;">Visit Website</a>
                    <span style="color: #d1d5db; margin: 0 8px;">|</span>
                    <a href="mailto:{{ $supportEmail }}" style="color: #ea580c; text-decoration: none; font-size: 13px;">Contact Support</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>