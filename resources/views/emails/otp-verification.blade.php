<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta name="x-apple-disable-message-reformatting">
    <title>Welcome to {{ $companyName }}</title>
    <style>
        /* Reset and base styles */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        
        body {
            margin: 0 !important;
            padding: 0 !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            line-height: 1.6;
            width: 100% !important;
            min-width: 100%;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Container styles */
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 15px;
            width: 100%;
        }
        
        .email-container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
        }
        
        /* Header styles */
        .email-header {
            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            line-height: 1.3;
        }
        
        .email-header p {
            margin: 8px 0 0 0;
            font-size: 15px;
            opacity: 0.9;
            line-height: 1.4;
        }
        
        /* Content styles */
        .email-content {
            padding: 30px 20px;
        }
        
        .greeting {
            font-size: 17px;
            color: #1f2937;
            font-weight: 600;
            margin-bottom: 18px;
            line-height: 1.4;
        }
        
        .intro-text {
            color: #4b5563;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        
        /* Verification section */
        .verification-section {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #0ea5e9;
            border-radius: 12px;
            padding: 25px 15px;
            text-align: center;
            margin: 25px 0;
        }
        
        .verification-label {
            color: #0369a1;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }
        
        .otp-code {
            background-color: #ffffff;
            border: 2px dashed #0ea5e9;
            border-radius: 8px;
            padding: 18px 10px;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 4px;
            color: #0369a1;
            font-family: 'Courier New', monospace;
            margin: 12px 0;
            display: inline-block;
            min-width: 180px;
            word-break: break-all;
        }
        
        .expiry-notice {
            color: #dc2626;
            font-size: 12px;
            font-weight: 600;
            margin-top: 12px;
            line-height: 1.4;
        }
        
        /* Instructions */
        .instructions {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 18px 15px;
            margin: 20px 0;
        }
        
        .instructions h3 {
            color: #1f2937;
            font-size: 15px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .instructions ol {
            margin: 0;
            padding-left: 18px;
            color: #4b5563;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .instructions li {
            margin-bottom: 4px;
        }
        
        /* Trust indicators */
        .trust-indicators {
            background: linear-gradient(135deg, #ecfdf5 0%, #dcfce7 100%);
            border: 1px solid #86efac;
            border-radius: 8px;
            padding: 18px 15px;
            margin: 20px 0;
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
            line-height: 1.4;
        }
        
        .trust-indicators h4::before {
            content: '✅';
            margin-right: 6px;
        }
        
        .trust-indicators p {
            color: #16a34a;
            font-size: 13px;
            margin: 0;
            line-height: 1.5;
        }
        
        /* Support section */
        .support-section {
            text-align: center;
            margin: 25px 0;
        }
        
        .support-section p {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.5;
            margin: 0 0 8px 0;
        }
        
        .support-section a {
            color: #ea580c;
            font-weight: 600;
            text-decoration: none;
        }
        
        .support-section a:hover {
            text-decoration: underline;
        }
        
        /* Footer styles */
        .email-footer {
            background-color: #f9fafb;
            padding: 25px 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .company-info {
            color: #374151;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .footer-text {
            color: #6b7280;
            font-size: 12px;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        
        .footer-links {
            margin-top: 12px;
        }
        
        .footer-links a {
            color: #ea580c;
            text-decoration: none;
            font-size: 12px;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
        
        .footer-links .separator {
            color: #d1d5db;
            margin: 0 6px;
        }
        
        /* Mobile-specific styles */
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 10px !important;
            }
            
            .email-header {
                padding: 25px 15px !important;
            }
            
            .email-header h1 {
                font-size: 22px !important;
            }
            
            .email-header p {
                font-size: 14px !important;
            }
            
            .email-content {
                padding: 25px 15px !important;
            }
            
            .greeting {
                font-size: 16px !important;
            }
            
            .intro-text {
                font-size: 14px !important;
            }
            
            .verification-section {
                padding: 20px 10px !important;
                margin: 20px 0 !important;
            }
            
            .otp-code {
                font-size: 24px !important;
                letter-spacing: 3px !important;
                padding: 15px 8px !important;
                min-width: 160px !important;
            }
            
            .verification-label {
                font-size: 12px !important;
            }
            
            .expiry-notice {
                font-size: 11px !important;
            }
            
            .instructions {
                padding: 15px 12px !important;
            }
            
            .instructions h3 {
                font-size: 14px !important;
            }
            
            .instructions ol {
                font-size: 13px !important;
                padding-left: 16px !important;
            }
            
            .trust-indicators {
                padding: 15px 12px !important;
            }
            
            .trust-indicators h4 {
                font-size: 13px !important;
                flex-direction: column !important;
                gap: 4px !important;
            }
            
            .trust-indicators h4::before {
                margin-right: 0 !important;
                margin-bottom: 2px !important;
            }
            
            .trust-indicators p {
                font-size: 12px !important;
            }
            
            .support-section p {
                font-size: 13px !important;
            }
            
            .email-footer {
                padding: 20px 15px !important;
            }
            
            .footer-text {
                font-size: 11px !important;
            }
            
            .footer-links {
                margin-top: 10px !important;
            }
            
            .footer-links a {
                font-size: 11px !important;
            }
            
            .footer-links .separator {
                margin: 0 4px !important;
            }
        }
        
        /* Dark mode support for email clients that support it */
        @media (prefers-color-scheme: dark) {
            .email-container {
                background-color: #1f2937 !important;
            }
            
            .greeting {
                color: #f9fafb !important;
            }
            
            .intro-text {
                color: #d1d5db !important;
            }
            
            .instructions {
                background-color: #374151 !important;
            }
            
            .instructions h3 {
                color: #f9fafb !important;
            }
            
            .instructions ol {
                color: #d1d5db !important;
            }
            
            .email-footer {
                background-color: #374151 !important;
            }
            
            .company-info {
                color: #f9fafb !important;
            }
            
            .footer-text {
                color: #9ca3af !important;
            }
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
                
                <!-- Support Section -->
                <div class="support-section">
                    <p>
                        Need assistance? Our support team is here to help!<br>
                        <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>
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
                
                <div class="footer-links">
                    <a href="{{ $appUrl }}">Visit Website</a>
                    <span class="separator">|</span>
                    <a href="mailto:{{ $supportEmail }}">Contact Support</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>