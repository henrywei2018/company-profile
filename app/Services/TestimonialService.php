<?php
// File: app/Services/TestimonialService.php

namespace App\Services;

use App\Models\Testimonial;
use App\Models\Project;
use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class TestimonialService
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function getFilteredTestimonials(array $filters = [], int $perPage = 15)
    {
        $query = Testimonial::with(['project', 'project.client']);

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('client_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('client_company', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('content', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['rating'])) {
            $query->where('rating', $filters['rating']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['featured'])) {
            $query->where('featured', $filters['featured']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function createTestimonial(array $data, ?UploadedFile $image = null): Testimonial
    {
        $testimonial = Testimonial::create($data);

        if ($image) {
            $path = $this->fileUploadService->uploadImage(
                $image,
                'testimonials',
                null,
                400,
                400
            );
            $testimonial->update(['image' => $path]);
        }

        return $testimonial;
    }

    public function updateTestimonial(Testimonial $testimonial, array $data, ?UploadedFile $image = null): Testimonial
    {
        if ($image) {
            // Delete old image
            if ($testimonial->image) {
                Storage::disk('public')->delete($testimonial->image);
            }

            $path = $this->fileUploadService->uploadImage(
                $image,
                'testimonials',
                null,
                400,
                400
            );
            $data['image'] = $path;
        }

        $testimonial->update($data);
        return $testimonial;
    }

    public function deleteTestimonial(Testimonial $testimonial): bool
    {
        if ($testimonial->image) {
            Storage::disk('public')->delete($testimonial->image);
        }

        return $testimonial->delete();
    }

    public function toggleActive(Testimonial $testimonial): Testimonial
    {
        $testimonial->update(['is_active' => !$testimonial->is_active]);
        return $testimonial;
    }

    public function toggleFeatured(Testimonial $testimonial): Testimonial
    {
        $testimonial->update(['featured' => !$testimonial->featured]);
        return $testimonial;
    }

    public function getStatistics(): array
    {
        return [
            'total' => Testimonial::count(),
            'active' => Testimonial::where('is_active', true)->count(),
            'featured' => Testimonial::where('featured', true)->count(),
            'average_rating' => Testimonial::where('is_active', true)->avg('rating'),
            'by_rating' => Testimonial::selectRaw('rating, COUNT(*) as count')
                ->groupBy('rating')
                ->pluck('count', 'rating')
                ->toArray(),
        ];
    }
}