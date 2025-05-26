<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Chat Report</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3b82f6; color: white; padding: 20px; text-align: center; }
        .stats { background: #f8f9fa; padding: 20px; margin: 20px 0; }
        .stat-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #dee2e6; }
        .stat-value { font-weight: bold; color: #3b82f6; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Daily Chat Report</h1>
            <p>{{ $date }}</p>
        </div>
        
        <div class="stats">
            <h2>Today's Chat Statistics</h2>
            
            <div class="stat-item">
                <span>Total Chat Sessions:</span>
                <span class="stat-value">{{ $stats['total_sessions_today'] }}</span>
            </div>
            
            <div class="stat-item">
                <span>Currently Active:</span>
                <span class="stat-value">{{ $stats['active_sessions'] }}</span>
            </div>
            
            <div class="stat-item">
                <span>Waiting for Response:</span>
                <span class="stat-value">{{ $stats['waiting_sessions'] }}</span>
            </div>
            
            @if($stats['waiting_sessions'] > 0)
                <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px;">
                    <strong>⚠️ Action Required:</strong> There are {{ $stats['waiting_sessions'] }} customers waiting for a response.
                    <br><a href="{{ route('admin.chat.index') }}" style="color: #3b82f6;">View Chat Dashboard</a>
                </div>
            @endif
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ route('admin.chat.index') }}" 
               style="display: inline-block; padding: 12px 24px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px;">
                View Chat Dashboard
            </a>
        </div>
        
        <div style="text-align: center; margin-top: 20px; font-size: 12px; color: #666;">
            This is an automated report from {{ config('app.name') }}
        </div>
    </div>
</body>
</html>