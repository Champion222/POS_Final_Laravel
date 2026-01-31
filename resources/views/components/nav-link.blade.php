@props(['active', 'icon' => false])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-2 px-4 py-2 bg-gray-900 text-white text-sm font-bold rounded-xl shadow-lg shadow-gray-200 transition-all duration-300 transform scale-105'
            : 'flex items-center gap-2 px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-900 hover:bg-gray-50 rounded-xl transition-all duration-300';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon) <i class="fas {{ $icon }} {{ $active ? 'text-indigo-400' : 'text-gray-300 group-hover:text-gray-500' }}"></i> @endif
    {{ $slot }}
</a>