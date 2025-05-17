<!-- resources/views/components/form/rich-editor.blade.php -->
@props(['name', 'label', 'value' => null, 'required' => false, 'disabled' => false, 'helper' => null, 'placeholder' => ''])

<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    <label for="{{ $name }}" class="block text-sm font-medium mb-2 {{ $disabled ? 'text-gray-400 dark:text-gray-500' : 'text-gray-700 dark:text-gray-200' }}">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    <div 
        x-data="{ 
            content: @js(old($name, $value)),
            init() {
                const quill = new Quill(this.$refs.editor, {
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
                
                quill.root.innerHTML = this.content;
                
                quill.on('text-change', () => {
                    this.content = quill.root.innerHTML;
                    document.getElementById('{{ $name }}').value = this.content;
                });
                
                if (@js($disabled)) {
                    quill.disable();
                    this.$refs.editor.classList.add('bg-gray-100');
                    this.$refs.editor.classList.add('dark:bg-gray-700');
                }
            }
        }"
    >
        <div x-ref="editor" class="bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 rounded-md"></div>
        <input type="hidden" id="{{ $name }}" name="{{ $name }}" x-model="content">
    </div>
    
    @if($helper)
        <div class="mt-1">
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $helper }}</span>
        </div>
    @endif
    
    @error($name)
        <div class="mt-1">
            <span class="text-xs text-red-600 dark:text-red-500">{{ $message }}</span>
        </div>
    @enderror
</div>

@once
@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
@endpush
@endonce