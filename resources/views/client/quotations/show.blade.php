{{-- resources/views/client/quotations/show.blade.php --}}
<x-layouts.client>
    <x-slot name="title">{{ $quotation->project_type }} - Detail Penawaran</x-slot>
    <x-slot name="description">View detailed information about your quotation request.</x-slot>

    <!-- Modern Header Section -->
    <div class="mb-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="{{ route('client.quotations.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">Penawaran Saya</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-600 dark:text-gray-300">{{ Str::limit($quotation->project_type, 25) }}</li>
            </ol>
        </nav>

        <!-- Enhanced Header with Status Timeline -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <!-- Left Section - Project Info -->
                <div class="flex-1">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                            {{ strtoupper(substr($quotation->project_type, 0, 2)) }}
                        </div>
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                                {{ $quotation->project_type }}
                            </h1>
                            <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                    </svg>
                                    #{{ $quotation->quotation_number }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v2m6-6v6m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Created {{ $quotation->created_at->format('M j, Y') }}
                                </span>
                                @if($quotation->location)
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $quotation->location }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Section - Status & Aksi -->
                <div class="flex items-center gap-4">
                    <!-- Status Badge with Enhanced Design -->
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium
                            {{ $quotation->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                            {{ $quotation->status === 'reviewed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                            {{ $quotation->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                            {{ $quotation->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}">
                            <div class="w-2 h-2 rounded-full mr-2
                                {{ $quotation->status === 'pending' ? 'bg-yellow-400' : '' }}
                                {{ $quotation->status === 'reviewed' ? 'bg-blue-400' : '' }}
                                {{ $quotation->status === 'approved' ? 'bg-green-400' : '' }}
                                {{ $quotation->status === 'rejected' ? 'bg-red-400' : '' }}">
                            </div>
                            {{ ucfirst($quotation->status) }}
                        </span>
                    </div>

                    <!-- Aksi Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium 
                                       text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 
                                       transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Aksi
                            <svg class="w-4 h-4 ml-2 transition-transform" :class="open ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-20">
                            <div class="py-1">
                                @if(in_array($quotation->status, ['pending', 'reviewed']))
                                    <a href="{{ route('client.quotations.edit', $quotation) }}" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Ubah Penawaran
                                    </a>
                                @endif

                                @if(in_array($quotation->status, ['pending', 'reviewed']))
                                    <div class="border-t border-gray-100 dark:border-gray-600 my-1"></div>
                                    <form method="POST" action="{{ route('client.quotations.cancel', $quotation) }}" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin membatalkan penawaran ini?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Batal Request
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Progress Timeline</h3>
                <div class="flex items-center justify-between relative">
                    <!-- Progress Line -->
                    <div class="absolute top-4 left-0 right-0 h-0.5 bg-gray-200 dark:bg-gray-600"></div>
                    <div class="absolute top-4 left-0 h-0.5 bg-blue-500 transition-all duration-500" 
                         style="width: {{ $quotation->status === 'pending' ? '25%' : ($quotation->status === 'reviewed' ? '50%' : ($quotation->status === 'approved' ? '100%' : '75%')) }}"></div>
                    
                    <!-- Timeline Steps -->
                    <div class="relative flex items-center justify-center w-8 h-8 rounded-full {{ in_array($quotation->status, ['pending', 'reviewed', 'approved', 'rejected']) ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-400' }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="relative flex items-center justify-center w-8 h-8 rounded-full {{ in_array($quotation->status, ['reviewed', 'approved', 'rejected']) ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-400' }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="relative flex items-center justify-center w-8 h-8 rounded-full {{ $quotation->status === 'approved' ? 'bg-green-500 text-white' : ($quotation->status === 'rejected' ? 'bg-red-500 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-400') }}">
                        @if($quotation->status === 'approved')
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @elseif($quotation->status === 'rejected')
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" />
                            </svg>
                        @else
                            <div class="w-2 h-2 bg-current rounded-full"></div>
                        @endif
                    </div>
                </div>
                <div class="flex items-center justify-between mt-2 text-xs text-gray-500 dark:text-gray-400">
                    <span>Kirimted</span>
                    <span>Under Review</span>
                    <span>{{ $quotation->status === 'approved' ? 'Approved' : ($quotation->status === 'rejected' ? 'Rejected' : 'Decision') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Project Informasi Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Detail Penawaran
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Project Type</label>
                            <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $quotation->project_type }}</p>
                        </div>

                        @if($quotation->service)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Service Category</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ $quotation->service->title }}
                                </span>
                            </div>
                        @endif

                        @if($quotation->budget)
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Project Budget</label>
        <div class="flex items-center">
            <span class="text-lg font-semibold text-gray-900 dark:text-white">
                Rp.{{ number_format($quotation->budget, 0, ',', '.') }}
            </span>
            @if($quotation->budget >= 50000000)
                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                    Sanggat Tinggi
                </span>
            @elseif($quotation->budget >= 10000000)
                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    Tinggi
                </span>
            @elseif($quotation->budget >= 5000000)
                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                    Menengah
                </span>
            @else
                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                    Rendah
                </span>
            @endif
        </div>
        
        {{-- Budget breakdown or additional info can go here --}}
        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            @if($quotation->budget >= 10000000)
                Termasuk konsultasi premium dan manajer proyek khusus
            @else
                Paket proyek standar dengan pembaruan rutin
            @endif
        </div>
    </div>
@endif

                        @if($quotation->start_date)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expected Start Date</label>
                                <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $quotation->start_date->format('F j, Y') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Requirements Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Informasi Tambahan
                    </h2>
                    
                    <div class="prose max-w-none text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        {!! nl2br(e($quotation->requirements)) !!}
                    </div>
                </div>
            </div>

            <!-- Attachments Card -->
            @if($quotation->attachments && $quotation->attachments->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            File Penawaran
                            <span class="ml-2 px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full">
                                {{ $quotation->attachments->count() }}
                            </span>
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($quotation->attachments as $attachment)
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">
                                    <div class="flex items-center space-x-3">
                                        <!-- File Icon -->
                                        <div class="flex-shrink-0 w-10 h-10 {{ $attachment->file_icon ?? 'bg-gray-100 dark:bg-gray-600' }} rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        
                                        <!-- File Info -->
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $attachment->file_name }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $attachment->formatted_file_size }}
                                            </p>
                                        </div>
                                        
                                        <!-- Download Button -->
                                        <a href="{{ route('client.quotations.download-attachment', [$quotation, $attachment]) }}" 
                                           class="opacity-0 group-hover:opacity-100 transition-opacity p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Admin Response Section (if exists) -->
            @if($quotation->admin_notes || $quotation->estimated_cost || $quotation->estimated_timeline)
                <div class="bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-800">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-green-900 dark:text-green-400 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.027 8.027 0 01-2.872-.513l-3.705 1.225a.25.25 0 01-.315-.315l1.225-3.705A8.027 8.027 0 014 12C4 7.582 7.582 4 12 4s8 3.582 8 8z" />
                            </svg>
                            Our Response
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($quotation->estimated_cost)
                                <div>
                                    <label class="block text-sm font-medium text-green-700 dark:text-green-300 mb-1">Estimated Cost</label>
                                    <p class="text-lg font-semibold text-green-900 dark:text-green-400">{{ $quotation->estimated_cost }}</p>
                                </div>
                            @endif

                            @if($quotation->estimated_timeline)
                                <div>
                                    <label class="block text-sm font-medium text-green-700 dark:text-green-300 mb-1">Estimated Timeline</label>
                                    <p class="text-lg font-semibold text-green-900 dark:text-green-400">{{ $quotation->estimated_timeline }}</p>
                                </div>
                            @endif
                        </div>

                        @if($quotation->admin_notes)
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-green-700 dark:text-green-300 mb-2">Additional Notes</label>
                                <div class="text-sm text-green-800 dark:text-green-300 leading-relaxed">
                                    {!! nl2br(e($quotation->admin_notes)) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
            
            <!-- Contact Informasi Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Informasi Kontak
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Nama Lengkap</label>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $quotation->name }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Email</label>
                            <p class="text-sm text-gray-900 dark:text-white">
                                <a href="mailto:{{ $quotation->email }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $quotation->email }}
                                </a>
                            </p>
                        </div>

                        @if($quotation->phone)
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Telepon</label>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    <a href="tel:{{ $quotation->phone }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ $quotation->phone }}
                                    </a>
                                </p>
                            </div>
                        @endif

                        @if($quotation->company)
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Perusahaan</label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $quotation->company }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Stats Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Statistik Singkat
                    </h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Prioritas</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $quotation->priority === 'urgent' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                {{ $quotation->priority === 'high' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400' : '' }}
                                {{ $quotation->priority === 'normal' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                {{ $quotation->priority === 'low' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400' : '' }}">
                                {{ ucfirst($quotation->priority ?? 'normal') }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Hari sejak dikirim</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $quotation->created_at->diffInDays(now()) }}
                            </span>
                        </div>

                        @if($quotation->attachments->count() > 0)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Lampiran</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $quotation->attachments->count() }} files
                                </span>
                            </div>
                        @endif

                        @if($quotation->source)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Sumber</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                    {{ ucfirst(str_replace('_', ' ', $quotation->source)) }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Selanjutnya Steps Card -->
            

            <!-- Help & Support Card -->
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Butuh Bantuan?
                    </h3>

                    <div class="space-y-3">
                        <a href="mailto:{{ config('mail.from.address', 'support@company.com') }}" 
                           class="flex items-center p-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-800 rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Email Bantuan
                        </a>

                        <a href="tel:+1234567890" 
                           class="flex items-center p-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-800 rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            Telepon Bantuan
                        </a>

                        <div class="pt-3 border-t border-gray-200 dark:border-gray-600">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                📞 Jam Kerja: Senin - Jummat, 9 AM - 6 PM
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Add smooth scrolling and enhanced interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scroll to sections
            const links = document.querySelectorAll('a[href^="#"]');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });

            // Add loading states to action buttons
            const actionButtons = document.querySelectorAll('form button[type="submit"]');
            actionButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('form');
                    if (form) {
                        this.disabled = true;
                        this.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...';
                        form.submit();
                    }
                });
            });

            // Add copy to clipboard functionality for quotation number
            const quotationNumber = document.querySelector('#quotation-number');
            if (quotationNumber) {
                quotationNumber.addEventListener('click', function() {
                    navigator.clipboard.writeText(this.textContent).then(() => {
                        // Show toast notification
                        showToast('Quotation number copied to clipboard!', 'success');
                    });
                });
            }

            // Enhanced file download tracking
            const downloadLinks = document.querySelectorAll('a[href*="download-attachment"]');
            downloadLinks.forEach(link => {
                link.addEventListener('click', function() {
                    // Track download analytics if needed
                    console.log('File download:', this.href);
                });
            });
        });

        // Toast notification function
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm w-full transform transition-all duration-300 ease-in-out ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            
            toast.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        ${type === 'success' 
                            ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
                            : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                        }
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            // Add entrance animation
            toast.style.transform = 'translateX(100%)';
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 10);
            
            // Auto remove after 4 seconds
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (toast.parentElement) {
                            toast.remove();
                        }
                    }, 300);
                }
            }, 4000);
        }
    </script>
    @endpush

    <style>
        /* Custom animations and transitions */
        .group:hover .opacity-0 {
            opacity: 1;
        }

        /* Enhanced focus states */
        button:focus, a:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        /* Smooth transitions for interactive elements */
        .transition-colors {
            transition-property: color, background-color, border-color;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        /* Memuat...imation */
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* Status timeline enhancements */
        .timeline-progress {
            transition: width 0.5s ease-in-out;
        }

        /* Card hover effects */
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Dark mode improvements */
        @media (prefers-color-scheme: dark) {
            .hover-lift:hover {
                box-shadow: 0 10px 25px rgba(255, 255, 255, 0.05);
            }
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .lg\:col-span-2 {
                grid-column: span 1;
            }
        }

        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                color: black !important;
                background: white !important;
            }
        }
    </style>
</x-layouts.client>