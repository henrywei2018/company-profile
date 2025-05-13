<!-- resources/views/admin/quotations/show.blade.php -->
<x-admin-layout :title="'Quotation Request Details'">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <a href="{{ route('admin.quotations.index') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900 mb-2">
                <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Quotations
            </a>
            <h2 class="text-xl font-semibold text-gray-900">Quotation Request: {{ $quotation->project_type }}</h2>
            <div class="mt-1 text-sm text-gray-500">
                Received {{ $quotation->created_at->format('M d, Y \a\t H:i') }}
            </div>
        </div>
        
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            @if($quotation->status === 'pending')
                <form action="{{ route('admin.quotations.approve', $quotation->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Approve
                    </button>
                </form>
                
                <form action="{{ route('admin.quotations.decline', $quotation->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Decline
                    </button>
                </form>
            @else
                <span class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium 
                    {{ $quotation->status === 'approved' ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' }}">
                    <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        @if($quotation->status === 'approved')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        @endif
                    </svg>
                    {{ ucfirst($quotation->status) }}
                </span>
            @endif
            
            <form action="{{ route('admin.quotations.destroy', $quotation->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this quotation?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="mr-2 -ml-1 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <!-- Client Information -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Client Information</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="text-sm font-medium text-gray-500">Name</div>
                <div class="mt-1 text-sm text-gray-900">{{ $quotation->name }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500">Email</div>
                <div class="mt-1 text-sm text-gray-900">
                    <a href="mailto:{{ $quotation->email }}" class="text-indigo-600 hover:text-indigo-900">
                        {{ $quotation->email }}
                    </a>
                </div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500">Phone</div>
                <div class="mt-1 text-sm text-gray-900">
                    @if($quotation->phone)
                        <a href="tel:{{ $quotation->phone }}" class="text-indigo-600 hover:text-indigo-900">
                            {{ $quotation->phone }}
                        </a>
                    @else
                        <span class="text-gray-500">Not provided</span>
                    @endif
                </div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500">Company</div>
                <div class="mt-1 text-sm text-gray-900">
                    {{ $quotation->company ?? 'Not provided' }}
                </div>
            </div>
            
            @if(isset($quotation->user) && $quotation->user)
                <div class="md:col-span-2">
                    <div class="text-sm font-medium text-gray-500">Registered User</div>
                    <div class="mt-1 text-sm text-gray-900">
                        <a href="{{ route('admin.users.edit', $quotation->user->id) }}" class="text-indigo-600 hover:text-indigo-900">
                            {{ $quotation->user->name }} (View Profile)
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Project Details -->
        <div class="px-6 py-4 border-t border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Project Details</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="text-sm font-medium text-gray-500">Project Type</div>
                <div class="mt-1 text-sm text-gray-900">{{ $quotation->project_type }}</div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500">Estimated Budget</div>
                <div class="mt-1 text-sm text-gray-900">
                    {{ $quotation->estimated_budget ?? 'Not specified' }}
                </div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500">Location</div>
                <div class="mt-1 text-sm text-gray-900">
                    {{ $quotation->location ?? 'Not specified' }}
                </div>
            </div>
            
            <div>
                <div class="text-sm font-medium text-gray-500">Desired Timeline</div>
                <div class="mt-1 text-sm text-gray-900">
                    {{ $quotation->timeline ?? 'Not specified' }}
                </div>
            </div>
            
            <div class="md:col-span-2">
                <div class="text-sm font-medium text-gray-500">Project Description</div>
                <div class="mt-1 text-sm text-gray-900 prose max-w-none">
                    {!! nl2br(e($quotation->description)) !!}
                </div>
            </div>
            
            @if($quotation->services)
                <div class="md:col-span-2">
                    <div class="text-sm font-medium text-gray-500">Requested Services</div>
                    <div class="mt-1 flex flex-wrap gap-2">
                        @foreach(json_decode($quotation->services) as $service)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $service }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Response and Notes -->
        <div class="px-6 py-4 border-t border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Response and Notes</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.quotations.update', $quotation->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Internal Notes -->
                <div class="mb-4">
                    <label for="internal_notes" class="block text-sm font-medium text-gray-700">Internal Notes</label>
                    <textarea
                        id="internal_notes"
                        name="internal_notes"
                        rows="3"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    >{{ old('internal_notes', $quotation->internal_notes) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">These notes are for internal use only and will not be visible to the client.</p>
                </div>
                
                <!-- Response to Client -->
                <div class="mb-4">
                    <label for="client_response" class="block text-sm font-medium text-gray-700">Response to Client</label>
                    <textarea
                        id="client_response"
                        name="client_response"
                        rows="5"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    >{{ old('client_response', $quotation->client_response) }}</textarea>
                </div>
                
                <!-- Estimated Cost -->
                <div class="mb-4">
                    <label for="estimated_cost" class="block text-sm font-medium text-gray-700">Estimated Cost</label>
                    <input
                        type="text"
                        id="estimated_cost"
                        name="estimated_cost"
                        value="{{ old('estimated_cost', $quotation->estimated_cost) }}"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        placeholder="e.g. $2,500 - $3,000"
                    />
                </div>
                
                <!-- Estimated Timeline -->
                <div class="mb-4">
                    <label for="estimated_timeline" class="block text-sm font-medium text-gray-700">Estimated Timeline</label>
                    <input
                        type="text"
                        id="estimated_timeline"
                        name="estimated_timeline"
                        value="{{ old('estimated_timeline', $quotation->estimated_timeline) }}"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        placeholder="e.g. 4-6 weeks"
                    />
                </div>
                
                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Send Email Response -->
        <div class="px-6 py-4 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Send Email Response</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.quotations.send-response', $quotation->id) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="email_subject" class="block text-sm font-medium text-gray-700">Email Subject</label>
                    <input
                        type="text"
                        id="email_subject"
                        name="email_subject"
                        value="{{ old('email_subject', 'Your Quotation Request: ' . $quotation->project_type) }}"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        required
                    />
                </div>
                
                <div class="mb-4">
                    <label for="email_message" class="block text-sm font-medium text-gray-700">Email Message</label>
                    <textarea
                        id="email_message"
                        name="email_message"
                        rows="10"
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        required
                    >{{ old('email_message', $quotation->client_response ? $quotation->client_response : "Dear {$quotation->name},\n\nThank you for your inquiry about {$quotation->project_type}. We are pleased to provide you with the following quotation...\n\nPlease let us know if you have any questions or if you would like to proceed.\n\nBest regards,\nCV Usaha Prima Lestari Team") }}</textarea>
                </div>
                
                <div class="flex items-start mb-4">
                    <div class="flex items-center h-5">
                        <input
                            id="include_quotation"
                            name="include_quotation"
                            type="checkbox"
                            value="1"
                            checked
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                        />
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="include_quotation" class="font-medium text-gray-700">Include quotation details in email</label>
                        <p class="text-gray-500">This will include the project details, estimated cost, and timeline in the email.</p>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity History -->
    @if(isset($activities) && $activities->count() > 0)
        <div class="mt-8 bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Activity History</h3>
            </div>
            <div class="p-6">
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($activities as $activity)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex items-start space-x-3">
                                        <div class="relative">
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center ring-8 ring-white">
                                                @if($activity->type === 'status_change')
                                                    <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                    </svg>
                                                @elseif($activity->type === 'email_sent')
                                                    <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                @elseif($activity->type === 'note_added')
                                                    <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                @else
                                                    <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $activity->user ? $activity->user->name : 'System' }}
                                                </div>
                                                <p class="mt-0.5 text-sm text-gray-500">
                                                    {{ $activity->created_at->format('M d, Y \a\t H:i') }}
                                                </p>
                                            </div>
                                            <div class="mt-2 text-sm text-gray-700">
                                                <p>{{ $activity->description }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
</x-admin-layout>