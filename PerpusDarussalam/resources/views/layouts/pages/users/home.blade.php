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
                    <div
                        class="hero-slide absolute inset-0 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }} transition-opacity duration-1000 ease-in-out">
                        <!-- PASTIKAN KELAS W-FULL H-FULL OBJECT-COVER ADA PADA TAG IMG INI -->
                        <img src="{{ asset('storage/' . $banner->image) }}" alt="Banner Perpustakaan {{ $index + 1 }}"
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
                <!-- Tambahkan #katalog-buku pada URL action-nya -->
                <form action="{{ route('user.home') }}#katalog-buku" method="GET" class="max-w-2xl mx-auto">
                    <div
                        class="relative flex items-center bg-white rounded-full p-1.5 shadow-2xl border border-emerald-500/30">
                        <div class="pl-4 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari judul buku, pengarang, atau kata kunci..."
                            class="w-full pl-3 pr-4 py-2.5 text-sm text-gray-800 bg-transparent focus:outline-none placeholder-gray-400 font-medium">
                        <button type="submit"
                            class="bg-[#003d30] hover:bg-[#002820] text-white px-6 py-2.5 rounded-full text-xs font-bold transition duration-200 shrink-0">
                            Cari Buku
                        </button>
                    </div>
                </form>
            </div>

        </section>

        <div class="max-w-6xl mx-auto px-4 space-y-12">

            <!-- 2. HASIL PENCARIAN / KATALOG BUKU TERBARU -->
            <section id="katalog-buku" class="bg-[#b2c8c6] text-white rounded-2xl p-6 md:p-8 shadow-2xl space-y-6 w-full relative border border-emerald-700/60">
                <div class="flex justify-between items-end border-b border-emerald-800/80 pb-4">
                    <div>
                        <h3 class="text-xl md:text-2xl font-bold tracking-tight text-white">
                            {{ request('search') ? 'Hasil Pencarian: "' . request('search') . '"' : 'Koleksi Buku Terbaru' }}
                        </h3>
                        <p class="text-xs text-emerald-200/80 font-light mt-1">
                            {{ request('search') ? 'Menampilkan buku yang sesuai dengan kata kunci' : 'Koleksi buku terupdate dari Perpustakaan' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        @if (request('search'))
                            <a href="{{ route('user.home') }}#katalog-buku"
                                class="text-xs text-emerald-300 font-semibold hover:underline mr-2">
                                Reset Pencarian
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Container Grid Buku -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-5 py-4 px-1 w-full">
                    @if (isset($books) && count($books) > 0)
                        @foreach ($books as $buku)
                            <a href="{{ route('user.book.show', $buku->id) }}"
                                class="bg-[#002820] rounded-xl border border-emerald-700/60 p-3 shadow-lg hover:shadow-2xl hover:-translate-y-2 hover:border-emerald-500 transition-all duration-300 cursor-pointer group flex flex-col justify-between">
                                <div>
                                    <div class="w-full h-52 bg-[#001f19] rounded-lg overflow-hidden relative shadow-inner mb-3 border border-emerald-800/40">
                                        <img src="{{ data_get($buku, 'cover') ? asset('storage/' . data_get($buku, 'cover')) : 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=400&q=80' }}"
                                            alt="{{ data_get($buku, 'judul') }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition duration-300">

                                        @if (data_get($buku, 'categories.nama'))
                                            <span class="absolute top-2 right-2 bg-[#004d40] text-emerald-100 text-[9px] font-bold px-2 py-0.5 rounded shadow border border-emerald-600/50">
                                                {{ data_get($buku, 'categories.nama') }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="space-y-1 text-center px-1">
                                        <h4 class="font-bold text-xs text-white line-clamp-2 leading-snug group-hover:text-emerald-300 transition">
                                            {{ data_get($buku, 'judul') }}
                                        </h4>
                                        <p class="text-[11px] text-emerald-300/80 line-clamp-1 font-medium">
                                            {{ data_get($buku, 'penulis') }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Tambahan kecil petunjuk teks agar user tahu card bisa diklik -->
                                <div class="mt-3 pt-2 border-t border-emerald-800/80 text-center">
                                    <span class="text-[10px] font-bold text-emerald-300 group-hover:underline">
                                        Lihat Detail &rarr;
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <div class="col-span-full text-center py-10 text-emerald-200 text-xs w-full">
                            Buku tidak ditemukan. Coba dengan kata kunci lain.
                        </div>
                    @endif
                </div>

                <div class="mt-4">
                    {{ $books->links('vendor.pagination.custom') }}
                </div>
            </section>

        <!-- 3. SECTION KUTIPAN AYAT AL-QUR'AN -->
        <section class="bg-[#b2c8c6] text-[#003d30] rounded-2xl p-6 md:p-8 shadow-xl text-center space-y-4 border border-white/20">
            <div class="inline-block bg-[#004d40] text-white text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">
                Mutiara Hadis
            </div>

            <h2 class="text-xl md:text-2xl font-bold">HR. Muslim, No. 2699</h2>

            <!-- Teks Arab -->
            <p class="text-2xl md:text-3xl font-serif leading-loose text-[#002820]" dir="rtl">
                مَنْ سَلَكَ طَرِيقًا يَلْتَمِسُ فِيهِ عِلْمًا سَهَّلَ اللَّهُ لَهُ بِهِ طَرِيقًا إِلَى الْجَنَّةِ
            </p>

            <!-- Terjemahan -->
            <p class="text-base md:text-lg italic font-medium leading-relaxed max-w-3xl mx-auto">
                "Barangsiapa menempuh suatu jalan untuk mencari ilmu, maka Allah akan memudahkan baginya jalan menuju surga."
            </p>
        </section>


        <!-- 4. INFORMATION & INFORMASI JAM OPERASIONAL -->
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
                        <span class="font-bold">08.00 - 15.30 WITA</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Sabtu, Minggu & Hari Libur</span>
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // --- 1. BANNER AUTO-SLIDE ---
            const slides = document.querySelectorAll('.hero-slide');
            if (slides.length > 1) {
                let currentSlide = 0;
                setInterval(() => {
                    slides[currentSlide].style.opacity = '0';
                    currentSlide = (currentSlide + 1) % slides.length;
                    slides[currentSlide].style.opacity = '1';
                }, 5000);
            }

            // --- 2. BUKU SLIDER KATALOG ---
            const bookSlider = document.getElementById('bookSlider');
            const btnPrevBook = document.getElementById('btnPrevBook');
            const btnNextBook = document.getElementById('btnNextBook');

            if (btnPrevBook && btnNextBook && bookSlider) {
                btnPrevBook.addEventListener('click', () => {

                    bookSlider.scrollBy({
                        left: -bookSlider.clientWidth * 0.75,
                        behavior: 'smooth'
                    });
                });

                btnNextBook.addEventListener('click', () => {

                    bookSlider.scrollBy({
                        left: bookSlider.clientWidth * 0.75,
                        behavior: 'smooth'
                    });
                });
            }
        });
    </script>
@endsection
