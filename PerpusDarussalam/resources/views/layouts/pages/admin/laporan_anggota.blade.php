@extends('layouts.pages.admin.provider.app')

@section('title', 'Laporan Anggota - Madrasah Darussalam')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Sidebar Navigasi -->
    @include('layouts.pages.admin.provider.sidebar')

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col">

        <!-- Area Isi Laporan -->
        <div class="p-8 space-y-8">
            
            <!-- Bilah Navigasi Tanggal & Tombol Aksi -->
            <div class="flex flex-wrap items-center justify-between gap-4">

                <!-- Kanan: Tombol Kembali, Import Excel, Unduh Excel -->
                <div class="flex items-center gap-3">
                    <!-- Tombol Kembali -->
                    <a href="{{ route('laporan.index', ['date' => $selectedDate->format('Y-m-d'), 'mode' => $mode]) }}" class="inline-flex items-center gap-2 bg-[#004d40] text-white px-4 py-2.5 rounded font-bold hover:bg-[#003d30] transition shadow text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>

                    <!-- Tombol Unduh Excel -->
                    <a href="{{ route('laporan.anggota.export', ['date' => $selectedDate->format('Y-m-d'), 'mode' => $mode]) }}" 
                    class="bg-[#004d40] text-white px-4 py-2.5 rounded hover:bg-[#003d30] transition shadow flex items-center gap-2 text-sm font-bold" 
                    title="Unduh Laporan Excel">
                        <span class="material-icons text-xl">file_download</span>
                        <span>Unduh Excel</span>
                    </a>
                </div>
            </div>

            <!-- Bagian Alert Flash Messages -->
            @if(session('success'))
                <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded shadow-sm text-sm font-medium flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-icons text-emerald-600">check_circle</span>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-800 hover:text-emerald-900 font-bold">&times;</button>
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-amber-100 border-l-4 border-amber-500 text-amber-800 p-4 rounded shadow-sm text-sm font-medium flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-icons text-amber-600">warning</span>
                        <span>{{ session('warning') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-amber-800 hover:text-amber-900 font-bold">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-100 border-l-4 border-rose-500 text-rose-800 p-4 rounded shadow-sm text-sm font-medium flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-icons text-rose-600">error</span>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-800 hover:text-rose-900 font-bold">&times;</button>
                </div>
            @endif

            <!-- Garis Pembatas Hijau -->
            <hr class="border-t-2 border-[#004d40]">

            <!-- Grid 6 Card Detail Anggota -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Total Anggota</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $totalAnggota }}</p>
                </div>

                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Laki - Laki</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $lakiLaki }}</p>
                </div>

                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Perempuan</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $perempuan }}</p>
                </div>

                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Siswa</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $siswa }}</p>
                </div>

                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Guru</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $guru }}</p>
                </div>
            </div>

        </div>
    </main>
</div>
@endsection