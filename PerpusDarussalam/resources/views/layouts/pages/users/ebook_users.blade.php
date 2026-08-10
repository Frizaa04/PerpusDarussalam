@extends('layouts.pages.users.provider.app')

@section('content')
    <div class="bg-[#004d40] min-h-screen text-white pb-16 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- HERO / HEADER HALAMAN & SEARCH BAR -->
            <div
                class="bg-[#00382e] rounded-2xl p-6 sm:p-10 text-white shadow-lg flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold mb-2">Katalog E-Book Perpustakaan</h1>
                    <p class="text-sm sm:text-base text-emerald-100 max-w-xl">
                        Jelajahi dan baca ratusan buku digital secara daring untuk mendukung proses belajar dan menambah
                        wawasan Anda.
                    </p>
                </div>
                <!-- Search Bar Mini di Header -->
                <div class="w-full md:w-auto">
                    <form action="{{ route('user.ebook.index') }}#katalog-ebook" method="GET"
                        class="flex items-center bg-white rounded-lg p-1 shadow-md">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari judul/penulis..."
                            class="px-3 py-2 text-sm text-gray-700 focus:outline-none w-full md:w-64">
                        <button type="submit"
                            class="bg-[#005a4e] text-white px-4 py-2 rounded-md hover:bg-[#004d40] text-sm font-semibold transition">
                            Cari
                        </button>
                    </form>
                </div>
            </div>


            <!-- SECTION DAFTAR E-BOOK (DIUBAH MENJADI GRID SEPERTI KATALOG BUKU) -->
            <section id="katalog-ebook"
                class="bg-[#00382e] text-white rounded-2xl p-6 md:p-8 shadow-2xl space-y-6 w-full relative border border-emerald-700/60">
                <div class="flex justify-between items-end border-b border-emerald-800/80 pb-4">
                    <div>
                        <h3 class="text-xl md:text-2xl font-bold tracking-tight text-white">
                            {{ request('search') ? 'Hasil Pencarian E-Book: "' . request('search') . '"' : 'Koleksi E-Book Terbaru' }}
                        </h3>
                        <p class="text-xs text-emerald-200/80 font-light mt-1">
                            {{ request('search') ? 'Menampilkan e-book yang sesuai dengan kata kunci' : 'Koleksi e-book terupdate dari Perpustakaan' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        @if (request('search'))
                            <a href="{{ route('user.ebook.index') }}#katalog-ebook"
                                class="text-xs text-emerald-300 font-semibold hover:underline mr-2">
                                Reset Pencarian
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Container Grid E-Book (Menggantikan Horizontal Slider) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4 w-full">
                    @if (isset($ebooks) && count($ebooks) > 0)
                        @foreach ($ebooks as $ebook)
                            <div
                                class="bg-[#002820] rounded-xl border border-emerald-700/60 p-3 shadow-lg hover:shadow-2xl hover:-translate-y-2 hover:border-emerald-500 transition-all duration-300 cursor-pointer group flex flex-col justify-between">
                                <div>
                                    <!-- Area Cover Buku -->
                                    <div
                                        class="w-full h-52 bg-[#001f19] rounded-lg overflow-hidden relative shadow-inner mb-3 border border-emerald-800/40">
                                        <img src="{{ $ebook->cover ? asset('storage/' . $ebook->cover) : 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=400&q=80' }}"
                                            alt="{{ $ebook->judul }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition duration-300">

                                        <!-- Badge Kategori -->
                                        <span
                                            class="absolute top-2 right-2 bg-[#004d40] text-emerald-100 text-[9px] font-bold px-2 py-0.5 rounded shadow border border-emerald-600/50">
                                            {{ $ebook->kategori ?? 'Umum' }}
                                        </span>
                                    </div>

                                    <!-- Informasi Judul & Penulis -->
                                    <div class="space-y-1 text-center px-1">
                                        <h4 class="font-bold text-xs text-white line-clamp-2 leading-snug group-hover:text-emerald-300 transition"
                                            title="{{ $ebook->judul }}">
                                            {{ $ebook->judul }}
                                        </h4>
                                        <p class="text-[11px] text-emerald-300/80 line-clamp-1 font-medium">
                                            {{ $ebook->penulis }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Tombol Aksi (Baca E-Book) di dalam Card -->
                                <div class="mt-3 pt-2 border-t border-emerald-800/80">
                                    <a href="{{ route('user.ebook.read', $ebook->id) }}" target="_blank"
                                        class="w-full block text-center bg-[#004d40] hover:bg-[#00695c] text-white text-[10px] font-bold py-1.5 rounded transition shadow border border-emerald-600/40">
                                        Baca E-Book
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-span-full text-center py-10 text-emerald-200 text-xs w-full">
                            E-book tidak ditemukan. Coba dengan kata kunci lain.
                        </div>
                    @endif
                </div>

                <div class="mt-4">
                    {{ $ebooks->links('vendor.pagination.custom') }}
                </div>
            </section>
        </div>
    </div>

    <!-- Script untuk Tombol Slider Kiri & Kanan -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.getElementById('bookSlider');
            const btnLeft = document.getElementById('slideLeft');
            const btnRight = document.getElementById('slideRight');

            if (slider && btnLeft && btnRight) {
                btnLeft.addEventListener('click', () => {
                    slider.scrollBy({
                        left: -300,
                        behavior: 'smooth'
                    });
                });

                btnRight.addEventListener('click', () => {
                    slider.scrollBy({
                        left: 300,
                        behavior: 'smooth'
                    });
                });
            }
        });
    </script>
@endsection
