
<aside class="w-72 bg-[#00695c] text-white flex flex-col justify-between shadow-lg min-h-screen">
    <div class="p-6">

        <div class="mb-6">
            <h2 class="text-2xl font-bold tracking-wider uppercase">Perpustakaan</h2>
            <p class="text-xs text-emerald-200 tracking-widest font-semibold uppercase mt-1">Madrasah Darussalam</p>
        </div>
        <hr class="border-emerald-700/60 my-6">
        
        <!-- Menu Navigasi -->
        <nav class="space-y-2">
            <!-- Menu Dashboard -->
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-4 px-4 py-3.5 rounded font-bold text-white transition {{ Route::is('admin.dashboard') ? 'bg-[#004d40] shadow-sm' : 'hover:bg-[#004d40]' }}">
                <span class="material-icons text-xl">dashboard</span> 
                <span class="tracking-wider text-sm uppercase">Dashboard</span>
            </a>

            <!-- Menu Manajemen Siswa -->
            <a href="{{ route('member.index') }}" 
               class="flex items-center gap-4 px-4 py-3.5 rounded font-bold text-white transition {{ Route::is('member.index') ? 'bg-[#004d40] shadow-sm' : 'hover:bg-[#004d40]' }}">
                <span class="material-icons text-xl">person_search</span> 
                <span class="tracking-wider text-sm uppercase">Manajemen Siswa</span>
            </a>

            <!-- Menu Katalog Buku -->
            <a href="{{ route('book.index') }}" 
               class="flex items-center gap-4 px-4 py-3.5 rounded font-bold text-white transition {{ Route::is('book.index') ? 'bg-[#004d40] shadow-sm' : 'hover:bg-[#004d40]' }}">
                <span class="material-icons text-xl">tablet_mac</span> 
                <span class="tracking-wider text-sm uppercase">Katalog Buku</span>
            </a>

            <!-- Menu Sirkulasi -->
            <a href="{{ route('circulation.index') }}" 
               class="flex items-center gap-4 px-4 py-3.5 rounded font-bold text-white transition {{ Route::is('circulation.index') ? 'bg-[#004d40] shadow-sm' : 'hover:bg-[#004d40]' }}">
                <span class="material-icons text-xl">swap_horiz</span> 
                <span class="tracking-wider text-sm uppercase">Sirkulasi</span>
            </a>

            <!-- Menu Absen -->
            <a href="#" 
               class="flex items-center gap-4 px-4 py-3.5 rounded font-bold text-white transition hover:bg-[#004d40]">
                <span class="material-icons text-xl">clean_hands</span> 
                <span class="tracking-wider text-sm uppercase">Absen</span>
            </a>

            <!-- Menu Laporan -->
            <a href="#" 
               class="flex items-center gap-4 px-4 py-3.5 rounded font-bold text-white transition hover:bg-[#004d40]">
                <span class="material-icons text-xl">description</span> 
                <span class="tracking-wider text-sm uppercase">Laporan</span>
            </a>
        </nav>
    </div>
</aside>