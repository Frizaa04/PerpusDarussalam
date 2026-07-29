@extends('layouts.app')

@section('title', 'Laporan Absensi - Madrasah Darussalam')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Sidebar Navigasi -->
    @include('layouts.sidebar')

    <!-- Main Content -->
    <main class="flex-1 flex flex-col">

        <!-- Area Isi Konten Laporan -->
        <div class="p-8 space-y-8">
            
            <!-- Bar Navigasi Filter Tanggal, Mode & Tombol Aksi -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                
                <!-- Kiri: Tombol Mode (Harian / Mingguan), Label Bulan, Tanggal & Kalender -->
                <div class="flex flex-wrap items-center gap-3">
                    
                    <!-- Toggle Tombol Harian / Mingguan -->
                    <div class="inline-flex bg-[#004d40] p-1 rounded-lg border border-[#004d40] shadow-sm">
                        <a href="{{ route('laporan.pengunjung', ['date' => $selectedDate->format('Y-m-d'), 'mode' => 'harian']) }}" 
                           class="px-3 py-1.5 text-xs font-bold rounded transition-colors {{ $mode === 'harian' ? 'bg-amber-400 text-[#004d40]' : 'text-white hover:bg-white/10' }}">
                            Harian
                        </a>
                        <a href="{{ route('laporan.pengunjung', ['date' => $selectedDate->format('Y-m-d'), 'mode' => 'mingguan']) }}" 
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
                                <a href="{{ route('laporan.pengunjung', ['date' => $d['full_date'], 'mode' => 'harian']) }}" 
                                   class="px-3 py-2 text-sm font-bold border-r border-white/30 last:border-r-0 transition-colors duration-150 {{ $d['is_active'] ? 'bg-[#003d30] text-amber-300' : 'text-white hover:bg-white/10' }}">
                                    {{ $d['day'] }}
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-[#003d30] border border-amber-400/50 text-amber-300 px-4 py-2 rounded text-sm font-bold shadow-sm">
                            Rentang: {{ \Carbon\Carbon::parse($startOfWeekDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endOfWeekDate)->format('d M Y') }}
                        </div>
                    @endif

                    <!-- Date Picker Kalender Popup -->
                    <form action="{{ route('laporan.pengunjung') }}" method="GET" class="flex items-center relative">
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

                <!-- Kanan: Tombol Kembali & Unduh Excel -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('laporan.index') }}" 
                       class="bg-[#004d40] text-white px-4 py-2 rounded text-sm font-bold hover:bg-[#003d30] transition shadow flex items-center gap-2">
                        <span>&larr;</span> Kembali
                    </a>
                    
                    <a href="#" 
                       class="bg-[#004d40] text-white px-4 py-2 rounded text-sm font-bold hover:bg-[#003d30] transition shadow flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Unduh Excel
                    </a>
                </div>

            </div>

            <!-- Garis Pembatas Hijau -->
            <hr class="border-t-2 border-[#004d40]">

            <!-- Grid 6 Card Laporan Absensi (Format Foto 2) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Card 1: Total Pengunjung -->
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Total Pengunjung</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $totalPengunjung }}</p>
                </div>

                <!-- Card 2: Laki - Laki -->
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Laki - Laki</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $lakiLaki }}</p>
                </div>

                <!-- Card 3: Perempuan -->
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Perempuan</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $perempuan }}</p>
                </div>

                <!-- Card 4: Siswa -->
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Siswa</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $siswa }}</p>
                </div>

                <!-- Card 5: Guru -->
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Guru</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $guru }}</p>
                </div>

                <!-- Card 6: Umum -->
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Umum</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $umum }}</p>
                </div>

            </div>

        </div>
    </main>
</div>
@endsection