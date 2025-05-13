<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectService
{
    /**
     * @var ProjectRepositoryInterface
     */
    protected $projectRepository;
    
    /**
     * @var FileUploadService
     */
    protected $fileUploadService;
    
    /**
     * ProjectService constructor.
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
     * Create a new project with images and SEO data
     *
     * @param array $data
     * @return Project
     */
    public function createProject(array $data)
    {
        DB::beginTransaction();
        
        try {
            // Extract images from data
            $images = $data['images'] ?? [];
            unset($data['images']);
            
            // Extract alt text from data
            $altTexts = $data['alt_text'] ?? [];
            unset($data['alt_text']);
            
            // Extract SEO data
            $seoData = [
                'title' => $data['seo_title'] ?? null,
                'description' => $data['seo_description'] ?? null,
                'keywords' => $data['seo_keywords'] ?? null,
            ];
            
            unset($data['seo_title']);
            unset($data['seo_description']);
            unset($data['seo_keywords']);
            
            // Create project
            $project = $this->projectRepository->create($data);
            
            // Upload and create images
            $this->processProjectImages($project, $images, $altTexts);
            
            // Create SEO data
            if (!empty(array_filter($seoData))) {
                $project->updateSeo($seoData);
            }
            
            DB::commit();
            
            return $project;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Update an existing project with images and SEO data
     *
     * @param Project $project
     * @param array $data
     * @return Project
     */
    public function updateProject(Project $project, array $data)
    {
        DB::beginTransaction();
        
        try {
            // Extract new images from data
            $newImages = $data['new_images'] ?? [];
            unset($data['new_images']);
            
            // Extract new alt text from data
            $newAltTexts = $data['new_alt_text'] ?? [];
            unset($data['new_alt_text']);
            
            // Extract existing images data
            $existingImages = $data['existing_images'] ?? [];
            unset($data['existing_images']);
            
            // Extract existing alt text data
            $existingAltTexts = $data['existing_alt_text'] ?? [];
            unset($data['existing_alt_text']);
            
            // Extract featured image
            $featuredImage = $data['featured_image'] ?? null;
            unset($data['featured_image']);
            
            // Extract SEO data
            $seoData = [
                'title' => $data['seo_title'] ?? null,
                'description' => $data['seo_description'] ?? null,
                'keywords' => $data['seo_keywords'] ?? null,
            ];
            
            unset($data['seo_title']);
            unset($data['seo_description']);
            unset($data['seo_keywords']);
            
            // Update project
            $project = $this->projectRepository->update($project->id, $data);
            
            // Process existing images
            $this->processExistingImages($project, $existingImages, $existingAltTexts, $featuredImage);
            
            // Process new images
            if (!empty($newImages)) {
                $this->processProjectImages($project, $newImages, $newAltTexts, count($project->images));
            }
            
            // Update SEO data
            $project->updateSeo($seoData);
            
            DB::commit();
            
            return $project;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Process project images
     *
     * @param Project $project
     * @param array $images
     * @param array $altTexts
     * @param int $startOrder
     * @return void
     */
    protected function processProjectImages(Project $project, array $images, array $altTexts = [], $startOrder = 0)
    {
        foreach ($images as $index => $image) {
            // Upload image
            $path = $this->fileUploadService->uploadImage(
                $image,
                'projects',
                null,
                1200 // Max width
            );
            
            // Create thumbnail
            $thumbPath = $this->fileUploadService->createThumbnail(
                $image,
                'projects/thumbnails',
                null,
                300,
                300
            );
            
            // Create image record
            ProjectImage::create([
                'project_id' => $project->id,
                'image_path' => $path,
                'thumbnail_path' => $thumbPath,
                'alt_text' => $altTexts[$index] ?? $project->title,
                'is_featured' => $index === 0 && $startOrder === 0, // First image is featured if no other images
                'sort_order' => $startOrder + $index + 1,
            ]);
        }
    }
    
    /**
     * Process existing images
     *
     * @param Project $project
     * @param array $existingImages
     * @param array $existingAltTexts
     * @param int|null $featuredImageId
     * @return void
     */
    protected function processExistingImages(Project $project, array $existingImages, array $existingAltTexts, $featuredImageId = null)
    {
        // Get all current project images
        $currentImages = $project->images;
        
        // Delete images not in the list
        foreach ($currentImages as $image) {
            if (!in_array($image->id, $existingImages)) {
                // Delete files
                Storage::disk('public')->delete($image->image_path);
                
                if ($image->thumbnail_path) {
                    Storage::disk('public')->delete($image->thumbnail_path);
                }
                
                // Delete record
                $image->delete();
            }
        }
        
        // Update existing images
        foreach ($existingImages as $index => $imageId) {
            $image = ProjectImage::find($imageId);
            
            if ($image) {
                $image->update([
                    'alt_text' => $existingAltTexts[$index] ?? $project->title,
                    'is_featured' => $featuredImageId ? $image->id == $featuredImageId : ($index === 0),
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }
    
    /**
     * Delete a project with all related data
     *
     * @param Project $project
     * @return bool
     */
    public function deleteProject(Project $project)
    {
        DB::beginTransaction();
        
        try {
            // Delete images
            foreach ($project->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                
                if ($image->thumbnail_path) {
                    Storage::disk('public')->delete($image->thumbnail_path);
                }
                
                $image->delete();
            }
            
            // Delete files
            foreach ($project->files as $file) {
                Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }
            
            // Delete the project (will automatically delete SEO data due to the relationship)
            $result = $this->projectRepository->delete($project->id);
            
            DB::commit();
            
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}