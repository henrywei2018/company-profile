<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Database\Seeder;

class ProjectFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all completed projects
        $projects = Project::where('status', 'completed')->get();
        
        foreach ($projects as $project) {
            // Common file types
            $fileTypes = [
                'pdf' => ['application/pdf', 5 * 1024 * 1024], // 5MB
                'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 2 * 1024 * 1024], // 2MB
                'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 3 * 1024 * 1024], // 3MB
                'jpg' => ['image/jpeg', 1 * 1024 * 1024], // 1MB
                'png' => ['image/png', 1.5 * 1024 * 1024], // 1.5MB
            ];
            
            // Common file categories
            $categories = ['Contract', 'Design', 'Report', 'Presentation', 'Schedule', 'Budget', 'Technical'];
            
            // Project proposal
            ProjectFile::create([
                'project_id' => $project->id,
                'file_path' => 'files/projects/' . $project->id . '/project-proposal.pdf',
                'file_name' => 'Project Proposal.pdf',
                'file_type' => $fileTypes['pdf'][0],
                'file_size' => $fileTypes['pdf'][1],
                'category' => 'Proposal',
                'description' => 'Initial project proposal document outlining scope, timeline, and budget.',
                'is_public' => true,
                'download_count' => rand(5, 20),
            ]);
            
            // Project contract
            ProjectFile::create([
                'project_id' => $project->id,
                'file_path' => 'files/projects/' . $project->id . '/project-contract.pdf',
                'file_name' => 'Project Contract.pdf',
                'file_type' => $fileTypes['pdf'][0],
                'file_size' => $fileTypes['pdf'][1],
                'category' => 'Contract',
                'description' => 'Official contract document between client and contractor.',
                'is_public' => false,
                'download_count' => rand(2, 10),
            ]);
            
            // Project timeline
            ProjectFile::create([
                'project_id' => $project->id,
                'file_path' => 'files/projects/' . $project->id . '/project-timeline.xlsx',
                'file_name' => 'Project Timeline.xlsx',
                'file_type' => $fileTypes['xlsx'][0],
                'file_size' => $fileTypes['xlsx'][1],
                'category' => 'Schedule',
                'description' => 'Detailed project timeline with milestones and deadlines.',
                'is_public' => true,
                'download_count' => rand(10, 30),
            ]);
            
            // Create 3-6 random additional files
            $additionalFileCount = rand(3, 6);
            
            for ($i = 1; $i <= $additionalFileCount; $i++) {
                $fileType = array_rand($fileTypes);
                $category = $categories[array_rand($categories)];
                $isPublic = rand(0, 1) == 1;
                
                $fileName = match($category) {
                    'Contract' => 'Addendum-' . $i,
                    'Design' => 'Design-Drawing-' . $i,
                    'Report' => 'Progress-Report-' . $i,
                    'Presentation' => 'Client-Presentation-' . $i,
                    'Schedule' => 'Updated-Schedule-' . $i,
                    'Budget' => 'Budget-Report-' . $i,
                    'Technical' => 'Technical-Specification-' . $i,
                    default => 'Document-' . $i,
                };
                
                ProjectFile::create([
                    'project_id' => $project->id,
                    'file_path' => 'files/projects/' . $project->id . '/' . strtolower(str_replace(' ', '-', $fileName)) . '.' . $fileType,
                    'file_name' => $fileName . '.' . $fileType,
                    'file_type' => $fileTypes[$fileType][0],
                    'file_size' => rand(500, 10000) * 1024, // Random size between 500KB and 10MB
                    'category' => $category,
                    'description' => 'Project ' . $category . ' document ' . $i,
                    'is_public' => $isPublic,
                    'download_count' => $isPublic ? rand(0, 50) : rand(0, 10),
                ]);
            }
            
            // Final completion report for all completed projects
            if ($project->status === 'completed' && $project->end_date) {
                ProjectFile::create([
                    'project_id' => $project->id,
                    'file_path' => 'files/projects/' . $project->id . '/completion-report.pdf',
                    'file_name' => 'Project Completion Report.pdf',
                    'file_type' => $fileTypes['pdf'][0],
                    'file_size' => $fileTypes['pdf'][1],
                    'category' => 'Report',
                    'description' => 'Final project completion report with outcomes and client feedback.',
                    'is_public' => true,
                    'download_count' => rand(20, 50),
                ]);
            }
        }
    }
}