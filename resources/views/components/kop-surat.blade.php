<div class="border-b-4 border-double border-slate-900 pb-4 mb-6">
    <div class="flex items-center justify-between gap-6">
        <!-- Logo / Icon Badge -->
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-xl bg-[#0F1E3A] text-white flex items-center justify-center font-extrabold text-xl tracking-wider shadow-sm border border-slate-700 shrink-0">
                TNY
            </div>
            <div>
                <h1 class="text-2xl font-black tracking-wider text-[#0F1E3A] uppercase font-serif">
                    {{ config('firm.name', 'TNY & PARTNERS') }}
                </h1>
                <h2 class="text-xs font-bold tracking-[0.2em] text-[#1E3A8A] uppercase">
                    {{ config('firm.tagline', 'Advocates & Legal Consultants') }}
                </h2>
                <p class="text-[11px] text-slate-600 italic mt-0.5">
                    {{ config('firm.description', 'Kantor Advokat & Konsultan Hukum • Pelayanan Hukum Profesional & Berintegritas') }}
                </p>
            </div>
        </div>

        <!-- Contact & Address -->
        <div class="text-right text-[10.5px] text-slate-600 leading-tight space-y-0.5 max-w-[320px] shrink-0">
            <p class="font-semibold text-slate-800">{{ config('firm.address') }}</p>
            <p>Hotline / WA: {{ config('firm.phone') }}</p>
            <p class="text-blue-700 font-medium">
                {{ config('firm.email') }} • {{ config('firm.website') }}
            </p>
        </div>
    </div>
</div>
