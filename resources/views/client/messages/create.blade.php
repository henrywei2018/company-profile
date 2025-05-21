<!-- resources/views/client/messages/create.blade.php -->
<x-layouts.admin title="New Message" :unreadMessages="$unreadMessages" :pendingQuotations="$pendingQuotations">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <x-admin.breadcrumb :items="[
            'Messages' => route('client.messages.index'),
            'New Message' => route('client.messages.create')
        ]" />
    </div>
    
    <x-admin.card>
        <x-slot name="title">New Message</x-slot>
        <x-slot name="subtitle">Send a new message to our support team</x-slot>
        
        <form action="{{ route('client.messages.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-6">
                <x-admin.input
                    name="subject"
                    label="Subject"
                    placeholder="Enter message subject"
                    required
                    value="{{ old('subject') }}"
                />
                
                <x-admin.textarea
                    name="message"
                    label="Message"
                    placeholder="Type your message here..."
                    rows="6"
                    required
                    value="{{ old('message') }}"
                />
                
                <x-admin.file-upload
                    name="attachments[]"
                    label="Attachments"
                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx"
                    multiple="true"
                    helper="Max 5 files. Maximum size per file: 2MB."
                    maxFiles="5"
                    maxFileSize="2"
                />
                
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-neutral-700">
                    <x-admin.button
                        href="{{ route('client.messages.index') }}"
                        color="light"
                        type="button"
                    >
                        Cancel
                    </x-admin.button>
                    
                    <x-admin.button
                        type="submit"
                        color="primary"
                    >
                        <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Send Message
                    </x-admin.button>
                </div>
            </div>
        </form>
    </x-admin.card>
</x-layouts.admin>