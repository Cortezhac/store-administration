@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'pb-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 ' . $class]) }}>
    {{ $slot }}
</div>
