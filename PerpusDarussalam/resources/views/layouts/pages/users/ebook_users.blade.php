@extends('layouts.pages.users.provider.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- HERO / HEADER HALAMAN -->
        <div class="bg-[#005a4e] rounded-2xl p-6 sm:p-10 mb-8 text-white shadow-lg flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold mb-2">Katalog E-Book Perpustakaan</h1>
                <p class="text-sm sm:text-base text-emerald-100 max-w-xl">
                    Jelajahi dan baca ratusan buku digital secara daring untuk mendukung proses belajar dan menambah wawasan Anda.
                </p>
            </div>
            <!-- Search Bar Mini di Header -->
            <div class="w-full md:w-auto">
                <form action="{{ route('user.ebook.index') }}" method="GET" class="flex items-center bg-white rounded-lg p-1 shadow-md">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul/penulis..." class="px-3 py-2 text-sm text-gray-700 focus:outline-none w-full md:w-64">
                    <button type="submit" class="bg-[#005a4e] text-white px-4 py-2 rounded-md hover:bg-[#004d40] text-sm font-semibold transition">
                        Cari
                    </button>
                </form>
            </div>
        </div>

        <!-- GRID DAFTAR E-BOOK -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
            @forelse($ebooks as $ebook)
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition duration-300 border border-gray-100 flex flex-col overflow-hidden">
                
                <!-- Cover Buku -->
                <div class="relative aspect-[3/4] bg-gray-200 overflow-hidden group">
                    @if($ebook->cover)
                        <img src="{{ asset('storage/' . $ebook->cover) }}" alt="{{ $ebook->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No Cover</div>
                    @endif
                    <!-- Badge Kategori -->
                    <span class="absolute top-2 left-2 bg-[#005a4e]/90 text-white text-[10px] font-semibold px-2 py-0.5 rounded shadow">
                        {{ $ebook->kategori ?? 'Umum' }}
                    </span>
                </div>

                <!-- Informasi Buku -->
                <div class="p-4 flex flex-col flex-grow justify-between">
                    <div>
                        <h3 class="font-bold text-gray-800 text-sm line-clamp-2 mb-1" title="{{ $ebook->judul }}">
                            {{ $ebook->judul }}
                        </h3>
                        <p class="text-xs text-gray-500 mb-2">{{ $ebook->penulis }}</p>
                    </div>

                    <!-- Tombol Aksi (Hanya Baca Online) -->
                    <div class="pt-2 border-t border-gray-100 mt-2">
                        <a href="{{ route('user.ebook.read', $ebook->id) }}" target="_blank" 
                           class="w-full block text-center bg-[#005a4e] hover:bg-[#004d40] text-white text-xs font-semibold py-2 rounded transition shadow-sm">
                            Baca E-Book
                        </a>
                    </div>
                </div>

            </div>
            @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                <p>Belum ada e-book yang tersedia atau hasil pencarian tidak ditemukan.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $ebooks->withQueryString()->links() }}
        </div>

    </div>
</div>
@endsection