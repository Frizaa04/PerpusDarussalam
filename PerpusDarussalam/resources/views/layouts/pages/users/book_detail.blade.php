@extends('layouts.pages.users.provider.app') 

@section('content')
<!-- MENGUBAH BACKGROUND HALAMAN JADI HIJAU TUA SAMA SEPERTI HOME (min-h-screen & bg-[#002820]) -->
<div class="min-h-screen bg-[#002820] py-8 px-4">
    <div class="max-w-6xl mx-auto space-y-8">
        
        <!-- Tombol Kembali -->
        <div>
            <a href="{{ route('user.home') }}" class="inline-flex items-center gap-2 bg-[#00382e] text-emerald-200 hover:text-white px-4 py-2 rounded-xl border border-emerald-700/60 text-xs font-semibold shadow transition hover:bg-emerald-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Beranda
            </a>
        </div>

        <!-- Card Utama Detail Buku (Dibuat Beda Warna Dengan BG Utama: bg-[#004d40] agar Pop-out) -->
        <div class="bg-[#004d40] text-white rounded-2xl p-6 md:p-10 shadow-2xl border border-emerald-600/50 grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
            
            <!-- Kolom Cover Buku -->
            <div class="w-full">
                <div class="w-full h-80 md:h-[400px] bg-[#002820] rounded-xl overflow-hidden shadow-inner border border-emerald-800/60 relative group">
                    <img src="{{ data_get($book, 'cover') ? asset('storage/' . data_get($book, 'cover')) : 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=400&q=80' }}" 
                         alt="{{ data_get($book, 'judul') }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    
                    @if(data_get($book, 'categories.nama'))
                        <span class="absolute top-3 right-3 bg-[#002820] text-emerald-100 text-xs font-bold px-3 py-1 rounded-full shadow border border-emerald-600/50">
                            {{ data_get($book, 'categories.nama') }}
                        </span>
                    @endif
                </div>

                <!-- Status Stok (Diubah ke bg-[#00382e] agar beda dengan Card & BG Utama) -->
                <div class="mt-4 text-center bg-[#00382e] p-3 rounded-xl border border-emerald-700/60">
                    <span class="text-xs text-emerald-200/80">Ketersediaan Stok Fisik:</span>
                    <p class="text-sm font-bold {{ $book->stok > 0 ? 'text-emerald-400' : 'text-red-400' }}">
                        {{ $book->stok > 0 ? $book->stok . ' Tersedia' : 'Stok Habis' }}
                    </p>
                </div>
            </div>

            <!-- Kolom Informasi Detail Buku -->
            <div class="md:col-span-2 space-y-6">
                <div>
                    <span class="text-xs font-semibold text-emerald-300 uppercase tracking-wider">Kode Buku: {{ data_get($book, 'kode_buku') }}</span>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-white mt-1 leading-tight">
                        {{ data_get($book, 'judul') }}
                    </h1>
                    <p class="text-sm text-emerald-200/80 font-medium mt-1">
                        Penulis: <span class="text-white font-semibold">{{ data_get($book, 'penulis') }}</span>
                    </p>
                </div>

                <!-- Informasi Spesifikasi (Grid) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 py-4 border-y border-emerald-600/50 text-xs">
                    <div>
                        <span class="text-emerald-300/80 block">Penerbit</span>
                        <span class="font-bold text-white text-sm">{{ data_get($book, 'penerbit', '-') }}</span>
                    </div>
                    <div>
                        <span class="text-emerald-300/80 block">Tahun Terbit</span>
                        <span class="font-bold text-white text-sm">{{ data_get($book, 'tahun_terbit', '-') }}</span>
                    </div>
                    <div>
                        <span class="text-emerald-300/80 block">ISBN</span>
                        <span class="font-bold text-white text-sm">{{ data_get($book, 'isbn', '-') }}</span>
                    </div>
                    <div>
                        <span class="text-emerald-300/80 block">Lokasi Rak</span>
                        <span class="font-bold text-white text-sm">{{ data_get($book, 'rak', '-') }}</span>
                    </div>
                    <div>
                        <span class="text-emerald-300/80 block">Tanggal Pembelian</span>
                        <span class="font-bold text-white text-sm">{{ data_get($book, 'tanggal_pembelian', '-') }}</span>
                    </div>
                </div>

                <!-- Deskripsi Buku -->
                <div class="space-y-2">
                    <h3 class="text-sm font-bold text-emerald-300 uppercase tracking-wide">Deskripsi Buku</h3>
                    <p class="text-xs md:text-sm text-emerald-100/90 leading-relaxed font-light whitespace-pre-line">
                        {{ data_get($book, 'deskripsi') ?: 'Tidak ada deskripsi untuk buku ini.' }}
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection