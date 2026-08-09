<!-- Header Area (Logo & Search) -->
<header class="bg-white px-8 md:px-16 py-4 flex justify-between items-center shadow-sm">
    <div class="flex items-center gap-3">
        <img src="{{ asset('image/covers/MadrasahDarussalam.png') }}" alt="Logo Darussalam" class="h-15 object-contain">
        <div class="border-l-2 border-gray-300 pl-3">
            <h1 class="font-bold text-[#004d40] leading-tight tracking-wider text-base">PERPUSTAKAAN</h1>
            <p class="text-xs text-gray-500 font-semibold tracking-widest uppercase">MADRASAH DARUSSALAM</p>
        </div>
    </div>

    <!-- Form Pencarian -->
<div class="flex items-center gap-3">
    <!-- Form Search Asli -->


    <!-- Tombol LogOut di Samping Kanan Search -->
    @auth('web')
        <form action="{{ route('user.logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-[#004d40] hover:bg-[#003d30] text-white font-semibold text-sm px-4 py-1.5 rounded-md transition duration-200 shadow flex items-center gap-1">
                LogOut
            </button>
        </form>
    @endauth
</div>
</header>

<!-- Navigation Bar Menu -->
<nav class="bg-[#003d30] text-white w-full">
    <div class="max-w-5xl mx-auto flex justify-between items-center px-8 py-3 text-sm font-semibold tracking-wide">
        <a href="{{ route('user.home') }}" class="hover:text-emerald-200 transition">Beranda</a>
        <a href="{{ route('user.area_anggota') }}" class="hover:text-emerald-200 transition">Area Anggota</a>
        <a href="{{ route('user.ebook.index') }}" class="hover:text-emerald-200 transition">E-Book</a>
        <a href="{{ route('user.about') }}" class="hover:text-emerald-200 transition">Tentang Kami</a>
    </div>
</nav>

@php
    $activeAnnouncement = \App\Models\Announcement::where('is_active', true)->latest()->first();
@endphp

@if($activeAnnouncement)
    <!-- Bar Teks Berjalan / Marquee -->
    <div class="bg-amber-100 border-b border-amber-200 text-amber-900 px-4 py-2 text-xs font-medium overflow-hidden relative shadow-inner flex items-center">
        <div class="bg-amber-600 text-white font-bold px-2 py-0.5 rounded text-[10px] uppercase tracking-wider mr-3 shrink-0 flex items-center gap-1 shadow-sm">
            <svg class="w-3 h-3 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/></svg>
            Pengumuman
        </div>
        
        <!-- Elemen Marquee HTML Murni -->
        <marquee behavior="scroll" direction="left" scrollamount="5" class="w-full tracking-wide font-semibold text-amber-950">
            {{ $activeAnnouncement->content }}
        </marquee>
    </div>
@endif