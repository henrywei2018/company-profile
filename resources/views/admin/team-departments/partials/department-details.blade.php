<!-- resources/views/admin/team-departments/partials/department-details.blade.php -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-2">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">{{ $teamMemberDepartment->name }}</h3>
        
        @if($teamMemberDepartment->description)
            <div class="prose max-w-none dark:prose-invert mb-6">
                <p>{{ $teamMemberDepartment->description }}</p>
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400 mb-6">No description available.</p>
        @endif

        @if($teamMemberDepartment->teamMembers->count() > 0)
            <h4 class="text-md font-medium text-gray-800 dark:text-white mb-3">Team Members</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($teamMemberDepartment->teamMembers as $member)
                    <div class="flex p-3 rounded-lg border border-gray-200 dark:border-neutral-700">
                        <div class="flex-shrink-0 mr-3">
                            @if($member->image)
                                <img src="{{ asset('storage/' . $member->image) }}" alt="{{ $member->name }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-neutral-700 flex items-center justify-center text-gray-700 dark:text-gray-300 font-semibold text-lg">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-900 dark:text-white">{{ $member->name }}</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $member->position }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-4 text-center">
                <p class="text-gray-500 dark:text-gray-400">No team members in this department.</p>
            </div>
        @endif
    </div>
    
    <div>
        <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-800 dark:text-white mb-3">Department Information</h4>
            <ul class="divide-y divide-gray-200 dark:divide-neutral-700">
                <li class="py-2 first:pt-0 flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Status</span>
                    <span>
                        @if($teamMemberDepartment->is_active)
                            <x-admin.badge type="success" dot="true">Active</x-admin.badge>
                        @else
                            <x-admin.badge type="danger" dot="true">Inactive</x-admin.badge>
                        @endif
                    </span>
                </li>
                <li class="py-2 flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Team Members</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $teamMemberDepartment->teamMembers->count() }}</span>
                </li>
                <li class="py-2 flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Sort Order</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $teamMemberDepartment->sort_order ?: 'Default' }}</span>
                </li>
                <li class="py-2 flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Created</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $teamMemberDepartment->created_at->format('M d, Y') }}</span>
                </li>
                <li class="py-2 last:pb-0 flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Last Updated</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $teamMemberDepartment->updated_at->format('M d, Y') }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>