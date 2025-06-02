{{-- File: resources/views/emails/notification.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $mailMessage->subject ?? 'Notification' }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 0;
        }
        .header {
            background-color: #3B82F6;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }
        .intro-line, .outro-line {
            margin: 15px 0;
            color: #555;
            line-height: 1.6;
        }
        .action-button {
            display: inline-block;
            background-color: #3B82F6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .action-button:hover {
            background-color: #2563EB;
        }
        .salutation {
            margin-top: 30px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        
        <div class="content">
            @if($greeting)
                <div class="greeting">{{ $greeting }}</div>
            @endif

            @if($introLines && count($introLines) > 0)
                @foreach($introLines as $line)
                    <div class="intro-line">{{ $line }}</div>
                @endforeach
            @endif

            @if($actionText && $actionUrl)
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $actionUrl }}" class="action-button">{{ $actionText }}</a>
                </div>
            @endif

            @if($outroLines && count($outroLines) > 0)
                @foreach($outroLines as $line)
                    <div class="outro-line">{{ $line }}</div>
                @endforeach
            @endif

            @if($salutation)
                <div class="salutation">{!! $salutation !!}</div>
            @endif
        </div>

        <div class="footer">
            <p>
                This email was sent from {{ config('app.name') }}.<br>
                If you have any questions, please contact us at {{ config('mail.from.address') }}.
            </p>
        </div>
    </div>
</body>
</html>