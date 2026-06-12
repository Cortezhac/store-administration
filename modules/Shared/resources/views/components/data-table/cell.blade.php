@props(['class' => ''])

<td {{ $attributes->merge(['class' => 'px-6 py-5 ' . $class]) }}>
    {{ $slot }}
</td>
