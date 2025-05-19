<!-- resources/views/components/admin/rich-editor.blade.php -->
@props([
    'label',
    'name',
    'value' => null,
    'placeholder' => '',
    'disabled' => false,
    'required' => false,
    'helper' => null,
    'error' => null,
    'id' => null,
    'minHeight' => '200px'
])

@php
    $id = $id ?? $name;
    $errorState = $errors->has($name);
@endphp

<div class="mb-4 w-full">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div 
        x-data="{ 
            content: @js(old($name, $value)),
            init() {
                const editor = new Quill(this.$refs.editor, {
                    theme: 'snow',
                    placeholder: @js($placeholder),
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline', 'strike'],
                            ['blockquote', 'code-block'],
                            [{ 'header': 1 }, { 'header': 2 }],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'script': 'sub'}, { 'script': 'super' }],
                            [{ 'indent': '-1'}, { 'indent': '+1' }],
                            [{ 'size': ['small', false, 'large', 'huge'] }],
                            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'align': [] }],
                            ['link', 'image'],
                            ['clean']
                        ]
                    }
                });
                
                editor.root.innerHTML = this.content || '';
                
                editor.on('text-change', () => {
                    this.content = editor.root.innerHTML;
                    this.$refs.input.value = this.content;
                });
                
                if (@js($disabled)) {
                    editor.disable();
                    this.$refs.editor.classList.add('bg-gray-100');
                    this.$refs.editor.classList.add('dark:bg-neutral-900');
                    this.$refs.editor.classList.add('cursor-not-allowed');
                }
                
                // Apply text color to match textarea
                this.$refs.editor.querySelector('.ql-editor').classList.add('text-gray-700');
                this.$refs.editor.querySelector('.ql-editor').classList.add('dark:text-white');
            }
        }"
    >
        <div class="rounded-md shadow-sm overflow-hidden {{ $errorState ? 'border border-red-500' : 'border border-gray-300 dark:border-neutral-700' }}">
            <div x-ref="editor" class="bg-white dark:bg-neutral-800" style="min-height: {{ $minHeight }}"></div>
            <input type="hidden" id="{{ $id }}" name="{{ $name }}" x-ref="input" x-model="content" {{ $required ? 'required' : '' }}>
        </div>
        
        @if($helper && !$errorState)
            <div class="mt-1">
                <span class="text-xs text-gray-500 dark:text-neutral-400">{{ $helper }}</span>
            </div>
        @endif
        
        @error($name)
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>

<style>
    /* Custom Quill styling to match textarea */
    .ql-toolbar.ql-snow {
        border-color: inherit;
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
        background-color: #f9fafb; /* gray-50 */
    }
    
    .ql-container.ql-snow {
        border-color: inherit;
        border-bottom-left-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
    }
    
    /* Focus state to match textarea */
    .ql-container.ql-snow:focus-within {
        border-color: #3b82f6; /* blue-500 */
        box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.5); /* focus:ring focus:ring-blue-500 focus:ring-opacity-50 */
    }
    
    /* Dark mode adjustments */
    .dark .ql-toolbar.ql-snow {
        background-color: #1f2937; /* gray-800 */
        border-color: #374151; /* gray-700 */
    }
    
    .dark .ql-toolbar.ql-snow .ql-picker,
    .dark .ql-toolbar.ql-snow .ql-stroke {
        color: #e5e7eb; /* gray-200 */
        stroke: #e5e7eb; /* gray-200 */
    }
    
    .dark .ql-toolbar.ql-snow .ql-fill {
        fill: #e5e7eb; /* gray-200 */
    }
    
    .dark .ql-editor.ql-blank::before {
        color: #9ca3af; /* gray-400 */
    }
</style>

@once
@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
@endpush
@endonce