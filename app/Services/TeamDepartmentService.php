<?php

namespace App\Services;

use App\Models\TeamMemberDepartment;
use App\Facades\Notifications;
use Illuminate\Support\Str;

class TeamDepartmentService
{
    public function getFilteredDepartments(array $filters = [], int $perPage = 15)
    {
        $query = TeamMemberDepartment::withCount('teamMembers');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->ordered()->paginate($perPage);
    }

    public function createDepartment(array $data): TeamMemberDepartment
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = TeamMemberDepartment::max('sort_order') + 1;
        }

        $department = TeamMemberDepartment::create($data);

        // Send notification
        Notifications::send('team_department.created', $department);

        return $department;
    }

    public function updateDepartment(TeamMemberDepartment $department, array $data): TeamMemberDepartment
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $department->update($data);

        // Send notification
        Notifications::send('team_department.updated', $department);

        return $department;
    }

    public function deleteDepartment(TeamMemberDepartment $department): bool
    {
        if ($department->teamMembers()->count() > 0) {
            throw new \Exception('Cannot delete department with team members');
        }

        // Send notification before deletion
        Notifications::send('team_department.deleted', $department);

        return $department->delete();
    }

    public function toggleActive(TeamMemberDepartment $department): TeamMemberDepartment
    {
        $wasActive = $department->is_active;
        $department->update(['is_active' => !$department->is_active]);

        // Send notification for status change
        $notificationType = $department->is_active ? 'team_department.activated' : 'team_department.deactivated';
        Notifications::send($notificationType, $department);

        return $department;
    }

    public function updateOrder(array $order): bool
    {
        foreach ($order as $index => $id) {
            TeamMemberDepartment::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        // Send notification about reordering
        Notifications::send('team_department.reordered', [
            'message' => 'Team departments have been reordered',
            'count' => count($order)
        ]);

        return true;
    }

    public function getStatistics(): array
    {
        return [
            'total' => TeamMemberDepartment::count(),
            'active' => TeamMemberDepartment::where('is_active', true)->count(),
            'with_members' => TeamMemberDepartment::has('teamMembers')->count(),
            'empty' => TeamMemberDepartment::doesntHave('teamMembers')->count(),
            'largest_department' => TeamMemberDepartment::withCount('teamMembers')
                ->orderBy('team_members_count', 'desc')
                ->first(),
        ];
    }

    public function bulkToggleActive(array $departmentIds, bool $active): int
    {
        $updated = TeamMemberDepartment::whereIn('id', $departmentIds)
            ->update(['is_active' => $active]);

        if ($updated > 0) {
            $notificationType = $active ? 'team_department.bulk_activated' : 'team_department.bulk_deactivated';
            Notifications::send($notificationType, [
                'count' => $updated,
                'status' => $active ? 'activated' : 'deactivated'
            ]);
        }

        return $updated;
    }

    public function bulkDelete(array $departmentIds): int
    {
        $departments = TeamMemberDepartment::whereIn('id', $departmentIds)
            ->doesntHave('teamMembers')
            ->get();

        $deleted = $departments->count();
        
        if ($deleted > 0) {
            TeamMemberDepartment::whereIn('id', $departments->pluck('id'))->delete();
            
            Notifications::send('team_department.bulk_deleted', [
                'count' => $deleted
            ]);
        }

        return $deleted;
    }
}