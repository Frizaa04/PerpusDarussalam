@extends('layouts.pages.users.provider.app')

@section('content')
<div class="bg-[#004d40] min-h-screen text-white pb-16 space-y-12">

    <!-- 1. HERO SLIDER BANNER (TINGGI DIKECILKAN & TOMBOL MANUAL DIHAPUS) -->
    <section class="relative w-full h-[220px] md:h-[450px]">

        <!-- Slider Wrapper Gambar -->
        <div id="heroBannerSlider" class="w-full h-full relative">
            @php
                $activeBanners = \App\Models\Banner::where('is_active', true)->get();
            @endphp

            @forelse($activeBanners as $index => $banner)
                <div class="hero-slide absolute inset-0 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }} transition-opacity duration-1000 ease-in-out">
                    <!-- PASTIKAN KELAS W-FULL H-FULL OBJECT-COVER ADA PADA TAG IMG INI -->
                    <img src="{{ asset('storage/' . $banner->image) }}" 
                        alt="Banner Perpustakaan {{ $index + 1 }}" 
                        class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40"></div>
                </div>
            @empty
                <!-- Fallback jika belum ada banner aktif yang diset admin -->
                <div class="hero-slide absolute inset-0 opacity-100">
                    <img src="https://images.unsplash.com/photo-1521587760476-6c12a4b040da?auto=format&fit=crop&w=1600&q=80" 
                        alt="Default Banner" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40"></div>
                </div>
            @endforelse
        </div>

        <!-- Form Search Utama (Tetap Melayang di Bawah Banner) -->
        <div class="absolute bottom-0 left-0 right-0 z-30 px-4 translate-y-1/2">
            <form action="{{ route('user.home') }}" method="GET" class="max-w-2xl mx-auto">
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
        </div>

    </section>

    <div class="max-w-6xl mx-auto px-4 space-y-12">

        <!-- 2. HASIL PENCARIAN / KATALOG BUKU TERBARU -->
        <section class="bg-[#00382e] text-white rounded-2xl p-6 md:p-8 shadow-2xl space-y-6 w-full relative border border-emerald-700/60">
            <div class="flex justify-between items-end border-b border-emerald-800/80 pb-4">
                <div>
                    <h3 class="text-xl md:text-2xl font-bold tracking-tight text-white">
                        {{ request('search') ? 'Hasil Pencarian: "'.request('search').'"' : 'Koleksi Buku Terbaru' }}
                    </h3>
                    <p class="text-xs text-emerald-200/80 font-light mt-1">
                        {{ request('search') ? 'Menampilkan buku yang sesuai dengan kata kunci' : 'Koleksi buku terupdate dari Perpustakaan' }}
                    </p>
                </div>
                
                <div class="flex items-center gap-2">
                    @if(request('search'))
                        <a href="{{ route('user.home') }}" class="text-xs text-emerald-300 font-semibold hover:underline mr-2">
                            Reset Pencarian
                        </a>
                    @else
                        <!-- Tombol Navigasi Slider Buku -->
                        <button id="slideLeft" type="button" class="bg-[#002820] hover:bg-emerald-800 text-emerald-200 p-2.5 rounded-full border border-emerald-700/60 shadow-sm transition active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button id="slideRight" type="button" class="bg-[#002820] hover:bg-emerald-800 text-emerald-200 p-2.5 rounded-full border border-emerald-700/60 shadow-sm transition active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Container Slider Card Buku -->
            <div id="bookSlider" class="flex gap-5 overflow-x-auto scroll-smooth no-scrollbar py-4 px-1 w-full">
                @if(isset($books) && count($books) > 0)
                    @foreach($books as $buku)
                        <div class="w-[calc((100%-1.25rem)/2)] sm:w-[calc((100%-2.5rem)/3)] md:w-[calc((100%-6.25rem)/6)] shrink-0 bg-[#dce8e5] rounded-xl border border-emerald-300/40 p-3 shadow-lg hover:shadow-2xl hover:-translate-y-2 hover:border-emerald-400 transition-all duration-300 cursor-pointer group flex flex-col justify-between">
                            <div>
                                <div class="w-full h-52 bg-[#b2c8c6] rounded-lg overflow-hidden relative shadow-inner mb-3">
                                    <img src="{{ data_get($buku, 'cover') ? asset('storage/' . data_get($buku, 'cover')) : 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=400&q=80' }}" 
                                         alt="{{ data_get($buku, 'judul') }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    
                                    @if(data_get($buku, 'category.nama'))
                                        <span class="absolute top-2 right-2 bg-[#004d40] text-emerald-100 text-[9px] font-bold px-2 py-0.5 rounded shadow">
                                            {{ data_get($buku, 'category.nama') }}
                                        </span>
                                    @elseif(data_get($buku, 'categories.nama'))
                                        <span class="absolute top-2 right-2 bg-[#004d40] text-emerald-100 text-[9px] font-bold px-2 py-0.5 rounded shadow">
                                            {{ data_get($buku, 'categories.nama') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="space-y-1 text-center px-1">
                                    <h4 class="font-bold text-xs text-[#002820] line-clamp-2 leading-snug group-hover:text-[#00695c] transition">
                                        {{ data_get($buku, 'judul') }}
                                    </h4>
                                    <p class="text-[11px] text-emerald-900/70 line-clamp-1 font-medium">
                                        {{ data_get($buku, 'penulis') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full text-center py-10 text-emerald-200 text-xs w-full">
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
                <div class="bg-[#003d30] border border-emerald-700/50 rounded-xl p-5 hover:border-emerald-400 transition group cursor-pointer shadow-lg flex flex-col justify-between">
                    <div class="w-10 h-10 rounded-lg bg-emerald-800/60 flex items-center justify-center text-emerald-300 mb-3 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm md:text-base text-white">Agama & Pendidikan Islam</h4>
                        <p class="text-xs text-gray-300 mt-1">Al-Qur'an, Hadits, Fiqih, Aqidah</p>
                    </div>
                </div>

                <div class="bg-[#003d30] border border-emerald-700/50 rounded-xl p-5 hover:border-emerald-400 transition group cursor-pointer shadow-lg flex flex-col justify-between">
                    <div class="w-10 h-10 rounded-lg bg-emerald-800/60 flex items-center justify-center text-emerald-300 mb-3 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm md:text-base text-white">Sains & Teknologi</h4>
                        <p class="text-xs text-gray-300 mt-1">Komputer, Biologi, Fisika, Kimia</p>
                    </div>
                </div>

                <div class="bg-[#003d30] border border-emerald-700/50 rounded-xl p-5 hover:border-emerald-400 transition group cursor-pointer shadow-lg flex flex-col justify-between">
                    <div class="w-10 h-10 rounded-lg bg-emerald-800/60 flex items-center justify-center text-emerald-300 mb-3 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm md:text-base text-white">Bahasa & Sastra</h4>
                        <p class="text-xs text-gray-300 mt-1">Bahasa Arab, Inggris, Indonesia</p>
                    </div>
                </div>

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

            <div class="bg-[#b2c8c6] text-[#003d30] rounded-2xl p-6 shadow-xl space-y-4">
                <h3 class="text-lg font-bold border-b border-[#003d30]/20 pb-2">
                    Jam Operasional
                </h3>
                <div class="space-y-2 text-xs md:text-sm font-medium">
                    <div class="flex justify-between">
                        <span>Senin - Kamis</span>
                        <span class="font-bold">08.00 - 15.00 WITA</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Jum'at</span>
                        <span class="font-bold">08.00 - 11.30 WITA</span>
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

<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<!-- SCRIPT JS UNTUK AUTO-SLIDE BANNER (5 DETIK) -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const slides = document.querySelectorAll('.hero-slide');
        if (slides.length > 1) {
            let currentSlide = 0;
            setInterval(() => {
                slides[currentSlide].style.opacity = '0';
                currentSlide = (currentSlide + 1) % slides.length;
                slides[currentSlide].style.opacity = '1';
            }, 5000); // Ganti slide setiap 5 detik
        }


        // --- 2. BUKU SLIDER KATALOG ---
        const bookSlider = document.getElementById('bookSlider');
        const slideLeft = document.getElementById('slideLeft');
        const slideRight = document.getElementById('slideRight');

        if (slideLeft && slideRight && bookSlider) {
            slideLeft.addEventListener('click', () => {
                bookSlider.scrollBy({ left: -bookSlider.clientWidth, behavior: 'smooth' });
            });

            slideRight.addEventListener('click', () => {
                bookSlider.scrollBy({ left: bookSlider.clientWidth, behavior: 'smooth' });
            });
        }
    });
</script>
@endsection