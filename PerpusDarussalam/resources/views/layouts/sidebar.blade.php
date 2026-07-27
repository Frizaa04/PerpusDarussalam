<aside class="w-64 bg-[#004d40] text-white min-h-screen flex flex-col shadow-lg">
    <!-- Header Sidebar -->
    <div class="p-6 border-b border-emerald-800/40">
        <h1 class="text-xl font-extrabold tracking-wide text-white uppercase leading-tight">PERPUSTAKAAN</h1>
        <p class="text-xs text-emerald-200 tracking-wider font-semibold uppercase mt-0.5">MADRASAH DARUSSALAM</p>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 py-4 space-y-1">
        
        <!-- DASHBOARD -->
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center px-6 py-3.5 text-sm font-bold tracking-wider transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-[#003d30] border-l-4 border-white' : 'hover:bg-white/10 opacity-90 hover:opacity-100' }}">
            <span class="material-icons text-xl mr-4 min-w-[24px]">dashboard</span>
            <span class="whitespace-nowrap uppercase">DASHBOARD</span>
        </a>

        <!-- MANAJEMEN SISWA -->
        <a href="{{ route('member.index') }}" 
           class="flex items-center px-6 py-3.5 text-sm font-bold tracking-wider transition-all {{ request()->routeIs('member.*') ? 'bg-[#003d30] border-l-4 border-white' : 'hover:bg-white/10 opacity-90 hover:opacity-100' }}">
            <span class="material-icons text-xl mr-4 min-w-[24px]">person</span>
            <span class="whitespace-nowrap uppercase">MANAJEMEN SISWA</span>
        </a>

        <!-- KATALOG BUKU -->
        <a href="{{ route('book.index') }}" 
           class="flex items-center px-6 py-3.5 text-sm font-bold tracking-wider transition-all {{ request()->routeIs('book.*') ? 'bg-[#003d30] border-l-4 border-white' : 'hover:bg-white/10 opacity-90 hover:opacity-100' }}">
            <span class="material-icons text-xl mr-4 min-w-[24px]">menu_book</span>
            <span class="whitespace-nowrap uppercase">KATALOG BUKU</span>
        </a>

        <!-- SIRKULASI -->
        <a href="{{ route('circulation.index') }}" 
           class="flex items-center px-6 py-3.5 text-sm font-bold tracking-wider transition-all {{ request()->routeIs('circulation.*') ? 'bg-[#003d30] border-l-4 border-white' : 'hover:bg-white/10 opacity-90 hover:opacity-100' }}">
            <span class="material-icons text-xl mr-4 min-w-[24px]">sync</span>
            <span class="whitespace-nowrap uppercase">SIRKULASI</span>
        </a>

        <!-- TRANSAKSI -->
        <a href="{{ route('transaction.index') }}" 
           class="flex items-center px-6 py-3.5 text-sm font-bold tracking-wider transition-all {{ request()->routeIs('transaction.*') ? 'bg-[#003d30] border-l-4 border-white' : 'hover:bg-white/10 opacity-90 hover:opacity-100' }}">
            <span class="material-icons text-xl mr-4 min-w-[24px]">edit</span>
            <span class="whitespace-nowrap uppercase">TRANSAKSI</span>
        </a>

        <!-- ABSEN -->
        <a href="{{ route('absen.index') }}" 
           class="flex items-center px-6 py-3.5 text-sm font-bold tracking-wider transition-all {{ request()->routeIs('absen.*') ? 'bg-[#003d30] border-l-4 border-white' : 'hover:bg-white/10 opacity-90 hover:opacity-100' }}">
            <span class="material-icons text-xl mr-4 min-w-[24px]">edit</span>
            <span class="whitespace-nowrap uppercase">ABSEN</span>
        </a>

        <!-- E-BOOK -->
        <a href="{{ route('ebook.index') }}" 
           class="flex items-center px-6 py-3.5 text-sm font-bold tracking-wider transition-all {{ request()->routeIs('ebook.*') ? 'bg-[#003d30] border-l-4 border-white' : 'hover:bg-white/10 opacity-90 hover:opacity-100' }}">
            <span class="material-icons text-xl mr-4 min-w-[24px]">bookmark</span>
            <span class="whitespace-nowrap uppercase">E-BOOK</span>
        </a>

        <!-- LAPORAN -->
        <a href="{{ route('laporan.index') }}" 
           class="flex items-center px-6 py-3.5 text-sm font-bold tracking-wider transition-all {{ request()->routeIs('laporan.index') || request()->routeIs('laporan.koleksi') || request()->routeIs('laporan.anggota') || request()->routeIs('laporan.pengunjung') ? 'bg-[#003d30] border-l-4 border-white' : 'hover:bg-white/10 opacity-90 hover:opacity-100' }}">
            <span class="material-icons text-xl mr-4 min-w-[24px]">description</span>
            <span class="whitespace-nowrap uppercase">LAPORAN</span>
        </a>

        <!-- LAPORAN KEUANGAN -->
        <a href="{{ route('laporan.keuangan') }}" 
           class="flex items-center px-6 py-3.5 text-sm font-bold tracking-wider transition-all {{ request()->routeIs('laporan.keuangan') ? 'bg-[#003d30] border-l-4 border-white' : 'hover:bg-white/10 opacity-90 hover:opacity-100' }}">
            <span class="material-icons text-xl mr-4 min-w-[24px]">assignment</span>
            <span class="whitespace-nowrap uppercase">LAPORAN KEUANGAN</span>
        </a>

    </nav>
</aside>