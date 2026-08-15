@props(['margin' => 'my-6'])

<hr {{ $attributes->merge(['class' => "border-[#F1F5F9] {$margin}"]) }}>
