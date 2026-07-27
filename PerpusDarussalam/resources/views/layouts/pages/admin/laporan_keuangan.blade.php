@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-white">
    
    <!-- Pemanggilan Sidebar -->
    @include('layouts.sidebar')

    <main class="flex-1 flex flex-col">
        <!-- Header Atas -->
        <header class="bg-white border-b border-gray-200 px-8 flex justify-end items-center shadow-sm h-20">
            <div class="flex items-center gap-4">
                <!-- Loncat / Notifikasi -->
                <button class="text-[#004d40] hover:text-[#003d30] transition p-1">
                    <span class="material-icons text-2xl">notifications_none</span>
                </button>
                
                <!-- Tombol LogOut -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-[#004d40] text-white px-4 py-1.5 rounded text-sm font-semibold hover:bg-[#003d30] transition">
                        LogOut
                    </button>
                </form>

                <!-- Logo Darussalam -->
                <img src="{{ asset('image/covers/darussalam.png') }}" alt="Logo Darussalam" class="h-12 object-contain ml-2">
            </div>
        </header>

        <!-- Area Isi Konten Laporan Keuangan -->
        <div class="p-8 space-y-8">
            
            <!-- Navigasi Tanggal & Kalender -->
            <div class="flex items-center gap-3">
                <!-- List Tanggal Horizontal -->
                <div class="inline-flex bg-[#004d40] rounded border border-[#004d40] overflow-hidden shadow-sm">
                    @foreach($dates as $d)
                        <a href="{{ route('laporan.keuangan', ['date' => $d['full_date']]) }}" 
                           class="px-4 py-2 text-sm font-bold border-r border-white/30 last:border-r-0 transition-colors duration-150 {{ $d['is_active'] ? 'bg-[#003d30] text-amber-300' : 'text-white hover:bg-white/10' }}">
                            {{ $d['day'] }}
                        </a>
                    @endforeach
                </div>

                <!-- Picker Kalender -->
                <form action="{{ route('laporan.keuangan') }}" method="GET" class="flex items-center">
                    <label for="date-picker-keuangan" class="cursor-pointer bg-[#004d40] text-white p-2.5 rounded hover:bg-[#003d30] transition shadow" title="Pilih Tanggal Lain">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </label>
                    <input type="date" id="date-picker-keuangan" name="date" class="hidden" onchange="this.form.submit()" value="{{ \Carbon\Carbon::parse($selectedDate)->format('Y-m-d') }}">
                </form>
            </div>

            <!-- Garis Pembatas Hijau -->
            <hr class="border-t-2 border-[#004d40]">

            <!-- Grid Card Ringkasan Laporan Keuangan -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Card 1: Pembuatan Kartu -->
                <div class="bg-[#b0bec5] rounded-lg p-6 text-center text-white shadow-md hover:shadow-lg transition-shadow">
                    <h3 class="font-bold text-lg mb-6 tracking-wide text-white">Pembuatan Kartu</h3>
                    <p class="text-4xl font-extrabold tracking-tight">
                        {{ number_format($pembuatanKartu ?? 0, 0, ',', '.') }}
                    </p>
                </div>

                <!-- Card 2: Kehilangan Kartu -->
                <div class="bg-[#b0bec5] rounded-lg p-6 text-center text-white shadow-md hover:shadow-lg transition-shadow">
                    <h3 class="font-bold text-lg mb-6 tracking-wide text-white">Kehilangan Kartu</h3>
                    <p class="text-4xl font-extrabold tracking-tight">
                        {{ number_format($kehilanganKartu ?? 0, 0, ',', '.') }}
                    </p>
                </div>

                <!-- Card 3: Keterlambatan Buku -->
                <div class="bg-[#b0bec5] rounded-lg p-6 text-center text-white shadow-md hover:shadow-lg transition-shadow">
                    <h3 class="font-bold text-lg mb-6 tracking-wide text-white">Keterlambatan Buku</h3>
                    <p class="text-4xl font-extrabold tracking-tight">
                        {{ number_format($keterlambatanBuku ?? 0, 0, ',', '.') }}
                    </p>
                </div>

            </div>

        </div>
    </main>
</div>
@endsection