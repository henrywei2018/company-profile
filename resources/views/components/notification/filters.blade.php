@props([
    'variant' => 'default',
    'filters' => []
])

<div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
    <form method="GET" class="space-y-4 sm:space-y-0 sm:grid sm:grid-cols-2 lg:grid-cols-4 sm:gap-4">
        <!-- Read Status Filter -->
        <div>
            <label for="read_status" class="block text-sm font-medium text-gray-700 dark:text-neutral-300">Status</label>
            <select 
                id="read_status" 
                name="read_status" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white sm:text-sm"
            >
                <option value="">All notifications</option>
                <option value="unread" {{ (request('read_status') === 'unread') ? 'selected' : '' }}>Unread only</option>
                <option value="read" {{ (request('read_status') === 'read') ? 'selected' : '' }}>Read only</option>
            </select>
        </div>

        <!-- Type Filter -->
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-neutral-300">Type</label>
            <select 
                id="type" 
                name="type" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white sm:text-sm"
            >
                <option value="">All types</option>
                <option value="project" {{ str_contains(request('type', ''), 'project') ? 'selected' : '' }}>Projects</option>
                <option value="quotation" {{ str_contains(request('type', ''), 'quotation') ? 'selected' : '' }}>Quotations</option>
                <option value="message" {{ str_contains(request('type', ''), 'message') ? 'selected' : '' }}>Messages</option>
                <option value="chat" {{ str_contains(request('type', ''), 'chat') ? 'selected' : '' }}>Chat</option>
                <option value="system" {{ str_contains(request('type', ''), 'system') ? 'selected' : '' }}>System</option>
            </select>
        </div>

        <!-- Search -->
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-neutral-300">Search</label>
            <input 
                type="text" 
                id="search" 
                name="search" 
                value="{{ request('search') }}"
                placeholder="Search notifications..."
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white sm:text-sm"
            >
        </div>

        <!-- Actions -->
        <div class="flex items-end space-x-2">
            <button 
                type="submit" 
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                Filter
            </button>
            <a 
                href="{{ request()->url() }}" 
                class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-700"
            >
                Reset
            </a>
        </div>
    </form>
</div>