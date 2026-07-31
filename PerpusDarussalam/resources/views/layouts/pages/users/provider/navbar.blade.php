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
    <form action="#" method="GET" class="flex items-center border border-gray-400 rounded overflow-hidden">
        <input type="text" name="search" placeholder="Search" class="px-4 py-1.5 text-sm outline-none text-gray-700 w-52 md:w-64 placeholder-gray-500">
        <button type="submit" class="bg-[#003d30] text-white px-3 py-1.5 flex items-center justify-center hover:bg-[#002b22] transition">
            <span class="material-icons text-base">search</span>
        </button>
    </form>
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