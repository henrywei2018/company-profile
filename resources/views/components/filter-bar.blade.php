<!-- resources/views/components/filter-bar.blade.php -->
<div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        @if(isset($filters))
            <div class="sm:flex-shrink-0">
                {{ $filters }}
            </div>
        @endif
        
        @if(isset($search))
            <div class="sm:flex-grow">
                {{ $search }}
            </div>
        @endif
    </div>
    
    <div class="flex justify-end">
        @if(isset($actions))
            {{ $actions }}
        @endif
    </div>
</div>