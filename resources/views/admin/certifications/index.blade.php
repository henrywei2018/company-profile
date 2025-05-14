<!-- resources/views/admin/certifications/index.blade.php -->
<x-admin-layout :title="'Certifications'">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <h2 class="text-xl font-semibold text-gray-900">Manage Certifications</h2>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.certifications.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Certification
            </a>
        </div>
    </div>

    <!-- Certifications Grid -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-6">
            @if($certifications->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($certifications as $certification)
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden flex flex-col">
                            <!-- Certificate Image -->
                            <div class="relative h-48 bg-gray-100 flex items-center justify-center">
                                @if($certification->image)
                                    <img src="{{ asset('storage/' . $certification->image) }}" alt="{{ $certification->name }}" class="h-full w-full object-contain p-4">
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                @endif
                                
                                <!-- Display Status Badge -->
                                <div class="absolute top-2 right-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $certification->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $certification->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Certificate Info -->
                            <div class="p-4 flex-grow">
                                <h3 class="text-lg font-medium text-gray-900">{{ $certification->name }}</h3>
                                
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ Str::limit($certification->description, 100) }}
                                </p>
                                
                                <div class="mt-2 flex flex-wrap gap-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $certification->issuer }}
                                    </span>
                                    
                                    @if($certification->expiry_date)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ \Carbon\Carbon::parse($certification->expiry_date)->isPast() ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            Expires: {{ \Carbon\Carbon::parse($certification->expiry_date)->format('M d, Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="px-4 py-3 bg-gray-50 text-right">
                                <div class="flex justify-end space-x-3">
                                    <a href="{{ route('admin.certifications.edit', $certification->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    
                                    @if($certification->certificate_file)
                                        <a href="{{ route('admin.certifications.download', $certification->id) }}" class="text-green-600 hover:text-green-900" title="Download Certificate">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </a>
                                    @endif
                                    
                                    <form action="{{ route('admin.certifications.toggle-status', $certification->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="{{ $certification->is_active ? 'text-gray-400 hover:text-gray-600' : 'text-green-600 hover:text-green-900' }}" title="{{ $certification->is_active ? 'Deactivate' : 'Activate' }}">
                                            @if($certification->is_active)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('admin.certifications.destroy', $certification->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this certification?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $certifications->links('components.pagination') }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No certifications found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new certification.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.certifications.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Certification
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Certification Stats -->
    <div class="mt-8 bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Certification Overview</h3>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Total Certifications
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ $totalCertifications ?? $certifications->total() }}
                        </dd>
                    </div>
                </div>

                <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Active Certifications
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-green-600">
                            {{ $activeCertifications ?? 0 }}
                        </dd>
                    </div>
                </div>

                <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Expiring Soon (30 Days)
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-yellow-600">
                            {{ $expiringSoonCertifications ?? 0 }}
                        </dd>
                    </div>
                </div>

                <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Expired Certifications
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-red-600">
                            {{ $expiredCertifications ?? 0 }}
                        </dd>
                    </div>
                </div>
            </dl>
        </div>
    </div>
</x-admin-layout>