@extends('layouts.pages.users.provider.app')

@section('content')
<div class="bg-[#004d40] min-h-screen text-white pb-16 space-y-12">

    <!-- 1. HERO SECTION: PENCARIAN & WELCOME -->
    <section class="relative pt-12 pb-8 px-4 md:px-8 text-center max-w-5xl mx-auto space-y-6">
        <span class="inline-block bg-[#003d30] border border-emerald-600/40 text-emerald-300 text-xs font-semibold px-4 py-1.5 rounded-full uppercase tracking-wider shadow-sm">
            Selamat Datang di Portal Perpustakaan
        </span>
        
        <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight leading-tight">
            Temukan Ilmu & Literasi Masa Depanmu
        </h1>
        
        <p class="text-gray-200 text-sm md:text-base max-w-2xl mx-auto font-light">
            Akses ribuan katalog buku, bahan ajar, dan e-book digital Perpustakaan Madrasah Darussalam secara cepat dan mudah.
        </p>

        <!-- Form Search Utama -->
        <form action="{{ route('user.home') }}" method="GET" class="max-w-2xl mx-auto mt-6">
            <div class="relative flex items-center bg-white rounded-full p-1.5 shadow-2xl border border-emerald-500/30">
                <div class="pl-4 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul buku, pengarang, atau kata kunci..." 
                       class="w-full pl-3 pr-4 py-2.5 text-sm text-gray-800 bg-transparent focus:outline-none placeholder-gray-400 font-medium">
                <button type="submit" class="bg-[#003d30] hover:bg-[#002820] text-white px-6 py-2.5 rounded-full text-xs font-bold transition duration-200 shrink-0">
                    Cari Buku
                </button>
            </div>
        </form>

    </section>

    <div class="max-w-6xl mx-auto px-4 space-y-12">

        <!-- 2. HASIL PENCARIAN / KATALOG BUKU TERBARU DARI DATABASE ADMIN -->
        <section class="space-y-6">
            <div class="flex justify-between items-end border-b border-emerald-800/60 pb-3">
                <div>
                    <h3 class="text-xl md:text-2xl font-bold">
                        {{ request('search') ? 'Hasil Pencarian: "'.request('search').'"' : 'Koleksi Buku Terbaru' }}
                    </h3>
                    <p class="text-xs text-emerald-200 font-light mt-0.5">
                        {{ request('search') ? 'Menampilkan buku yang sesuai dengan kata kunci' : 'Koleksi buku asli dari Perpustakaan' }}
                    </p>
                </div>
                @if(request('search'))
                    <a href="{{ route('user.home') }}" class="text-xs text-emerald-300 hover:underline">
                        Reset Pencarian
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
                @if(isset($books) && count($books) > 0)
                    @foreach($books as $buku)
                        <div class="bg-[#003d30] border border-emerald-700/50 rounded-xl overflow-hidden hover:border-emerald-400 transition group flex flex-col justify-between shadow-lg">
                            <div>
                                <div class="h-44 bg-emerald-950 overflow-hidden relative">
                                    <img src="{{ data_get($buku, 'cover') ? asset('storage/' . data_get($buku, 'cover')) : 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=400&q=80' }}" 
                                         alt="{{ data_get($buku, 'judul') }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    
                                    @if(data_get($buku, 'category.nama'))
                                        <span class="absolute top-2 right-2 bg-emerald-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded">
                                            {{ data_get($buku, 'category.nama') }}
                                        </span>
                                    @elseif(data_get($buku, 'categories') && method_exists($buku->categories, 'isNotEmpty') && $buku->categories->isNotEmpty())
                                        <span class="absolute top-2 right-2 bg-emerald-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded">
                                            {{ $buku->categories->first()->nama }}
                                        </span>
                                    @endif
                                </div>
                                <div class="p-3 space-y-1">
                                    <h4 class="font-bold text-xs text-white line-clamp-2">{{ data_get($buku, 'judul') }}</h4>
                                    <p class="text-[11px] text-gray-300 line-clamp-1">{{ data_get($buku, 'penulis') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full text-center py-8 text-gray-300 text-xs">
                        Buku tidak ditemukan. Coba dengan kata kunci lain.
                    </div>
                @endif
            </div>
        </section>

        <!-- 3. SECTION KUTIPAN AYAT AL-QUR'AN -->
        <section class="bg-[#b2c8c6] text-[#003d30] rounded-2xl p-6 md:p-8 shadow-xl text-center space-y-3 border border-white/20">
            <div class="inline-block bg-[#004d40] text-white text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">
                Mutiara Al-Qur'an
            </div>
            <h2 class="text-xl md:text-2xl font-bold">QS. Al-Baqarah: 152</h2>
            <p class="text-base md:text-lg italic font-medium leading-relaxed max-w-3xl mx-auto">
                "Maka ingatlah kepada-Ku, Aku pun akan mengingatmu. Bersyukurlah kepada-Ku dan janganlah kamu ingkar kepada-Ku."
            </p>
        </section>

        <!-- 4. KATEGORI BUKU & LITERASI -->
        <section class="space-y-6">
            <div class="flex justify-between items-end border-b border-emerald-800/60 pb-3">
                <div>
                    <h3 class="text-xl md:text-2xl font-bold">Kategori Koleksi</h3>
                    <p class="text-xs text-emerald-200 font-light mt-0.5">Jelajahi buku berdasarkan kategori bidang studi</p>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Card Kategori 1 -->
                <div class="bg-[#003d30] border border-emerald-700/50 rounded-xl p-5 hover:border-emerald-400 transition group cursor-pointer shadow-lg flex flex-col justify-between">
                    <div class="w-10 h-10 rounded-lg bg-emerald-800/60 flex items-center justify-center text-emerald-300 mb-3 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm md:text-base text-white">Agama & Pendidikan Islam</h4>
                        <p class="text-xs text-gray-300 mt-1">Al-Qur'an, Hadits, Fiqih, Aqidah</p>
                    </div>
                </div>

                <!-- Card Kategori 2 -->
                <div class="bg-[#003d30] border border-emerald-700/50 rounded-xl p-5 hover:border-emerald-400 transition group cursor-pointer shadow-lg flex flex-col justify-between">
                    <div class="w-10 h-10 rounded-lg bg-emerald-800/60 flex items-center justify-center text-emerald-300 mb-3 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm md:text-base text-white">Sains & Teknologi</h4>
                        <p class="text-xs text-gray-300 mt-1">Komputer, Biologi, Fisika, Kimia</p>
                    </div>
                </div>

                <!-- Card Kategori 3 -->
                <div class="bg-[#003d30] border border-emerald-700/50 rounded-xl p-5 hover:border-emerald-400 transition group cursor-pointer shadow-lg flex flex-col justify-between">
                    <div class="w-10 h-10 rounded-lg bg-emerald-800/60 flex items-center justify-center text-emerald-300 mb-3 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm md:text-base text-white">Bahasa & Sastra</h4>
                        <p class="text-xs text-gray-300 mt-1">Bahasa Arab, Inggris, Indonesia</p>
                    </div>
                </div>

                <!-- Card Kategori 4 -->
                <div class="bg-[#003d30] border border-emerald-700/50 rounded-xl p-5 hover:border-emerald-400 transition group cursor-pointer shadow-lg flex flex-col justify-between">
                    <div class="w-10 h-10 rounded-lg bg-emerald-800/60 flex items-center justify-center text-emerald-300 mb-3 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm md:text-base text-white">Umum & Fiksi</h4>
                        <p class="text-xs text-gray-300 mt-1">Novel, Sejarah, Ensiklopedia</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. INFORMATION & INFORMASI JAM OPERASIONAL -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Informasi Layanan -->
            <div class="md:col-span-2 bg-[#003d30] border border-emerald-700/50 rounded-2xl p-6 shadow-xl space-y-4">
                <h3 class="text-lg font-bold text-emerald-300 border-b border-emerald-800/60 pb-2">
                    Panduan Peminjaman Buku
                </h3>
                <ul class="space-y-3 text-xs md:text-sm text-gray-200">
                    <li class="flex items-start gap-2.5">
                        <span class="bg-emerald-800 text-emerald-300 rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">1</span>
                        <span>Cari buku yang diinginkan melalui menu pencarian di atas.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="bg-emerald-800 text-emerald-300 rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">2</span>
                        <span>Catat Kode Buku atau tunjukkan Kartu Anggota ke petugas perpustakaan.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="bg-emerald-800 text-emerald-300 rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">3</span>
                        <span>Batas waktu peminjaman buku fisik adalah <strong>7 Hari</strong> dari tanggal peminjaman.</span>
                    </li>
                </ul>
            </div>

            <!-- Jam Operasional -->
            <div class="bg-[#b2c8c6] text-[#003d30] rounded-2xl p-6 shadow-xl space-y-4">
                <h3 class="text-lg font-bold border-b border-[#003d30]/20 pb-2">
                    Jam Operasional
                </h3>
                <div class="space-y-2 text-xs md:text-sm font-medium">
                    <div class="flex justify-between">
                        <span>Senin - Kamis</span>
                        <span class="font-bold">07.30 - 15.00 WITA</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Jum'at</span>
                        <span class="font-bold">07.30 - 11.30 WITA</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Sabtu - Minggu</span>
                        <span class="font-bold text-red-700">Tutup</span>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>
@endsection