<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProjectService
{
    protected $projectRepository;
    protected $fileUploadService;
    
    /**
     * Create a new service instance.
     *
     * @param ProjectRepositoryInterface $projectRepository
     * @param FileUploadService $fileUploadService
     */
    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        FileUploadService $fileUploadService
    ) {
        $this->projectRepository = $projectRepository;
        $this->fileUploadService = $fileUploadService;
    }
    
    /**
     * Create a new project with images
     *
     * @param array $data
     * @param array $images
     * @param array $altTexts
     * @return Project
     */
    public function createProject(array $data, array $images = [], array $altTexts = []): Project
    {
        // Create the project
        $project = $this->projectRepository->create($data);
        
        // Process images
        if (!empty($images)) {
            $this->processProjectImages($project, $images, $altTexts);
        }
        
        // Process SEO data if available
        if (isset($data['seo_title']) || isset($data['seo_description']) || isset($data['seo_keywords'])) {
            $this->processSeoData($project, $data);
        }
        
        return $project;
    }
    
    /**
     * Update a project with images
     *
     * @param Project $project
     * @param array $data
     * @param array $existingImages
     * @param array $existingAltTexts
     * @param array $newImages
     * @param array $newAltTexts
     * @param int|null $featuredImageId
     * @return Project
     */
    public function updateProject(
        Project $project,
        array $data,
        array $existingImages = [],
        array $existingAltTexts = [],
        array $newImages = [],
        array $newAltTexts = [],
        ?int $featuredImageId = null
    ): Project {
        // Update project data
        $project = $this->projectRepository->update($project, $data);
        
        // Process existing images
        $this->updateExistingImages($project, $existingImages, $existingAltTexts, $featuredImageId);
        
        // Process new images
        if (!empty($newImages)) {
            $this->processProjectImages($project, $newImages, $newAltTexts);
        }
        
        // Process SEO data
        $this->processSeoData($project, $data);
        
        return $project;
    }
    
    /**
     * Delete a project and its resources
     *
     * @param Project $project
     * @return bool
     */
    public function deleteProject(Project $project): bool
    {
        // Delete associated images
        foreach ($project->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
        
        // Delete project
        return $this->projectRepository->delete($project);
    }
    
    /**
     * Process project images
     *
     * @param Project $project
     * @param array $images
     * @param array $altTexts
     * @return void
     */
    private function processProjectImages(Project $project, array $images, array $altTexts = []): void
    {
        foreach ($images as $index => $image) {
            $path = $this->fileUploadService->uploadImage(
                $image,
                'projects',
                null,
                1200
            );
            
            ProjectImage::create([
                'project_id' => $project->id,
                'image_path' => $path,
                'alt_text' => $altTexts[$index] ?? $project->title,
                'is_featured' => $index === 0, // First image is featured by default
                'sort_order' => $index + 1,
            ]);
        }
    }
    
    /**
     * Update existing images
     *
     * @param Project $project
     * @param array $existingImages
     * @param array $existingAltTexts
     * @param int|null $featuredImageId
     * @return void
     */
    private function updateExistingImages(
        Project $project,
        array $existingImages,
        array $existingAltTexts,
        ?int $featuredImageId
    ): void {
        foreach ($project->images as $image) {
            if (!in_array($image->id, $existingImages)) {
                // Delete image file
                Storage::disk('public')->delete($image->image_path);
                
                // Delete image record
                $image->delete();
            } else {
                // Update image info
                $index = array_search($image->id, $existingImages);
                $image->update([
                    'alt_text' => $existingAltTexts[$index] ?? $project->title,
                    'is_featured' => $featuredImageId && $featuredImageId == $image->id,
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }
    
    /**
     * Process SEO data
     *
     * @param Project $project
     * @param array $data
     * @return void
     */
    private function processSeoData(Project $project, array $data): void
    {
        $project->updateSeo([
            'title' => $data['seo_title'] ?? null,
            'description' => $data['seo_description'] ?? null,
            'keywords' => $data['seo_keywords'] ?? null,
        ]);
    }
    
    /**
     * Toggle featured status
     *
     * @param Project $project
     * @return Project
     */
    public function toggleFeatured(Project $project): Project
    {
        $project->update([
            'featured' => !$project->featured
        ]);
        
        return $project;
    }
}