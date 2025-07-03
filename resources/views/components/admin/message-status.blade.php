{{-- resources/views/components/admin/message-status.blade.php --}}

@props(['message'])

<div class="flex flex-col ">
    {{-- Read Status --}}
    @if(!$message->is_read)
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
            <x-admin.icons.mail class="w-3 h-3 mr-1" />
            Unread
        </span>
    @else
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
            <x-admin.icons.mail-open class="w-3 h-3 mr-1" />
            Read
        </span>
    @endif
    
    {{-- Reply Status --}}
    @if(!$message->is_replied)
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
            <x-admin.icons.clock class="w-3 h-3 mr-1" />
            Pending
        </span>
    @endif
    
    {{-- Attachment Indicator --}}
    @if($message->attachments->count() > 0)
        <span class="text-gray-400" title="{{ $message->attachments->count() }} attachment(s)">
            <x-admin.icons.paperclip class="w-4 h-4" />
        </span>
    @endif
</div>