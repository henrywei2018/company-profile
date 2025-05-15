<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Database\Seeder;

class ProjectImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // For the purpose of development, we'll use placeholder images
        // In a production environment, you would use actual project images
        
        // Get all projects
        $projects = Project::all();
        
        foreach ($projects as $project) {
            // Add a featured image
            ProjectImage::create([
                'project_id' => $project->id,
                'image_path' => 'images/projects/' . strtolower(str_replace(' ', '-', $project->category)) . '-project-main.jpg',
                'alt_text' => $project->title . ' - Featured Image',
                'is_featured' => true,
                'sort_order' => 1,
            ]);
            
            // Add 3-5 additional images for each project
            $imageCount = rand(3, 5);
            
            for ($i = 1; $i <= $imageCount; $i++) {
                ProjectImage::create([
                    'project_id' => $project->id,
                    'image_path' => 'images/projects/' . strtolower(str_replace(' ', '-', $project->category)) . '-project-' . $i . '.jpg',
                    'alt_text' => $project->title . ' - Image ' . $i,
                    'is_featured' => false,
                    'sort_order' => $i + 1,
                ]);
            }
        }
    }
}