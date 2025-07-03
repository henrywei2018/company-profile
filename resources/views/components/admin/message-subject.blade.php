{{-- resources/views/components/admin/message-subject.blade.php --}}

@props(['message', 'showClient' => true])

<div class="flex flex-col">
    <div class="text-sm font-medium text-gray-900 dark:text-white">
        {{ $message->subject }}
    </div>
    
    @if($showClient)
    <div class="text-sm text-gray-500 dark:text-gray-400">
        <span class="font-medium">{{ $message->name }}</span>
        @if($message->user)
            <span class="text-xs">({{ $message->user->email }})</span>
        @else
            <span class="text-xs">({{ $message->email }})</span>
        @endif
    </div>
    @endif
    
    @if($message->parent_id)
        <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
            <x-admin.icons.reply class="w-3 h-3 inline mr-1" />
            Reply to conversation
        </div>
    @endif
</div>