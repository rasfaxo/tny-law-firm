@props(['disabled' => false, 'tag' => 'input'])

@if ($tag === 'textarea')
    <textarea @disabled($disabled) {{ $attributes->merge(['class' => 'w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm px-4 py-3 disabled:opacity-50 disabled:cursor-not-allowed']) }}>{{ $slot }}</textarea>
@else
    <input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm h-11 px-4 disabled:opacity-50 disabled:cursor-not-allowed']) }}>
@endif
