<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectMilestone;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProjectMilestoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all projects
        $projects = Project::all();
        
        foreach ($projects as $project) {
            // Skip if project has no start date
            if (!$project->start_date) {
                continue;
            }
            
            // Define common milestones for construction projects
            $milestones = [];
            
            // Start date
            $startDate = Carbon::parse($project->start_date);
            
            // Calculate project duration (in months)
            $endDate = $project->end_date ? Carbon::parse($project->end_date) : Carbon::now()->addMonths(24);
            $duration = $startDate->diffInMonths($endDate);
            
            // Milestone 1: Project Initiation - at start
            $milestones[] = [
                'title' => 'Project Initiation',
                'description' => 'Initial project setup, team formation, and mobilization of resources.',
                'due_date' => $startDate->format('Y-m-d'),
                'completed_date' => $project->status != 'pending' ? $startDate->format('Y-m-d') : null,
                'status' => $project->status != 'pending' ? 'completed' : 'pending',
                'progress_percent' => $project->status != 'pending' ? 100 : 0,
            ];
            
            // Milestone 2: Design and Planning - 10% into project
            $planningDate = (clone $startDate)->addDays(round($duration * 30 * 0.1));
            $milestones[] = [
                'title' => 'Design and Planning',
                'description' => 'Completion of detailed designs, plans, and obtaining necessary approvals and permits.',
                'due_date' => $planningDate->format('Y-m-d'),
                'completed_date' => $project->status != 'pending' && $planningDate->isPast() ? $planningDate->format('Y-m-d') : null,
                'status' => $project->status != 'pending' && $planningDate->isPast() ? 'completed' : ($planningDate->isPast() ? 'in_progress' : 'pending'),
                'progress_percent' => $project->status != 'pending' && $planningDate->isPast() ? 100 : ($planningDate->isPast() ? 70 : 0),
            ];
            
            // Milestone 3: Foundation Work - 25% into project
            $foundationDate = (clone $startDate)->addDays(round($duration * 30 * 0.25));
            $milestones[] = [
                'title' => 'Foundation Work',
                'description' => 'Completion of all foundation and underground structural work.',
                'due_date' => $foundationDate->format('Y-m-d'),
                'completed_date' => $project->status != 'pending' && $foundationDate->isPast() ? $foundationDate->format('Y-m-d') : null,
                'status' => $project->status != 'pending' && $foundationDate->isPast() ? 'completed' : ($foundationDate->isPast() ? 'in_progress' : 'pending'),
                'progress_percent' => $project->status != 'pending' && $foundationDate->isPast() ? 100 : ($foundationDate->isPast() ? 60 : 0),
            ];
            
            // Milestone 4: Structural Work - 50% into project
            $structuralDate = (clone $startDate)->addDays(round($duration * 30 * 0.5));
            $milestones[] = [
                'title' => 'Structural Work',
                'description' => 'Completion of main structural elements including columns, beams, and floor slabs.',
                'due_date' => $structuralDate->format('Y-m-d'),
                'completed_date' => $project->status == 'completed' ? $structuralDate->format('Y-m-d') : null,
                'status' => $project->status == 'completed' ? 'completed' : ($structuralDate->isPast() ? 'in_progress' : 'pending'),
                'progress_percent' => $project->status == 'completed' ? 100 : ($structuralDate->isPast() ? 50 : 0),
            ];
            
            // Milestone 5: MEP Work - 70% into project
            $mepDate = (clone $startDate)->addDays(round($duration * 30 * 0.7));
            $milestones[] = [
                'title' => 'Mechanical, Electrical, and Plumbing Work',
                'description' => 'Installation and testing of all MEP systems including HVAC, electrical, and plumbing.',
                'due_date' => $mepDate->format('Y-m-d'),
                'completed_date' => $project->status == 'completed' ? $mepDate->format('Y-m-d') : null,
                'status' => $project->status == 'completed' ? 'completed' : ($mepDate->isPast() ? 'in_progress' : 'pending'),
                'progress_percent' => $project->status == 'completed' ? 100 : ($mepDate->isPast() ? 40 : 0),
            ];
            
            // Milestone 6: Finishing Work - 85% into project
            $finishingDate = (clone $startDate)->addDays(round($duration * 30 * 0.85));
            $milestones[] = [
                'title' => 'Finishing Work',
                'description' => 'Completion of all interior and exterior finishes including walls, floors, ceilings, and facades.',
                'due_date' => $finishingDate->format('Y-m-d'),
                'completed_date' => $project->status == 'completed' ? $finishingDate->format('Y-m-d') : null,
                'status' => $project->status == 'completed' ? 'completed' : ($finishingDate->isPast() ? 'in_progress' : 'pending'),
                'progress_percent' => $project->status == 'completed' ? 100 : ($finishingDate->isPast() ? 30 : 0),
            ];
            
            // Milestone 7: Project Completion - end date
            $milestones[] = [
                'title' => 'Project Completion',
                'description' => 'Final inspections, handover, and project closure documentation.',
                'due_date' => $endDate->format('Y-m-d'),
                'completed_date' => $project->status == 'completed' ? $endDate->format('Y-m-d') : null,
                'status' => $project->status == 'completed' ? 'completed' : 'pending',
                'progress_percent' => $project->status == 'completed' ? 100 : 0,
            ];
            
            // Create the milestones
            foreach ($milestones as $milestone) {
                ProjectMilestone::create([
                    'project_id' => $project->id,
                    'title' => $milestone['title'],
                    'description' => $milestone['description'],
                    'due_date' => $milestone['due_date'],
                    'completed_date' => $milestone['completed_date'],
                    'status' => $milestone['status'],
                    'progress_percent' => $milestone['progress_percent'],
                ]);
            }
        }
    }
}