<!-- Header Area (Logo & Search) -->
<header class="bg-white px-8 md:px-16 py-4 flex justify-between items-center shadow-sm">
    <div class="flex items-center gap-3">
        <img src="{{ asset('image/covers/darussalam.png') }}" alt="Logo Darussalam" class="h-12 object-contain">
        <div class="border-l-2 border-gray-300 pl-3">
            <h1 class="font-bold text-[#004d40] leading-tight tracking-wider text-base">PERPUSTAKAAN</h1>
            <p class="text-xs text-gray-500 font-semibold tracking-widest uppercase">MADRASAH DARUSSALAM</p>
        </div>
    </div>

    <!-- Form Pencarian -->
<div class="flex items-center gap-3">
    <!-- Form Search Asli -->
    <form action="#" method="GET" class="flex items-center">
        <input type="text" placeholder="Search" class="px-3 py-1.5 border border-gray-300 rounded-l-md focus:outline-none text-sm text-gray-700">
        <button type="submit" class="bg-[#003d30] text-white px-3 py-2 rounded-r-md hover:bg-[#004d40] transition flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </button>
    </form>

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
        <a href="#" class="hover:text-emerald-200 transition">E-Book</a>
        <a href="#" class="hover:text-emerald-200 transition">Tentang Kami</a>
    </div>
</nav>