@extends('layouts.pages.admin.provider.app')

@section('title', 'Laporan Perpustakaan - Madrasah Darussalam')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Sidebar Navigasi -->
    @include('layouts.pages.admin.provider.sidebar')

    <!-- Main Content -->
    <main class="flex-1 flex flex-col">

        <!-- Area Isi Konten Laporan -->
        <div class="p-8 space-y-8">
            
            <!-- Bar Navigasi Filter Tanggal & Mode -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                
                <!-- Kiri: Tombol Mode (Harian / Mingguan), Label Bulan, Tanggal & Kalender -->
                <div class="flex flex-wrap items-center gap-3">
                    
                    <!-- Toggle Tombol Harian / Mingguan -->
                    <div class="inline-flex bg-[#004d40] p-1 rounded-lg border border-[#004d40] shadow-sm">
                        <a href="{{ route('laporan.index', ['date' => $selectedDate->format('Y-m-d'), 'mode' => 'harian']) }}" 
                           class="px-3 py-1.5 text-xs font-bold rounded transition-colors {{ $mode === 'harian' ? 'bg-amber-400 text-[#004d40]' : 'text-white hover:bg-white/10' }}">
                            Harian
                        </a>
                        <a href="{{ route('laporan.index', ['date' => $selectedDate->format('Y-m-d'), 'mode' => 'mingguan']) }}" 
                           class="px-3 py-1.5 text-xs font-bold rounded transition-colors {{ $mode === 'mingguan' ? 'bg-amber-400 text-[#004d40]' : 'text-white hover:bg-white/10' }}">
                            Per Minggu
                        </a>
                    </div>

                    <!-- Label Bulan & Tahun -->
                    <div class="bg-[#004d40] text-amber-300 px-3.5 py-2 rounded text-sm font-bold shadow-sm">
                        {{ $monthYearLabel }}
                    </div>

                    <!-- Pilihan Tanggal / Rentang Minggu -->
                    @if($mode === 'harian')
                        <div class="inline-flex bg-[#004d40] rounded border border-[#004d40] overflow-hidden shadow-sm">
                            @foreach($dates as $d)
                                <a href="{{ route('laporan.index', ['date' => $d['full_date'], 'mode' => 'harian']) }}" 
                                   class="px-3 py-2 text-sm font-bold border-r border-white/30 last:border-r-0 transition-colors duration-150 {{ $d['is_active'] ? 'bg-[#003d30] text-amber-300' : 'text-white hover:bg-white/10' }}">
                                    {{ $d['day'] }}
                                </a>
                            @endforeach
                        </div>
                    @else
                        <!-- Badge Rentang Tanggal Minggu Ini (Senin - Minggu) -->
                        <div class="bg-[#003d30] border border-amber-400/50 text-amber-300 px-4 py-2 rounded text-sm font-bold shadow-sm">
                            Rentang: {{ \Carbon\Carbon::parse($startOfWeekDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endOfWeekDate)->format('d M Y') }}
                        </div>
                    @endif

                    <!-- Date Picker Kalender Popup -->
                    <form action="{{ route('laporan.index') }}" method="GET" class="flex items-center relative">
                        <input type="hidden" name="mode" value="{{ $mode }}">

                        <button type="button" 
                                onclick="document.getElementById('date-picker-laporan').showPicker()" 
                                class="bg-[#004d40] text-white p-2.5 rounded hover:bg-[#003d30] transition shadow flex items-center justify-center focus:outline-none" 
                                title="Pilih Tanggal Kalender">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </button>

                        <input type="date" 
                               id="date-picker-laporan" 
                               name="date" 
                               class="opacity-0 absolute pointer-events-none w-0 h-0" 
                               onchange="this.form.submit()" 
                               value="{{ $selectedDate->format('Y-m-d') }}">
                    </form>

                </div>
            </div>

            <!-- Garis Pembatas Hijau -->
            <hr class="border-t-2 border-[#004d40]">

            <!-- Grid 4 Card Statistik Laporan -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Card 1: Total Koleksi -->
                <a href="{{ route('laporan.koleksi') }}" 
                   class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Total Koleksi</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $totalKoleksi }}</p>
                </a>

                <!-- Card 2: Total Anggota -->
                <a href="{{ route('laporan.anggota') }}" 
                   class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Total Anggota</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $totalAnggota }}</p>
                </a>

                <!-- Card 3: Absensi -->
                <a href="{{ route('laporan.pengunjung', ['date' => $selectedDate->format('Y-m-d')]) }}" 
                   class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Absensi</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $pengunjung }}</p>
                </a>

                <!-- Card 4: Peminjaman -->
                <a href="{{ route('laporan.peminjaman', ['date' => $selectedDate->format('Y-m-d')]) }}" 
                   class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Peminjaman</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $peminjaman }}</p>
                </a>

            </div>

        </div>
    </main>
</div>
@endsection