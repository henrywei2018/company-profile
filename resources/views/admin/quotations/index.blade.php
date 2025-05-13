<!-- resources/views/admin/quotations/index.blade.php -->
<x-admin-layout :title="'Quotations'">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <h2 class="text-xl font-semibold text-gray-900">Manage Quotation Requests</h2>
        <div class="mt-4 md:mt-0 flex flex-col md:flex-row gap-3">
            <form action="{{ route('admin.quotations.index') }}" method="GET" class="flex items-center space-x-2">
                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        placeholder="Search quotations..."
                        value="{{ request()->get('search') }}"
                        class="w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <button type="submit" class="text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <select
                    name="status"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    onchange="this.form.submit()"
                >
                    <option value="">All Status</option>
                    <option value="pending" {{ request()->get('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request()->get('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request()->get('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <select
                    name="date_range"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    onchange="this.form.submit()"
                >
                    <option value="">All Time</option>
                    <option value="today" {{ request()->get('date_range') === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request()->get('date_range') === 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request()->get('date_range') === 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="quarter" {{ request()->get('date_range') === 'quarter' ? 'selected' : '' }}>This Quarter</option>
                    <option value="year" {{ request()->get('date_range') === 'year' ? 'selected' : '' }}>This Year</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Quotations Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($quotations->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Client
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Project Type
                        </th>
                        <th scope="col" class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($quotations as $quotation)
                        <tr class="{{ $quotation->status === 'pending' ? 'bg-yellow-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $quotation->name }}
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $quotation->email }}
                                    @if($quotation->phone)
                                        <span class="hidden md:inline"> • {{ $quotation->phone }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 truncate max-w-xs">
                                    {{ $quotation->project_type ?? 'General Inquiry' }}
                                </div>
                                <div class="text-sm text-gray-500 truncate max-w-xs">
                                    {{ $quotation->location ?? 'Location not specified' }}
                                </div>
                            </td>
                            <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $quotation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($quotation->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                       'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($quotation->status) }}
                                </span>
                                @if($quotation->status === 'approved' && $quotation->client_approved)
                                    <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Client Approved
                                    </span>
                                @endif
                            </td>
                            <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $quotation->created_at->format('M d, Y') }}
                                <p class="text-xs text-gray-400">{{ $quotation->created_at->format('H:i') }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.quotations.show', $quotation->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        View
                                    </a>
                                    
                                    @if($quotation->status === 'pending')
                                        <form action="{{ route('admin.quotations.approve', $quotation->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="text-green-600 hover:text-green-900">
                                                Approve
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('admin.quotations.decline', $quotation->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                Decline
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form action="{{ route('admin.quotations.destroy', $quotation->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this quotation?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4 border-t border-gray-200">
                {{ $quotations->links('components.pagination') }}
            </div>
        @else
            <div class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No quotation requests found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    {{ request()->has('search') || request()->has('status') || request()->has('date_range') 
                        ? 'Try adjusting your search or filter criteria.' 
                        : 'Quotation requests from your website will appear here.' }}
                </p>
                @if(request()->anyFilled(['search', 'status', 'date_range']))
                    <div class="mt-6">
                        <a href="{{ route('admin.quotations.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Clear Filters
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Quotation Statistics -->
    <div class="mt-8 bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Quotation Overview</h3>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Total Quotations
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ $totalQuotations ?? $quotations->total() }}
                        </dd>
                    </div>
                </div>

                <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Pending Quotations
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-yellow-600">
                            {{ $pendingQuotations ?? 0 }}
                        </dd>
                    </div>
                </div>

                <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Approved Quotations
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-green-600">
                            {{ $approvedQuotations ?? 0 }}
                        </dd>
                    </div>
                </div>

                <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Declined Quotations
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-red-600">
                            {{ $declinedQuotations ?? 0 }}
                        </dd>
                    </div>
                </div>
            </dl>
        </div>
    </div>
</x-admin-layout>