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
@endphp

<div class="mb-4">
    <label for="{{ $id }}" class="block text-sm font-medium mb-2 {{ $disabled ? 'text-gray-400 dark:text-neutral-500' : 'text-gray-700 dark:text-neutral-300' }}">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
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
            }
        }"
    >
        <div class="border border-gray-300 dark:border-neutral-700 rounded-md overflow-hidden">
            <div x-ref="editor" class="bg-white dark:bg-neutral-800" style="min-height: {{ $minHeight }}"></div>
            <input type="hidden" id="{{ $id }}" name="{{ $name }}" x-ref="input" x-model="content" {{ $required ? 'required' : '' }}>
        </div>
        
        @if($helper && !$errors->has($name))
            <div class="mt-1">
                <span class="text-xs text-gray-500 dark:text-neutral-400">{{ $helper }}</span>
            </div>
        @endif
        
        @error($name)
            <div class="mt-1">
                <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
            </div>
        @enderror
    </div>
</div>

@once
@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
@endpush
@endonce