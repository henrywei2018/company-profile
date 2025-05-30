<?php

namespace App\Services;

use App\Models\Certification;
use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CertificationService
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function getFilteredCertifications(array $filters = [], int $perPage = 15)
    {
        $query = Certification::query();

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('issuer', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['valid'])) {
            if ($filters['valid'] === 'valid') {
                $query->valid();
            } else {
                $query->expired();
            }
        }

        return $query->ordered()->paginate($perPage);
    }

    public function createCertification(array $data, ?UploadedFile $image = null): Certification
    {
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = Certification::max('sort_order') + 1;
        }

        $certification = Certification::create($data);

        if ($image) {
            $path = $this->fileUploadService->uploadImage(
                $image,
                'certifications',
                null,
                800
            );
            $certification->update(['image' => $path]);
        }

        return $certification;
    }

    public function updateCertification(Certification $certification, array $data, ?UploadedFile $image = null): Certification
    {
        if ($image) {
            if ($certification->image) {
                Storage::disk('public')->delete($certification->image);
            }

            $path = $this->fileUploadService->uploadImage(
                $image,
                'certifications',
                null,
                800
            );
            $data['image'] = $path;
        }

        $certification->update($data);
        return $certification;
    }

    public function deleteCertification(Certification $certification): bool
    {
        if ($certification->image) {
            Storage::disk('public')->delete($certification->image);
        }

        return $certification->delete();
    }

    public function toggleActive(Certification $certification): Certification
    {
        $certification->update(['is_active' => !$certification->is_active]);
        return $certification;
    }

    public function updateOrder(array $order): bool
    {
        foreach ($order as $index => $id) {
            Certification::where('id', $id)->update(['sort_order' => $index + 1]);
        }
        return true;
    }

    public function getExpiringCertifications(int $days = 30)
    {
        return Certification::where('is_active', true)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays($days))
            ->get();
    }

    public function getStatistics(): array
    {
        return [
            'total' => Certification::count(),
            'active' => Certification::where('is_active', true)->count(),
            'valid' => Certification::valid()->count(),
            'expired' => Certification::expired()->count(),
            'expiring_soon' => $this->getExpiringCertifications(30)->count(),
        ];
    }
}