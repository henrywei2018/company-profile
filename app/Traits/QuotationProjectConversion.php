<?php

namespace App\Traits;

use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

trait QuotationProjectConversion
{
    /**
     * Check if this quotation can be converted to a project
     */
    public function canConvertToProject(): bool
    {
        return $this->status === 'approved' && 
               !$this->project_created && 
               !$this->hasExistingProject();
    }

    /**
     * Check if there's already a project for this quotation
     */
    public function hasExistingProject(): bool
    {
        if (!Schema::hasColumn('projects', 'quotation_id')) {
            return false;
        }

        return Project::where('quotation_id', $this->id)->exists();
    }

    /**
     * Get the existing project if it exists
     */
    public function getExistingProject(): ?Project
    {
        if (!Schema::hasColumn('projects', 'quotation_id')) {
            return null;
        }

        return Project::where('quotation_id', $this->id)->first();
    }

    /**
     * Check if quotation is ready for project conversion
     */
    public function isReadyForProjectConversion(): bool
    {
        return $this->status === 'approved' && 
               ($this->client_approved === true || $this->client_approved === null) &&
               !$this->project_created;
    }

    /**
     * Get suggested project data from quotation
     */
    public function getSuggestedProjectData(): array
    {
        $data = [
            'title' => $this->project_type ?? 'Project from Quotation #' . $this->id,
            'description' => $this->requirements ?? 'Project created from quotation request',
            'short_description' => $this->requirements ? Str::limit($this->requirements, 200) : null,
            'client_id' => $this->client_id,
            'quotation_id' => $this->id,
            'location' => $this->location,
            'status' => 'planning',
            'start_date' => $this->start_date,
            'year' => $this->start_date ? $this->start_date->year : now()->year,
            'featured' => false,
            'is_active' => true,
            'priority' => $this->priority ?? 'normal',
        ];

        // Add service mapping if exists
        if ($this->service_id && Schema::hasColumn('projects', 'service_id')) {
            $data['service_id'] = $this->service_id;
        }

        // Add client name if no client_id
        if (!$this->client_id && Schema::hasColumn('projects', 'client_name')) {
            $data['client_name'] = $this->name;
        }

        // Try to extract budget
        if (Schema::hasColumn('projects', 'budget')) {
            $data['budget'] = $this->extractBudgetAmount();
        }

        // Suggest category based on service or project type
        if (Schema::hasColumn('projects', 'project_category_id')) {
            $data['project_category_id'] = $this->suggestProjectCategory();
        }

        // Estimate completion date
        if (Schema::hasColumn('projects', 'estimated_completion_date')) {
            $data['estimated_completion_date'] = $this->estimateCompletionDate();
        }

        return $data;
    }

    /**
     * Extract numeric budget amount from text fields
     */
    public function extractBudgetAmount(): ?float
    {
        // Try estimated_cost first, then budget_range
        $budgetText = $this->estimated_cost ?? $this->budget_range;
        
        if (!$budgetText) {
            return null;
        }

        // Remove currency symbols and extract numbers
        $cleanText = preg_replace('/[^\d.,\-]/', '', $budgetText);
        
        // Handle ranges (take the higher value)
        if (strpos($cleanText, '-') !== false) {
            $parts = explode('-', $cleanText);
            $cleanText = trim(end($parts));
        }

        // Clean up and convert to float
        $cleanText = str_replace(',', '', $cleanText);
        
        if (is_numeric($cleanText)) {
            return floatval($cleanText);
        }

        return null;
    }

    /**
     * Suggest project category based on service or project type
     */
    public function suggestProjectCategory(): ?int
    {
        // First try to match by service
        if ($this->service) {
            $category = ProjectCategory::where('name', 'like', '%' . $this->service->title . '%')
                ->where('is_active', true)
                ->first();
            
            if ($category) {
                return $category->id;
            }
        }

        // Then try to match by project type
        if ($this->project_type) {
            $keywords = explode(' ', strtolower($this->project_type));
            
            foreach ($keywords as $keyword) {
                $category = ProjectCategory::where('name', 'like', '%' . $keyword . '%')
                    ->where('is_active', true)
                    ->first();
                
                if ($category) {
                    return $category->id;
                }
            }
        }

        // Try to find a default or "General" category
        $defaultCategory = ProjectCategory::where('name', 'like', '%general%')
            ->orWhere('name', 'like', '%default%')
            ->orWhere('name', 'like', '%misc%')
            ->where('is_active', true)
            ->first();

        return $defaultCategory?->id;
    }

    /**
     * Estimate project completion date
     */
    public function estimateCompletionDate(): ?\Carbon\Carbon
    {
        if ($this->start_date) {
            // Estimate based on project type or default to 3 months
            $estimatedDuration = $this->estimateProjectDuration();
            return $this->start_date->copy()->addMonths($estimatedDuration);
        }

        // Default to 3 months from now
        return now()->addMonths(3);
    }

    /**
     * Estimate project duration in months based on project type
     */
    protected function estimateProjectDuration(): int
    {
        if (!$this->project_type) {
            return 3; // Default 3 months
        }

        $projectType = strtolower($this->project_type);
        
        // Define duration estimates based on common project types
        $durationMap = [
            'website' => 2,
            'web development' => 3,
            'mobile app' => 4,
            'construction' => 6,
            'renovation' => 3,
            'interior design' => 2,
            'marketing campaign' => 2,
            'software development' => 4,
            'system integration' => 5,
            'consulting' => 1,
            'training' => 1,
            'audit' => 1,
        ];

        foreach ($durationMap as $keyword => $months) {
            if (strpos($projectType, $keyword) !== false) {
                return $months;
            }
        }

        // If no match, estimate based on budget
        $budget = $this->extractBudgetAmount();
        if ($budget) {
            if ($budget < 10000) return 1;
            if ($budget < 50000) return 2;
            if ($budget < 100000) return 3;
            if ($budget < 500000) return 6;
            return 12;
        }

        return 3; // Default fallback
    }

    /**
     * Generate unique project slug from title
     */
    public function generateProjectSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (Project::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Copy attachments to project
     */
    public function copyAttachmentsToProject(Project $project): int
    {
        $copiedCount = 0;

        if (!Schema::hasTable('project_files') || $this->attachments->count() === 0) {
            return $copiedCount;
        }

        foreach ($this->attachments as $attachment) {
            try {
                // Generate new file path for project
                $originalPath = $attachment->file_path;
                $fileName = pathinfo($attachment->file_name, PATHINFO_FILENAME);
                $extension = pathinfo($attachment->file_name, PATHINFO_EXTENSION);
                $newFileName = $fileName . '_from_quotation_' . $this->id . '.' . $extension;
                $newPath = 'project_files/' . $project->id . '/' . $newFileName;

                // Copy file if it exists
                if (Storage::disk('public')->exists($originalPath)) {
                    // Ensure directory exists
                    $directory = dirname($newPath);
                    if (!Storage::disk('public')->exists($directory)) {
                        Storage::disk('public')->makeDirectory($directory);
                    }

                    // Copy the file
                    Storage::disk('public')->copy($originalPath, $newPath);

                    // Create project file record
                    $project->files()->create([
                        'file_path' => $newPath,
                        'file_name' => $newFileName,
                        'original_name' => $attachment->file_name,
                        'file_type' => $attachment->file_type,
                        'file_size' => $attachment->file_size,
                        'uploaded_by' => auth()->id(),
                        'is_public' => false,
                        'description' => 'Transferred from quotation #' . $this->id,
                        'category' => 'quotation_transfer',
                    ]);

                    $copiedCount++;
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to copy attachment from quotation to project', [
                    'quotation_id' => $this->id,
                    'project_id' => $project->id,
                    'attachment_id' => $attachment->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $copiedCount;
    }

    /**
     * Mark quotation as converted to project
     */
    public function markAsConvertedToProject(Project $project): void
    {
        $updateData = [
            'project_created' => true,
            'project_created_at' => now(),
        ];

        // Add admin notes about the conversion
        if ($this->admin_notes) {
            $updateData['admin_notes'] = $this->admin_notes . "\n\n" 
                . "Converted to project: {$project->title} (ID: {$project->id}) on " . now()->format('Y-m-d H:i:s');
        } else {
            $updateData['admin_notes'] = "Converted to project: {$project->title} (ID: {$project->id}) on " . now()->format('Y-m-d H:i:s');
        }

        $this->update($updateData);
    }

    /**
     * Get conversion summary for this quotation
     */
    public function getConversionSummary(): array
    {
        $summary = [
            'can_convert' => $this->canConvertToProject(),
            'is_ready' => $this->isReadyForProjectConversion(),
            'has_existing_project' => $this->hasExistingProject(),
            'existing_project' => $this->getExistingProject(),
            'suggested_data' => $this->getSuggestedProjectData(),
            'attachments_count' => $this->attachments->count(),
            'estimated_budget' => $this->extractBudgetAmount(),
            'estimated_duration' => $this->estimateProjectDuration(),
        ];

        // Add validation warnings
        $warnings = [];
        
        if (!$this->client_id) {
            $warnings[] = 'No linked client account - project will use client name only';
        }

        if (!$this->start_date) {
            $warnings[] = 'No start date specified - project timeline may need adjustment';
        }

        if (!$this->extractBudgetAmount()) {
            $warnings[] = 'No budget information available';
        }

        if (!$this->service_id) {
            $warnings[] = 'No service specified - project category may need manual selection';
        }

        $summary['warnings'] = $warnings;

        return $summary;
    }

    /**
     * Validate quotation for conversion
     */
    public function validateForConversion(): array
    {
        $errors = [];
        $warnings = [];

        // Check basic eligibility
        if ($this->status !== 'approved') {
            $errors[] = 'Quotation must be approved before conversion';
        }

        if ($this->project_created) {
            $errors[] = 'Quotation has already been converted to a project';
        }

        if ($this->hasExistingProject()) {
            $errors[] = 'A project already exists for this quotation';
        }

        // Check for potential issues
        if (!$this->project_type || strlen($this->project_type) < 3) {
            $warnings[] = 'Project type is very short or missing';
        }

        if (!$this->requirements || strlen($this->requirements) < 10) {
            $warnings[] = 'Project requirements are very brief';
        }

        if ($this->client_approved === false) {
            $warnings[] = 'Client has declined this quotation';
        }

        if (!$this->client_id) {
            $warnings[] = 'No registered client account linked';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }
}