<!-- resources/views/components/tab-panel.blade.php -->
@props(['id', 'active' => false])

<div id="{{ $id }}" role="tabpanel" aria-labelledby="{{ $id }}-tab" 
    {{ $attributes->merge([
        'class' => ($active ? '' : 'hidden') . ' transition-all duration-300',
    ]) }}>
    {{ $slot }}
</div>