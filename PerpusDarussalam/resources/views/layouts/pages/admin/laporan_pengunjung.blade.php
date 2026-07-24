@extends('layouts.app')

@section('title', 'Laporan Pengunjung - Madrasah Darussalam')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col">
        
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-8 flex justify-end items-center shadow-sm h-20 gap-4">
            <button class="text-[#004d40] hover:opacity-80 transition" title="Notifikasi">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                </svg>
            </button>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-[#004d40] text-white px-4 py-1.5 rounded text-sm font-bold hover:bg-[#003d30] transition shadow">
                    LogOut
                </button>
            </form>

            <div class="flex items-center h-full ml-2">
                <img src="{{ asset('image/covers/darussalam.png') }}" alt="Madrasah Darussalam IBS" class="h-14 object-contain">
            </div>
        </header>

        <!-- Area Content -->
        <div class="p-8 space-y-8">
            
            <!-- Filter Tanggal & Action Buttons -->
            <div class="flex items-center justify-between">
                <!-- Filter Tanggal -->
                <div class="flex items-center gap-3">
                    <div class="inline-flex bg-[#004d40] rounded border border-[#004d40] overflow-hidden shadow-sm">
                        @foreach($dates as $d)
                            <a href="{{ route('laporan.pengunjung', ['date' => $d['full_date']]) }}" 
                               class="px-4 py-2 text-sm font-bold border-r border-white/30 last:border-r-0 transition-colors duration-150 {{ $d['is_active'] ? 'bg-[#003d30] text-amber-300' : 'text-white hover:bg-white/10' }}">
                                {{ $d['day'] }}
                            </a>
                        @endforeach
                    </div>

                    <!-- Tombol Kalender -->
                    <form action="{{ route('laporan.pengunjung') }}" method="GET" class="relative inline-block">
                        <div class="bg-[#004d40] text-white p-2.5 rounded shadow hover:bg-[#003d30] transition flex items-center justify-center relative cursor-pointer">
                            <svg class="w-5 h-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <input type="date" 
                                   name="date" 
                                   value="{{ $selectedDate->format('Y-m-d') }}" 
                                   onchange="this.form.submit()" 
                                   class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" 
                                   title="Pilih Tanggal">
                        </div>
                    </form>
                </div>

                <!-- Tombol Kembali & Unduh -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('laporan.index') }}" class="inline-flex items-center gap-2 bg-[#004d40] text-white px-4 py-2.5 rounded font-bold hover:bg-[#003d30] transition shadow text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>

                    <a href="{{ route('laporan.pengunjung.export', ['date' => $selectedDate->format('Y-m-d')]) }}" class="bg-[#004d40] text-white px-4 py-2.5 rounded hover:bg-[#003d30] transition shadow flex items-center gap-2 text-sm font-bold" title="Unduh Excel">
                        <span class="material-icons text-xl">file_download</span>
                        <span>Unduh Excel</span>
                    </a>
                </div>
            </div>

            <hr class="border-t-2 border-[#004d40]">

            <!-- Grid 6 Card Statistik Pengunjung -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Total Anggota / Pengunjung -->
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Total Anggota</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $totalPengunjung }}</p>
                </div>

                <!-- Laki - Laki -->
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Laki - Laki</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $lakiLaki }}</p>
                </div>

                <!-- Perempuan -->
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Perempuan</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $perempuan }}</p>
                </div>

                <!-- Siswa -->
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Siswa</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $siswa }}</p>
                </div>

                <!-- Guru -->
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Guru</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $guru }}</p>
                </div>

                <!-- Umum -->
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Umum</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $umum }}</p>
                </div>

            </div>

        </div>
    </main>
</div>
@endsection