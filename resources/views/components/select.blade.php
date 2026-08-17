@props(['disabled' => false])

<select @disabled($disabled) {{ $attributes->merge(['class' => 'w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4 disabled:opacity-50 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</select>
