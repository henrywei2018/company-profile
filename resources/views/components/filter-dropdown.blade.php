<!-- resources/views/components/filter-dropdown.blade.php -->
@props(['name', 'label', 'options', 'currentValue' => null])

<div class="hs-dropdown relative inline-flex">
    <button id="filter-{{ $name }}" type="button" class="hs-dropdown-toggle py-2 px-4 inline-flex justify-center items-center gap-2 rounded-md border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 focus:ring-offset-white transition-all text-sm dark:bg-gray-800 dark:hover:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:hover:text-white dark:focus:ring-gray-700 dark:focus:ring-offset-gray-800">
        {{ $label }}
        <svg class="hs-dropdown-open:rotate-180 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
    </button>

    <div class="hs-dropdown-menu transition-[opacity,margin] duration hidden min-w-[15rem] bg-white shadow-md rounded-lg p-2 dark:bg-gray-800 dark:border dark:border-gray-700" aria-labelledby="filter-{{ $name }}">
        <form id="filter-form-{{ $name }}" method="GET" action="{{ url()->current() }}">
            @foreach(request()->except($name) as $key => $value)
                @if(is_array($value))
                    @foreach($value as $arrayValue)
                        <input type="hidden" name="{{ $key }}[]" value="{{ $arrayValue }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
            
            <div class="mb-2">
                <button type="button" onclick="clearFilter('{{ $name }}')" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    Clear filter
                </button>
            </div>
            
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($options as $value => $label)
                    <div class="py-2 first:pt-0 last:pb-0">
                        <div class="flex items-center">
                            <input type="radio" id="{{ $name }}-{{ $value }}" name="{{ $name }}" value="{{ $value }}" 
                                class="shrink-0 mt-0.5 border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800" 
                                {{ $currentValue == $value ? 'checked' : '' }}
                                onchange="document.getElementById('filter-form-{{ $name }}').submit();">
                            <label for="{{ $name }}-{{ $value }}" class="ml-2 text-sm text-gray-800 dark:text-gray-200">
                                {{ $label }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </form>
    </div>
</div>

@once
<script>
    function clearFilter(name) {
        const form = document.getElementById('filter-form-' + name);
        const inputs = form.querySelectorAll(`input[name="${name}"]`);
        inputs.forEach(input => {
            input.checked = false;
        });
        form.submit();
    }
</script>
@endonce