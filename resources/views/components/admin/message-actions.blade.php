{{-- resources/views/components/admin/message-actions.blade.php --}}

@props(['message', 'routes' => []])

<div class="flex items-center justify-end space-x-2">
    {{-- View Button --}}
    @if(isset($routes['show']))
    <a href="{{ route($routes['show'], $message) }}" 
       class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" 
       title="View Message">
        <x-admin.icons.eye class="w-4 h-4" />
    </a>
    @endif
    
    {{-- Reply Button --}}
    @if(isset($routes['reply']))
    <a href="{{ route($routes['reply'], $message) }}" 
       class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300" 
       title="Reply">
        <x-admin.icons.reply class="w-4 h-4" />
    </a>
    @endif
    
    {{-- Toggle Read Status --}}
    @if(isset($routes['toggle_read']))
    <form action="{{ route($routes['toggle_read'], $message) }}" method="POST" class="inline">
        @csrf
        <button type="submit" 
                class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300" 
                title="{{ $message->is_read ? 'Mark as Unread' : 'Mark as Read' }}">
            @if($message->is_read)
                <x-admin.icons.mail class="w-4 h-4" />
            @else
                <x-admin.icons.mail-open class="w-4 h-4" />
            @endif
        </button>
    </form>
    @endif
    
    {{-- Delete Button --}}
    @if(isset($routes['destroy']))
    <form action="{{ route($routes['destroy'], $message) }}" method="POST" class="inline" 
          onsubmit="return confirm('Are you sure you want to delete this message?')">
        @csrf
        @method('DELETE')
        <button type="submit" 
                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" 
                title="Delete Message">
            <x-admin.icons.trash class="w-4 h-4" />
        </button>
    </form>
    @endif
</div>