@extends('layouts.app')

@section('title', 'Laporan Peminjaman - Madrasah Darussalam')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Sidebar Navigasi -->
    @include('layouts.sidebar')

    <!-- Main Content -->
    <main class="flex-1 flex flex-col">

        <!-- Content -->
        <div class="p-8 space-y-8">
            
            <!-- Navigasi Tanggal & Icon Print -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="inline-flex bg-[#004d40] rounded border border-[#004d40] overflow-hidden shadow-sm">
                        @foreach($dates as $d)
                            <a href="{{ route('laporan.peminjaman', ['date' => $d['full_date']]) }}" 
                               class="px-4 py-2 text-sm font-bold border-r border-white/30 last:border-r-0 transition-colors duration-150 {{ $d['is_active'] ? 'bg-[#003d30] text-amber-300' : 'text-white hover:bg-white/10' }}">
                                {{ $d['day'] }}
                            </a>
                        @endforeach
                    </div>

                    <!-- Date Picker -->
                    <form action="{{ route('laporan.peminjaman') }}" method="GET" class="flex items-center">
                        <label for="date-picker" class="cursor-pointer bg-[#004d40] text-white p-2.5 rounded hover:bg-[#003d30] transition shadow" title="Pilih Tanggal Lain">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </label>
                        <input type="date" id="date-picker" name="date" class="hidden" onchange="this.form.submit()" value="{{ $selectedDate->format('Y-m-d') }}">
                    </form>
                </div>

                <!-- Tombol Cetak/Print -->
                <button onclick="window.print()" class="bg-[#004d40] text-white p-2.5 rounded hover:bg-[#003d30] transition shadow" title="Cetak Laporan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </button>
            </div>

            <!-- Garis Pembatas Hijau -->
            <hr class="border-t-2 border-[#004d40]">

            <!-- Grid 4 Card Detail Peminjaman (Desain Foto 2) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Card Total Peminjaman -->
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Total Peminjaman</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $totalPeminjaman }}</p>
                </div>

                <!-- Card Sedang di Pinjam -->
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Sedang di Pinjam</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $sedangDipinjam }}</p>
                </div>

                <!-- Card Sudah di Kembalikan -->
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Sudah di Kembalikan</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $sudahDikembalikan }}</p>
                </div>

                <!-- Card Terlambat/Belum Kembali -->
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Terlambat/Belum Kembali</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $terlambat }}</p>
                </div>

            </div>

        </div>
    </main>
</div>
@endsection