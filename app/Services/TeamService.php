<?php

namespace App\Services;

use App\Models\TeamMember;
use App\Models\TeamMemberDepartment;
use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeamService
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function getFilteredTeamMembers(array $filters = [], int $perPage = 15)
    {
        $query = TeamMember::with('department');

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('position', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['featured'])) {
            $query->where('featured', $filters['featured']);
        }

        if (!empty($filters['department'])) {
            $query->where('team_member_department_id', $filters['department']);
        }

        return $query->ordered()->paginate($perPage);
    }

    public function createTeamMember(array $data, ?UploadedFile $photo = null): TeamMember
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $teamMember = TeamMember::create($data);

        if ($photo) {
            $path = $this->fileUploadService->uploadImage(
                $photo,
                'team/photos',
                null,
                600,
                600
            );
            $teamMember->update(['photo' => $path]);
        }

        return $teamMember;
    }

    public function updateTeamMember(TeamMember $teamMember, array $data, ?UploadedFile $photo = null): TeamMember
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($photo) {
            if ($teamMember->photo) {
                Storage::disk('public')->delete($teamMember->photo);
            }

            $path = $this->fileUploadService->uploadImage(
                $photo,
                'team/photos',
                null,
                600,
                600
            );
            $data['photo'] = $path;
        }

        $teamMember->update($data);
        return $teamMember;
    }

    public function deleteTeamMember(TeamMember $teamMember): bool
    {
        if ($teamMember->photo) {
            Storage::disk('public')->delete($teamMember->photo);
        }

        return $teamMember->delete();
    }

    public function toggleActive(TeamMember $teamMember): TeamMember
    {
        $teamMember->update(['is_active' => !$teamMember->is_active]);
        return $teamMember;
    }

    public function toggleFeatured(TeamMember $teamMember): TeamMember
    {
        $teamMember->update(['featured' => !$teamMember->featured]);
        return $teamMember;
    }

    public function updateOrder(array $order): bool
    {
        foreach ($order as $index => $id) {
            TeamMember::where('id', $id)->update(['sort_order' => $index + 1]);
        }
        return true;
    }

    public function getStatistics(): array
    {
        return [
            'total' => TeamMember::count(),
            'active' => TeamMember::where('is_active', true)->count(),
            'featured' => TeamMember::where('featured', true)->count(),
            'by_department' => TeamMember::join('team_member_departments', 'team_members.team_member_department_id', '=', 'team_member_departments.id')
                ->selectRaw('team_member_departments.name, COUNT(*) as count')
                ->groupBy('team_member_departments.name')
                ->pluck('count', 'name')
                ->toArray(),
        ];
    }
}