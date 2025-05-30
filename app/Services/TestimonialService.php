<?php

namespace App\Services;

use App\Models\Testimonial;
use App\Models\Project;
use App\Services\FileUploadService;
use App\Facades\Notifications;
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

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function createTestimonial(array $data, ?UploadedFile $image = null): Testimonial
    {
        // Set default status
        $data['status'] = $data['status'] ?? 'pending';

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

        // Send notification to admins about new testimonial
        Notifications::send('testimonial.created', $testimonial);

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

        // Send notification about update
        Notifications::send('testimonial.updated', $testimonial);

        return $testimonial;
    }

    public function deleteTestimonial(Testimonial $testimonial): bool
    {
        // Send notification before deletion
        Notifications::send('testimonial.deleted', $testimonial);

        if ($testimonial->image) {
            Storage::disk('public')->delete($testimonial->image);
        }

        return $testimonial->delete();
    }

    public function approveTestimonial(Testimonial $testimonial, ?string $adminNotes = null): Testimonial
    {
        $testimonial->update([
            'status' => 'approved',
            'is_active' => true,
            'admin_notes' => $adminNotes,
            'approval_notification_sent_at' => now(),
        ]);

        // Send approval notification to client if they exist
        if ($testimonial->project && $testimonial->project->client) {
            Notifications::send('testimonial.approved', $testimonial, $testimonial->project->client);
        }

        // Send notification to admins
        Notifications::send('testimonial.status_updated', $testimonial);

        return $testimonial;
    }

    public function rejectTestimonial(Testimonial $testimonial, string $reason): Testimonial
    {
        $testimonial->update([
            'status' => 'rejected',
            'is_active' => false,
            'admin_notes' => $reason,
        ]);

        // Send rejection notification to client if they exist
        if ($testimonial->project && $testimonial->project->client) {
            Notifications::send('testimonial.rejected', $testimonial, $testimonial->project->client);
        }

        // Send notification to admins
        Notifications::send('testimonial.status_updated', $testimonial);

        return $testimonial;
    }

    public function toggleActive(Testimonial $testimonial): Testimonial
    {
        $wasActive = $testimonial->is_active;
        $testimonial->update(['is_active' => !$testimonial->is_active]);

        // Send notification for status change
        $notificationType = $testimonial->is_active ? 'testimonial.activated' : 'testimonial.deactivated';
        Notifications::send($notificationType, $testimonial);

        return $testimonial;
    }

    public function toggleFeatured(Testimonial $testimonial): Testimonial
    {
        $wasFeatured = $testimonial->featured;
        $testimonial->update(['featured' => !$testimonial->featured]);

        // Send notification for featured status change
        if ($testimonial->featured && !$wasFeatured) {
            // Notify client if they exist
            if ($testimonial->project && $testimonial->project->client) {
                Notifications::send('testimonial.featured', $testimonial, $testimonial->project->client);
                $testimonial->update(['featured_notification_sent_at' => now()]);
            }
        }

        $notificationType = $testimonial->featured ? 'testimonial.featured' : 'testimonial.unfeatured';
        Notifications::send($notificationType, $testimonial);

        return $testimonial;
    }

    public function bulkApprove(array $testimonialIds): int
    {
        $testimonials = Testimonial::whereIn('id', $testimonialIds)
            ->where('status', 'pending')
            ->get();

        $approved = 0;
        foreach ($testimonials as $testimonial) {
            $this->approveTestimonial($testimonial);
            $approved++;
        }

        // Send bulk notification
        if ($approved > 0) {
            Notifications::send('testimonial.bulk_approved', [
                'count' => $approved,
                'message' => "{$approved} testimonial(s) have been approved"
            ]);
        }

        return $approved;
    }

    public function bulkReject(array $testimonialIds, string $reason): int
    {
        $testimonials = Testimonial::whereIn('id', $testimonialIds)
            ->where('status', 'pending')
            ->get();

        $rejected = 0;
        foreach ($testimonials as $testimonial) {
            $this->rejectTestimonial($testimonial, $reason);
            $rejected++;
        }

        // Send bulk notification
        if ($rejected > 0) {
            Notifications::send('testimonial.bulk_rejected', [
                'count' => $rejected,
                'reason' => $reason,
                'message' => "{$rejected} testimonial(s) have been rejected"
            ]);
        }

        return $rejected;
    }

    public function bulkToggleActive(array $testimonialIds, bool $active): int
    {
        $updated = Testimonial::whereIn('id', $testimonialIds)
            ->update(['is_active' => $active]);

        if ($updated > 0) {
            $notificationType = $active ? 'testimonial.bulk_activated' : 'testimonial.bulk_deactivated';
            Notifications::send($notificationType, [
                'count' => $updated,
                'status' => $active ? 'activated' : 'deactivated'
            ]);
        }

        return $updated;
    }

    public function bulkToggleFeatured(array $testimonialIds, bool $featured): int
    {
        $updated = Testimonial::whereIn('id', $testimonialIds)
            ->update(['featured' => $featured]);

        if ($updated > 0) {
            $notificationType = $featured ? 'testimonial.bulk_featured' : 'testimonial.bulk_unfeatured';
            Notifications::send($notificationType, [
                'count' => $updated,
                'status' => $featured ? 'featured' : 'unfeatured'
            ]);
        }

        return $updated;
    }

    public function requestTestimonial(Project $project): bool
    {
        // Check if testimonial already exists for this project
        if ($project->testimonial) {
            return false;
        }

        // Check if project is completed
        if ($project->status !== 'completed') {
            return false;
        }

        // Send testimonial request to client
        if ($project->client) {
            Notifications::send('testimonial.request', $project, $project->client);
            return true;
        }

        return false;
    }

    public function sendTestimonialReminder(Project $project): bool
    {
        // Check if we already sent a reminder recently
        if ($project->testimonial_reminder_sent_at && 
            $project->testimonial_reminder_sent_at->diffInDays(now()) < 7) {
            return false;
        }

        if ($project->client) {
            Notifications::send('testimonial.reminder', $project, $project->client);
            $project->update(['testimonial_reminder_sent_at' => now()]);
            return true;
        }

        return false;
    }

    public function getStatistics(): array
    {
        return [
            'total' => Testimonial::count(),
            'active' => Testimonial::where('is_active', true)->count(),
            'featured' => Testimonial::where('featured', true)->count(),
            'pending' => Testimonial::where('status', 'pending')->count(),
            'approved' => Testimonial::where('status', 'approved')->count(),
            'rejected' => Testimonial::where('status', 'rejected')->count(),
            'average_rating' => Testimonial::where('is_active', true)->avg('rating'),
            'by_rating' => Testimonial::selectRaw('rating, COUNT(*) as count')
                ->groupBy('rating')
                ->pluck('count', 'rating')
                ->toArray(),
            'this_month' => Testimonial::whereMonth('created_at', now()->month)->count(),
            'conversion_rate' => $this->calculateConversionRate(),
        ];
    }

    public function getFeaturedTestimonials(int $limit = 6): \Illuminate\Database\Eloquent\Collection
    {
        return Testimonial::where('is_active', true)
            ->where('featured', true)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTestimonialsByRating(int $rating, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Testimonial::where('is_active', true)
            ->where('status', 'approved')
            ->where('rating', $rating)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getRandomTestimonials(int $count = 3): \Illuminate\Database\Eloquent\Collection
    {
        return Testimonial::where('is_active', true)
            ->where('status', 'approved')
            ->inRandomOrder()
            ->limit($count)
            ->get();
    }

    protected function calculateConversionRate(): float
    {
        $completedProjects = Project::where('status', 'completed')->count();
        if ($completedProjects === 0) return 0;

        $testimonialsCount = Testimonial::count();
        return round(($testimonialsCount / $completedProjects) * 100, 1);
    }
}