{{-- resources/views/components/admin/messages-table.blade.php --}}

@props([
    'messages',
    'showBulkActions' => true,
    'showProjectColumn' => true,
    'showClientColumn' => true,
    'actionRoutes' => [],
])

<div class="bg-white dark:bg-neutral-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    @if($showBulkActions)
                    {{-- Bulk Selection Column --}}
                    <th scope="col" class="px-6 py-3 text-left">
                        <input type="checkbox" id="select-all" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    </th>
                    @endif
                    
                    {{-- Status Column --}}
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Status
                    </th>
                    
                    {{-- Priority Column --}}
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Priority
                    </th>
                    
                    {{-- Client/Subject Column --}}
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        @if($showClientColumn) Client & @endif Subject
                    </th>
                    
                    {{-- Type Column --}}
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Type
                    </th>
                    
                    @if($showProjectColumn)
                    {{-- Project Column --}}
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Project
                    </th>
                    @endif
                    
                    {{-- Date Column --}}
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Date
                    </th>
                    
                    {{-- Actions Column --}}
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-neutral-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($messages as $message)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ !$message->is_read ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                        @if($showBulkActions)
                        {{-- Bulk Selection Checkbox --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="message_ids[]" value="{{ $message->id }}" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </td>
                        @endif
                        
                        {{-- Status Indicators --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-admin.message-status :message="$message" />
                        </td>
                        
                        {{-- Priority --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-admin.message-priority :priority="$message->priority" />
                        </td>
                        
                        {{-- Client & Subject --}}
                        <td class="px-6 py-4">
                            <x-admin.message-subject :message="$message" :showClient="$showClientColumn" />
                        </td>
                        
                        {{-- Type --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-admin.message-type :type="$message->type" />
                        </td>
                        
                        @if($showProjectColumn)
                        {{-- Project --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-admin.message-project :project="$message->project" />
                        </td>
                        @endif
                        
                        {{-- Date --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <x-admin.message-date :date="$message->created_at" />
                        </td>
                        
                        {{-- Actions --}}
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <x-admin.message-actions :message="$message" :routes="$actionRoutes" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $showBulkActions ? '8' : '7' }}" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <x-admin.empty-state 
                                icon="chat"
                                title="No messages found"
                                :description="request()->hasAny(['search', 'status', 'priority']) ? 'No messages match your current filters.' : 'No messages have been received yet.'"
                            >
                                @if(request()->hasAny(['search', 'status', 'priority']))
                                    <a href="{{ route('admin.messages.index') }}" 
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                        Clear Filters
                                    </a>
                                @endif
                                <a href="{{ route('admin.messages.create') }}" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 shadow-sm">
                                    Send Message
                                </a>
                            </x-admin.empty-state>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    @if($messages->hasPages())
        <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $messages->links() }}
        </div>
    @endif
</div>