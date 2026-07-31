@extends('layouts.pages.users.provider.app')

@section('content')
    <!-- Hero Section / Kutipan Ayat -->
    <section class="bg-[#004d40] text-white min-h-[calc(100vh-140px)] flex flex-col items-center justify-center py-24 px-6 text-center">
        <div class="max-w-2xl mx-auto space-y-6">
            <h2 class="text-2xl md:text-3xl font-bold tracking-wide">Kutipan Ayat</h2>
            
            <div class="space-y-2">
                <p class="text-sm font-medium tracking-wide text-gray-200">QS. Al-Baqarah: 152</p>
                <p class="text-sm md:text-base leading-relaxed font-normal text-gray-100 italic">
                    "Maka ingatlah kepada-Ku, Aku pun akan mengingatmu.<br>
                    Bersyukurlah kepada-Ku dan janganlah kamu ingkar kepada-Ku."
                </p>
            </div>

            <div class="pt-4">
                <a href="#" class="inline-block border border-white text-white text-xs font-semibold px-8 py-2.5 rounded-full hover:bg-white hover:text-[#004d40] transition duration-200">
                    Detail Ayat
                </a>
            </div>
        </div>
    </section>
@endsection