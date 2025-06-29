{{-- resources/views/emails/otp-verification.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verify Your Email - {{ $companyName }}</title>
    <style>
        /* Reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }
        
        .email-wrapper {
            background-color: #f8fafc;
            padding: 40px 20px;
            min-height: 100vh;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Header with gradient */
        .email-header {
            background: linear-gradient(135deg, #ea580c 0%, #f59e0b 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .email-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
        }
        
        .logo-container {
            position: relative;
            z-index: 2;
            margin-bottom: 20px;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        
        .logo svg {
            width: 32px;
            height: 32px;
            color: white;
        }
        
        .email-header h1 {
            color: white;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 2;
        }
        
        .email-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            margin: 0;
            position: relative;
            z-index: 2;
        }
        
        /* Content */
        .email-content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 16px;
        }
        
        .intro-text {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 32px;
            line-height: 1.6;
        }
        
        /* OTP Code Section */
        .otp-section {
            background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%);
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 32px;
            text-align: center;
            margin: 32px 0;
            position: relative;
            overflow: hidden;
        }
        
        .otp-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.3; }
        }
        
        .otp-label {
            font-size: 14px;
            font-weight: 600;
            color: #92400e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            position: relative;
            z-index: 2;
        }
        
        .otp-code {
            font-size: 36px;
            font-weight: 800;
            color: #92400e;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 16px 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
        }
        
        .otp-expire {
            font-size: 14px;
            color: #d97706;
            font-weight: 500;
            position: relative;
            z-index: 2;
        }
        
        /* Instructions */
        .instructions {
            background-color: #f0f9ff;
            border: 1px solid #e0f2fe;
            border-radius: 8px;
            padding: 24px;
            margin: 32px 0;
        }
        
        .instructions h3 {
            color: #0c4a6e;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }
        
        .instructions h3::before {
            content: '💡';
            margin-right: 8px;
            font-size: 18px;
        }
        
        .instructions ol {
            color: #075985;
            margin-left: 20px;
            line-height: 1.7;
        }
        
        .instructions li {
            margin-bottom: 8px;
        }
        
        /* Security Notice */
        .security-notice {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 20px;
            margin: 24px 0;
        }
        
        .security-notice h4 {
            color: #991b1b;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        
        .security-notice h4::before {
            content: '🔒';
            margin-right: 8px;
        }
        
        .security-notice p {
            color: #dc2626;
            font-size: 13px;
            margin: 0;
            line-height: 1.5;
        }
        
        /* Support Section */
        .support-section {
            text-align: center;
            margin: 32px 0;
            padding: 24px;
            background-color: #f9fafb;
            border-radius: 8px;
        }
        
        .support-text {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 12px;
        }
        
        .support-email {
            color: #ea580c;
            text-decoration: none;
            font-weight: 600;
        }
        
        .support-email:hover {
            text-decoration: underline;
        }
        
        /* Footer */
        .email-footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer-logo {
            margin-bottom: 16px;
        }
        
        .footer-text {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 16px;
        }
        
        .footer-links {
            margin-bottom: 16px;
        }
        
        .footer-links a {
            color: #ea580c;
            text-decoration: none;
            font-size: 13px;
            margin: 0 12px;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
        
        .copyright {
            color: #9ca3af;
            font-size: 12px;
            margin: 0;
        }
        
        /* Responsive */
        @media (max-width: 600px) {
            .email-wrapper {
                padding: 20px 10px;
            }
            
            .email-header {
                padding: 30px 20px;
            }
            
            .email-header h1 {
                font-size: 24px;
            }
            
            .email-content {
                padding: 30px 20px;
            }
            
            .otp-section {
                padding: 24px;
                margin: 24px 0;
            }
            
            .otp-code {
                font-size: 28px;
                letter-spacing: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <div class="logo-container">
                    <div class="logo">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
                <h1>{{ $companyName }}</h1>
                <p>Email Verification Required</p>
            </div>
            
            <!-- Content -->
            <div class="email-content">
                <div class="greeting">
                    Hello {{ $userName }}! 👋
                </div>
                
                <div class="intro-text">
                    Thank you for registering with <strong>{{ $companyName }}</strong>. To complete your registration and secure your account, please verify your email address using the verification code below.
                </div>
                
                <!-- OTP Code Section -->
                <div class="otp-section">
                    <div class="otp-label">Your Verification Code</div>
                    <div class="otp-code">{{ $otpCode }}</div>
                    <div class="otp-expire">⏰ This code expires in {{ $expiresInMinutes }} minutes</div>
                </div>
                
                <!-- Instructions -->
                <div class="instructions">
                    <h3>How to verify your email:</h3>
                    <ol>
                        <li>Return to the verification page on our website</li>
                        <li>Enter the 6-digit code shown above</li>
                        <li>Click "Verify Email" to complete the process</li>
                        <li>You'll be redirected to your dashboard</li>
                    </ol>
                </div>
                
                <!-- Security Notice -->
                <div class="security-notice">
                    <h4>Security Notice</h4>
                    <p>This verification code is unique to your account and should not be shared with anyone. If you didn't request this verification, please ignore this email or contact our support team.</p>
                </div>
                
                <!-- Support Section -->
                <div class="support-section">
                    <div class="support-text">
                        Need help? We're here for you!
                    </div>
                    <a href="mailto:{{ $supportEmail }}" class="support-email">{{ $supportEmail }}</a>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="email-footer">
                <div class="footer-logo">
                    <strong>{{ $companyName }}</strong>
                </div>
                
                <div class="footer-text">
                    This email was sent to <strong>{{ $userEmail }}</strong> because you created an account with us.
                </div>
                
                <div class="footer-links">
                    <a href="{{ $appUrl }}">Visit Website</a>
                    <a href="{{ $appUrl }}/contact">Contact Support</a>
                    <a href="{{ $appUrl }}/about">About Us</a>
                </div>
                
                <div class="copyright">
                    © {{ $currentYear }} {{ $companyName }}. All rights reserved.
                </div>
            </div>
        </div>
    </div>
</body>
</html>