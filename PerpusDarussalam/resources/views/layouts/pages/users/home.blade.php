@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#f4f7f6] flex flex-col font-sans">

    <!-- Header & Search Bar -->
    <header class="bg-white px-8 py-4 flex justify-between items-center shadow-sm">
        <div class="flex items-center gap-3">
            <img src="{{ asset('image/covers/darussalam.png') }}" alt="Logo Darussalam" class="h-12 object-contain">
            <div class="border-l-2 border-gray-300 pl-3">
                <h1 class="font-bold text-[#004d40] leading-tight tracking-wider text-base">PERPUSTAKAAN</h1>
                <p class="text-xs text-gray-500 font-semibold tracking-widest uppercase">MADRASAH DARUSSALAM</p>
            </div>
        </div>

        <!-- Form Pencarian -->
        <form action="{{ route('book.index') }}" method="GET" class="flex items-center border border-gray-300 rounded overflow-hidden">
            <input type="text" name="search" placeholder="Search" class="px-4 py-1.5 text-sm outline-none text-gray-700 w-64">
            <button type="submit" class="bg-[#003d30] text-white px-3 py-1.5 flex items-center justify-center hover:bg-[#002b22] transition">
                <span class="material-icons text-sm">search</span>
            </button>
        </form>
    </header>

    <!-- Navigation Bar -->
    <nav class="bg-[#003d30] text-white px-8 py-3 shadow-md">
        <div class="max-w-6xl mx-auto flex justify-center items-center gap-12 font-medium text-sm tracking-wide">
            <a href="{{ route('user.home') }}" class="hover:text-emerald-300 font-semibold border-b-2 border-emerald-400 pb-0.5 transition">Beranda</a>
            <a href="#" class="hover:text-emerald-300 transition">News</a>
            <a href="#" class="hover:text-emerald-300 transition">Area Anggota</a>
            <a href="#" class="hover:text-emerald-300 transition">E-Book</a>
            <a href="#" class="hover:text-emerald-300 transition">Tentang Kami</a>
        </div>
    </nav>

    <!-- Hero Section / Kutipan Ayat -->
    <section class="bg-[#005a4e] text-white flex-1 flex items-center justify-center py-24 px-6 text-center">
        <div class="max-w-2xl mx-auto space-y-6">
            <h2 class="text-2xl md:text-3xl font-bold tracking-wide">Kutipan Ayat</h2>
            
            <div class="space-y-2">
                <p class="text-sm font-semibold tracking-wider text-emerald-100">QS. Al-Baqarah: 152</p>
                <p class="text-sm md:text-base leading-relaxed font-light text-gray-100 italic">
                    "Maka ingatlah kepada-Ku, Aku pun akan mengingatmu.<br>
                    Bersyukurlah kepada-Ku dan janganlah kamu ingkar kepada-Ku."
                </p>
            </div>

            <div class="pt-2">
                <a href="#" class="inline-block border border-white text-white text-xs font-semibold px-6 py-2 rounded-full hover:bg-white hover:text-[#005a4e] transition duration-200">
                    Detail Ayat
                </a>
            </div>
        </div>
    </section>

</div>
@endsection