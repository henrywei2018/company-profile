<?php
// File: app/Services/PdfReportService.php

namespace App\Services;

use App\Models\User;
use App\Models\CompanyProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Carbon\Carbon;

class PdfReportService
{
    protected DashboardService $dashboardService;
    protected ClientAccessService $clientAccessService;

    public function __construct(
        DashboardService $dashboardService,
        ClientAccessService $clientAccessService
    ) {
        $this->dashboardService = $dashboardService;
        $this->clientAccessService = $clientAccessService;
    }

    /**
     * Generate client performance report.
     */
    public function generateClientReport(User $user, array $filters = []): Response
    {
        // Get report data
        $reportData = $this->dashboardService->generateReport($user, $filters);
        $companyProfile = CompanyProfile::getInstance();
        
        // Prepare data for PDF
        $data = [
            'user' => $user,
            'company' => $companyProfile,
            'reportData' => $reportData,
            'generatedAt' => now(),
            'period' => $this->formatPeriod($reportData['period']),
            'charts' => $this->generateChartImages($user, $filters),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('reports.client.performance', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'margin-top' => 20,
                'margin-bottom' => 20,
                'margin-left' => 15,
                'margin-right' => 15,
            ]);

        $filename = "client_report_{$user->id}_" . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate admin dashboard report.
     */
    public function generateAdminReport(User $user, array $filters = []): Response
    {
        // Get comprehensive admin data
        $reportData = $this->dashboardService->generateReport($user, $filters);
        $companyProfile = CompanyProfile::getInstance();
        
        // Get additional admin-specific data
        $adminData = [
            'user' => $user,
            'company' => $companyProfile,
            'reportData' => $reportData,
            'generatedAt' => now(),
            'period' => $this->formatPeriod($reportData['period']),
            'systemHealth' => $this->getSystemHealthData(),
            'charts' => $this->generateAdminChartImages($filters),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('reports.admin.dashboard', $adminData)
            ->setPaper('a4', 'landscape') // Landscape for admin reports
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'margin-top' => 15,
                'margin-bottom' => 15,
                'margin-left' => 15,
                'margin-right' => 15,
            ]);

        $filename = "admin_report_" . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate project report.
     */
    public function generateProjectReport(User $user, array $projectIds, array $filters = []): Response
    {
        $projects = $this->clientAccessService->getClientProjects($user)
            ->whereIn('id', $projectIds)
            ->with(['category', 'service', 'client', 'images', 'testimonial'])
            ->get();

        $companyProfile = CompanyProfile::getInstance();
        
        $data = [
            'user' => $user,
            'company' => $companyProfile,
            'projects' => $projects,
            'generatedAt' => now(),
            'summary' => $this->calculateProjectsSummary($projects),
            'filters' => $filters,
        ];

        $pdf = Pdf::loadView('reports.projects.summary', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'margin-top' => 20,
                'margin-bottom' => 20,
                'margin-left' => 15,
                'margin-right' => 15,
            ]);

        $filename = "projects_report_" . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate quotation report.
     */
    public function generateQuotationReport(User $user, array $filters = []): Response
    {
        $quotations = $this->clientAccessService->getClientQuotations($user, $filters)
            ->with(['service', 'attachments'])
            ->get();

        $companyProfile = CompanyProfile::getInstance();
        
        $data = [
            'user' => $user,
            'company' => $companyProfile,
            'quotations' => $quotations,
            'generatedAt' => now(),
            'summary' => $this->calculateQuotationsSummary($quotations),
            'filters' => $filters,
        ];

        $pdf = Pdf::loadView('reports.quotations.summary', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'margin-top' => 20,
                'margin-bottom' => 20,
                'margin-left' => 15,
                'margin-right' => 15,
            ]);

        $filename = "quotations_report_" . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate invoice/quotation PDF.
     */
    public function generateQuotationPdf(\App\Models\Quotation $quotation): Response
    {
        $companyProfile = CompanyProfile::getInstance();
        
        $data = [
            'quotation' => $quotation->load(['service', 'client']),
            'company' => $companyProfile,
            'generatedAt' => now(),
            'validUntil' => now()->addDays(30),
        ];

        $pdf = Pdf::loadView('reports.quotations.single', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'margin-top' => 20,
                'margin-bottom' => 20,
                'margin-left' => 15,
                'margin-right' => 15,
            ]);

        $filename = "quotation_{$quotation->id}_" . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate project completion certificate.
     */
    public function generateCompletionCertificate(\App\Models\Project $project): Response
    {
        if ($project->status !== 'completed') {
            throw new \InvalidArgumentException('Only completed projects can have completion certificates.');
        }

        $companyProfile = CompanyProfile::getInstance();
        
        $data = [
            'project' => $project->load(['category', 'service', 'client']),
            'company' => $companyProfile,
            'generatedAt' => now(),
            'completionDate' => $project->actual_completion_date ?: $project->updated_at,
        ];

        $pdf = Pdf::loadView('reports.projects.certificate', $data)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'margin-top' => 10,
                'margin-bottom' => 10,
                'margin-left' => 10,
                'margin-right' => 10,
            ]);

        $filename = "completion_certificate_{$project->id}_" . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Preview report before download.
     */
    public function previewClientReport(User $user, array $filters = []): string
    {
        $reportData = $this->dashboardService->generateReport($user, $filters);
        $companyProfile = CompanyProfile::getInstance();
        
        $data = [
            'user' => $user,
            'company' => $companyProfile,
            'reportData' => $reportData,
            'generatedAt' => now(),
            'period' => $this->formatPeriod($reportData['period']),
            'preview' => true,
        ];

        return view('reports.client.performance', $data)->render();
    }

    /**
     * Format period for display.
     */
    protected function formatPeriod(array $period): string
    {
        $start = Carbon::parse($period['start'])->format('M d, Y');
        $end = Carbon::parse($period['end'])->format('M d, Y');
        
        return "{$start} - {$end}";
    }

    /**
     * Get system health data for admin reports.
     */
    protected function getSystemHealthData(): array
    {
        return [
            'total_users' => \App\Models\User::count(),
            'active_users' => \App\Models\User::where('is_active', true)->count(),
            'total_projects' => \App\Models\Project::count(),
            'active_projects' => \App\Models\Project::whereIn('status', ['in_progress', 'on_hold'])->count(),
            'total_quotations' => \App\Models\Quotation::count(),
            'pending_quotations' => \App\Models\Quotation::where('status', 'pending')->count(),
            'unread_messages' => \App\Models\Message::where('is_read', false)->count(),
            'system_uptime' => '99.9%', // Mock data
            'last_backup' => now()->subHours(6),
        ];
    }

    /**
     * Generate chart images for PDF (placeholder).
     */
    protected function generateChartImages(User $user, array $filters): array
    {
        // This would generate actual chart images using a library like Chart.js or similar
        // For now, return placeholders
        return [
            'projects_by_status' => '/images/charts/projects-status-placeholder.png',
            'monthly_progress' => '/images/charts/monthly-progress-placeholder.png',
            'performance_metrics' => '/images/charts/performance-placeholder.png',
        ];
    }

    /**
     * Generate admin chart images.
     */
    protected function generateAdminChartImages(array $filters): array
    {
        return [
            'projects_overview' => '/images/charts/admin-projects-placeholder.png',
            'quotations_trends' => '/images/charts/admin-quotations-placeholder.png',
            'revenue_trends' => '/images/charts/admin-revenue-placeholder.png',
            'client_growth' => '/images/charts/admin-clients-placeholder.png',
        ];
    }

    /**
     * Calculate projects summary.
     */
    protected function calculateProjectsSummary($projects): array
    {
        return [
            'total' => $projects->count(),
            'completed' => $projects->where('status', 'completed')->count(),
            'in_progress' => $projects->where('status', 'in_progress')->count(),
            'total_value' => $projects->sum('budget'),
            'avg_duration' => $this->calculateAverageDuration($projects),
            'completion_rate' => $projects->count() > 0 ? 
                round(($projects->where('status', 'completed')->count() / $projects->count()) * 100, 1) : 0,
        ];
    }

    /**
     * Calculate quotations summary.
     */
    protected function calculateQuotationsSummary($quotations): array
    {
        return [
            'total' => $quotations->count(),
            'approved' => $quotations->where('status', 'approved')->count(),
            'pending' => $quotations->where('status', 'pending')->count(),
            'rejected' => $quotations->where('status', 'rejected')->count(),
            'conversion_rate' => $quotations->count() > 0 ? 
                round(($quotations->where('status', 'approved')->count() / $quotations->count()) * 100, 1) : 0,
        ];
    }

    /**
     * Calculate average project duration.
     */
    protected function calculateAverageDuration($projects): int
    {
        $completedProjects = $projects->where('status', 'completed')
            ->filter(fn($p) => $p->start_date && $p->actual_completion_date);
        
        if ($completedProjects->isEmpty()) {
            return 0;
        }
        
        $totalDays = $completedProjects->sum(function($project) {
            return $project->start_date->diffInDays($project->actual_completion_date);
        });
        
        return round($totalDays / $completedProjects->count());
    }

    /**
     * Get PDF configuration for different report types.
     */
    public function getPdfConfig(string $reportType): array
    {
        $configs = [
            'client_report' => [
                'paper' => 'a4',
                'orientation' => 'portrait',
                'margin-top' => 20,
                'margin-bottom' => 20,
            ],
            'admin_report' => [
                'paper' => 'a4',
                'orientation' => 'landscape',
                'margin-top' => 15,
                'margin-bottom' => 15,
            ],
            'quotation' => [
                'paper' => 'a4',
                'orientation' => 'portrait',
                'margin-top' => 25,
                'margin-bottom' => 25,
            ],
            'certificate' => [
                'paper' => 'a4',
                'orientation' => 'landscape',
                'margin-top' => 10,
                'margin-bottom' => 10,
            ],
        ];

        return $configs[$reportType] ?? $configs['client_report'];
    }

    /**
     * Add watermark to PDF (for drafts).
     */
    public function addWatermark(string $text = 'DRAFT'): array
    {
        return [
            'watermark_text' => $text,
            'watermark_font' => 'Arial',
            'watermark_size' => 50,
            'watermark_color' => [200, 200, 200],
            'watermark_angle' => 45,
        ];
    }
}
