{{-- resources/views/admin/company/pdf/export.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .logo {
            max-width: 120px;
            max-height: 60px;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .tagline {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        
        .export-info {
            font-size: 10px;
            color: #9ca3af;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
            margin-left: 20px;
            margin-left: 20px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #d1d5db;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .info-item {
            margin-bottom: 10px;
        }
        
        .info-label {
            font-weight: bold;
            color: #4b5563;
            display: inline-block;
            width: 120px;
        }
        
        .info-value {
            color: #1f2937;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            text-align: center;
            padding: 15px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }
        
        .stat-number {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
        }
        
        .stat-label {
            font-size: 10px;
            color: #6b7280;
            margin-top: 5px;
        }
        
        .certificates-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-right: 20px;
        }
        
        .certificates-table th,
        .certificates-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .certificates-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #374151;
        }
        
        .social-links {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        
        .social-item {
            font-size: 11px;
        }
        
        .values-list {
            list-style: none;
            padding: 0;
        }
        
        .values-list li {
            padding: 5px 0;
            position: relative;
            padding-left: 20px;
        }
        
        .values-list li:before {
            content: "•";
            color: #3b82f6;
            font-weight: bold;
            position: absolute;
            left: 0;
        }
        
        .footer {
            position: fixed;
            bottom: 30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        @if($company->logo_url)
            <img src="{{ public_path('storage/' . $company->logo) }}" alt="Company Logo" class="logo">
        @endif
        <div class="company-name">{{ $company->company_name }}</div>
        @if($company->tagline)
            <div class="tagline">{{ $company->tagline }}</div>
        @endif
    </div>

    <!-- Company Information -->
    <div class="section">
        <div class="section-title">Company Information</div>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <span class="info-label">Company Name:</span>
                    <span class="info-value">{{ $company->company_name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $company->email }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $company->phone }}</span>
                </div>
                @if($company->whatsapp)
                <div class="info-item">
                    <span class="info-label">WhatsApp:</span>
                    <span class="info-value">{{ $company->whatsapp }}</span>
                </div>
                @endif
            </div>
            <div>
                <div class="info-item">
                    <span class="info-label">Address:</span>
                    <span class="info-value">{{ $company->full_address }}</span>
                </div>
                @if($company->country)
                <div class="info-item">
                    <span class="info-label">Country:</span>
                    <span class="info-value">{{ $company->country }}</span>
                </div>
                @endif
                @if($company->latitude && $company->longitude)
                <div class="info-item">
                    <span class="info-label">Coordinates:</span>
                    <span class="info-value">{{ $company->latitude }}, {{ $company->longitude }}</span>
                </div>
                @endif
            </div>
        </div>
        
        @if($company->about)
        <div class="info-item">
            <span class="info-label">About:</span>
            <span class="info-value">{{ $company->about }}</span>
        </div>
        @endif
    </div>

    <!-- Vision & Mission -->
    @if($company->vision || $company->mission)
    <div class="section">
        <div class="section-title">Vision & Mission</div>
        @if($company->vision)
        <div class="info-item">
            <span class="info-label">Vision:</span>
            <span class="info-value">{{ $company->vision }}</span>
        </div>
        @endif
        @if($company->mission)
        <div class="info-item">
            <span class="info-label">Mission:</span>
            <span class="info-value">{{ $company->mission }}</span>
        </div>
        @endif
    </div>
    @endif

    <!-- Company Values -->
    @if($company->values && count($company->values) > 0)
    <div class="section">
        <div class="section-title">Company Values</div>
        <ul class="values-list">
            @foreach($company->values as $value)
                <li>{{ $value }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Social Media -->
    @if(count($company->social_links) > 0)
    <div class="section">
        <div class="section-title">Social Media Presence</div>
        <div class="social-links">
            @foreach($company->social_links as $platform => $url)
                <div class="social-item">
                    <span class="info-label">{{ ucfirst($platform) }}:</span>
                    <span class="info-value">{{ $url }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- SEO Information -->
    @if($seo)
    <div class="section">
        <div class="section-title">SEO Information</div>
        @if($seo->title)
        <div class="info-item">
            <span class="info-label">Meta Title:</span>
            <span class="info-value">{{ $seo->title }}</span>
        </div>
        @endif
        @if($seo->description)
        <div class="info-item">
            <span class="info-label">Meta Description:</span>
            <span class="info-value">{{ $seo->description }}</span>
        </div>
        @endif
        @if($seo->keywords)
        <div class="info-item">
            <span class="info-label">Keywords:</span>
            <span class="info-value">{{ $seo->keywords }}</span>
        </div>
        @endif
    </div>
    @endif

    <!-- Certificates -->
    @if($certificates->count() > 0)
    <div class="section page-break">
        <div class="section-title">Certificates ({{ $certificates->count() }})</div>
        <table class="certificates-table">
            <thead>
                <tr>
                    <th>Certificate Name</th>
                    <th>Issuer</th>
                    <th>Issue Date</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($certificates as $cert)
                <tr>
                    <td>{{ $cert->name }}</td>
                    <td>{{ $cert->issuer }}</td>
                    <td>{{ $cert->issue_date ? $cert->issue_date->format('M Y') : 'N/A' }}</td>
                    <td>{{ $cert->expiry_date ? $cert->expiry_date->format('M Y') : 'No Expiry' }}</td>
                    <td>{{ $cert->status ? 'Active' : 'Inactive' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>{{ $company->company_name }} - Company Profile Report</p>
        <p>This document was generated on {{ $exportDate->format('F j, Y') }} at {{ $exportDate->format('g:i A') }}</p>
    </div>
</body>
</html>