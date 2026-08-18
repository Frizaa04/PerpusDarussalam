@extends('layouts.pages.admin.provider.app')

@section('title', 'Laporan Perpustakaan & Keuangan - Madrasah Darussalam')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    <!-- Sidebar Navigasi -->
    @include('layouts.pages.admin.provider.sidebar')

    <!-- Main Content -->
    <main class="flex-1 flex flex-col">
        <div class="p-8 space-y-8">
            
            <!-- SECTION 1: LAPORAN PERPUSTAKAAN (EX-LAPORAN.BLADE) -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3">

                    {{-- Tombol Harian / Per Minggu --}}
                    <div class="inline-flex bg-[#004d40] p-1 rounded-lg border border-[#004d40] shadow-sm">

                        <a href="{{ route('laporan.index', [
                            'date' => $selectedDate->format('Y-m-d'),
                            'mode' => 'harian',
                            'category' => $category
                        ]) }}"
                        class="px-3 py-1.5 text-xs font-bold rounded transition-colors {{ $mode === 'harian' ? 'bg-amber-400 text-[#004d40]' : 'text-white hover:bg-white/10' }}">
                            Harian
                        </a>

                        <a href="{{ route('laporan.index', [
                            'date' => $selectedDate->format('Y-m-d'),
                            'mode' => 'mingguan',
                            'category' => $category
                        ]) }}"
                        class="px-3 py-1.5 text-xs font-bold rounded transition-colors {{ $mode === 'mingguan' ? 'bg-amber-400 text-[#004d40]' : 'text-white hover:bg-white/10' }}">
                            Per Minggu
                        </a>

                        <a href="{{ route('laporan.index', [
                            'date' => $selectedDate->format('Y-m-d'),
                            'mode' => 'bulanan',
                            'category' => $category
                        ]) }}"
                        class="px-3 py-1.5 text-xs font-bold rounded transition-colors {{ $mode === 'bulanan' ? 'bg-amber-400 text-[#004d40]' : 'text-white hover:bg-white/10' }}">
                            Bulanan
                        </a>

                    </div>

                    {{-- Nama Bulan dan Tahun --}}
                    <div class="bg-[#004d40] text-amber-300 px-3.5 py-2 rounded text-sm font-bold shadow-sm">
                        {{ $monthYearLabel }}
                    </div>

                    @if($mode === 'harian')
                        {{-- Pilihan Tanggal --}}
                        <div class="inline-flex bg-[#004d40] rounded border border-[#004d40] overflow-hidden shadow-sm">
                            @foreach($dates as $d)
                                <a href="{{ route('laporan.index', [
                                    'date' => $d['full_date'],
                                    'mode' => 'harian',
                                    'category' => $category
                                ]) }}"
                                class="px-3 py-2 text-sm font-bold border-r border-white/30 last:border-r-0 transition-colors duration-150 {{ 
                                    $d['is_active']
                                        ? 'bg-[#003d30] text-amber-300'
                                        : 'text-white hover:bg-white/10'
                                }}">
                                    {{ $d['day'] }}
                                </a>
                            @endforeach
                        </div>
                    @elseif($mode === 'mingguan')
                        {{-- Rentang Minggu --}}
                        <div class="bg-[#003d30] border border-amber-400/50 text-amber-300 px-4 py-2 rounded text-sm font-bold shadow-sm">
                            Rentang:
                            {{ \Carbon\Carbon::parse($startDate)->format('d M') }}
                            -
                            {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        </div>
                    @elseif($mode === 'bulanan')
                        {{-- Rentang Bulan --}}
                        <div class="bg-[#003d30] border border-amber-400/50 text-amber-300 px-4 py-2 rounded text-sm font-bold shadow-sm">
                            Bulan:
                            {{ $selectedDate->translatedFormat('F Y') }}
                        </div>
                    @endif

                    {{-- TOMBOL KALENDER DINAMIS (FOTO 1) --}}
                    <div class="relative inline-block">
                        <input 
                            type="date" 
                            id="datePickerMain" 
                            class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10"
                            value="{{ $selectedDate->format('Y-m-d') }}"
                            onchange="changeMainDate(this.value)"
                        >
                        <button type="button" class="bg-[#004d40] hover:bg-[#003d30] text-white p-2 rounded-lg flex items-center justify-center transition-colors shadow border border-[#004d40]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>

                </div>
            </div>

            <h2 class="text-2xl font-bold text-[#004d40] mt-6 mb-4">
                Laporan Manajemen Perpustakaan
            </h2>
            <hr class="border-t-2 border-[#004d40]">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <a href="{{ route('laporan.koleksi', [
                    'date' => $selectedDate->format('Y-m-d'),
                    'mode' => $mode
                ]) }}" class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Total Koleksi</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $totalKoleksi }}</p>
                </a>

                <a href="{{ route('laporan.anggota', [
                    'date' => $selectedDate->format('Y-m-d'),
                    'mode' => $mode
                ]) }}" class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Total Anggota</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $totalAnggota }}</p>
                </a>

                <a href="{{ route('laporan.pengunjung', [
                    'date' => $selectedDate->format('Y-m-d'),
                    'mode' => $mode
                ]) }}" class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Absensi</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $pengunjung }}</p>
                </a>

                <a href="{{ route('laporan.peminjaman', [
                    'date' => $selectedDate->format('Y-m-d'),
                    'mode' => $mode
                ]) }}" class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Peminjaman</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $peminjaman }}</p>
                </a>
            </div>

            <!-- SECTION 2: LAPORAN KEUANGAN (EX-LAPORAN_KEUANGAN.BLADE) -->
            <div class="pt-8 border-t-4 border-[#004d40]">
                @if (!$category)
                    <div class="flex flex-wrap items-center justify-between gap-4 w-full mb-6">
                        <h2 class="text-2xl font-bold text-[#004d40]">Laporan Keuangan</h2>
                        <div class="flex items-center gap-3">
                            <div class="inline-flex items-center justify-center bg-[#004d40] text-amber-300 px-4 py-2.5 rounded font-bold text-sm shadow">
                                Total: Rp. {{ number_format($totalSemua, 0, ',', '.') }}
                            </div>
                            <a href="{{ route('laporan.transaksi.export', request()->all()) }}" class="inline-flex items-center justify-center gap-2 bg-[#004d40] text-white px-4 py-2.5 rounded font-bold hover:bg-[#003d30] transition shadow text-sm">Unduh Excel</a>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">

                        <a href="{{ route('laporan.index', [
                            'date' => $selectedDate->format('Y-m-d'),
                            'mode' => $mode,
                            'category' => 'pembuatan_kartu'
                        ]) }}" class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                            <h3 class="text-sm font-bold text-white/90 tracking-wide">Pembuatan Kartu</h3>
                            <p class="text-4xl font-extrabold mt-4">{{ $pembuatanKartuCount }}</p>
                        </a>

                        <a href="{{ route('laporan.index', [
                            'date' => $selectedDate->format('Y-m-d'),
                            'mode' => $mode,
                            'category' => 'kehilangan_kartu'
                        ]) }}" class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                            <h3 class="text-sm font-bold text-white/90 tracking-wide">Kehilangan Kartu</h3>
                            <p class="text-4xl font-extrabold mt-4">{{ $kehilanganKartuCount }}</p>
                        </a>

                        <a href="{{ route('laporan.index', [
                            'date' => $selectedDate->format('Y-m-d'),
                            'mode' => $mode,
                            'category' => 'denda_keterlambatan'
                        ]) }}" class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                            <h3 class="text-sm font-bold text-white/90 tracking-wide">Keterlambatan Buku</h3>
                            <p class="text-4xl font-extrabold mt-4">{{ $keterlambatanBukuCount }}</p>
                        </a>

                        <a href="{{ route('laporan.index', [
                            'date' => $selectedDate->format('Y-m-d'),
                            'mode' => $mode,
                            'category' => 'kehilangan_buku'
                        ]) }}" class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                            <h3 class="text-sm font-bold text-white/90 tracking-wide">Kehilangan Buku</h3>
                            <p class="text-4xl font-extrabold mt-4">{{ $kehilanganBukuCount }}</p>
                        </a>

                        <!-- 5. Perpanjang Kartu -->
                        <a href="{{ route('laporan.index', [
                            'date' => $selectedDate->format('Y-m-d'),
                            'mode' => $mode,
                            'category' => 'perpanjang_kartu'
                        ]) }}" class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                            <h3 class="text-sm font-bold text-white/90 tracking-wide">Perpanjang Kartu</h3>
                            <p class="text-4xl font-extrabold mt-4">{{ $perpanjangKartuCount ?? 0 }}</p>
                        </a>

                    </div>
                @else
                    {{-- Tombol Kembali ke Laporan Utama --}}
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('laporan.index', [
                            'date' => $selectedDate->format('Y-m-d'),
                            'mode' => $mode
                        ]) }}"
                        class="inline-flex items-center gap-2 bg-[#004d40] text-white px-4 py-2.5 rounded font-bold hover:bg-[#003d30] transition shadow text-sm">

                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>

                            Kembali ke Laporan
                        </a>
                    </div>

                    <!-- Tabel Detail -->
                    <div class="bg-[#b0bec5] p-6 rounded shadow-md border border-gray-300">
                        <h2 class="text-xl font-bold text-white mb-4">
                            @if ($category === 'pembuatan_kartu')
                                Tabel Daftar Transaksi Pembuatan Kartu
                            @elseif($category === 'kehilangan_kartu')
                                Tabel Daftar Transaksi Kehilangan Kartu
                            @elseif($category === 'kehilangan_buku')
                                Tabel Daftar Transaksi Kehilangan Buku
                            @else
                                Tabel Daftar Transaksi Keterlambatan Buku
                            @endif
                        </h2>

                        <div class="overflow-x-auto rounded border border-white/30">
                            <table class="min-w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-[#004d40] text-white divide-x divide-white/30">
                                        <th class="p-3 text-sm font-bold">No</th>
                                        <th class="p-3 text-sm font-bold">Nama</th>
                                        <th class="p-3 text-sm font-bold">Nominal</th>
                                        <th class="p-3 text-sm font-bold">Tanggal</th>
                                        <th class="p-3 text-sm font-bold">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="text-white divide-y divide-white/30">
                                    @forelse($dataList as $key => $item)
                                        <tr class="divide-x divide-white/30 hover:bg-white/10 transition">
                                            <td class="p-3 text-sm font-medium">
                                                {{ sprintf('%02d', $dataList->firstItem() + $key) }}
                                            </td>
                                            <td class="p-3 text-sm font-medium">
                                                {{ $item->user->name ?? 'Pemustaka' }}
                                            </td>
                                            <td class="p-3 text-sm font-medium">
                                                Rp. {{ number_format($item->nominal, 0, ',', '.') }}
                                            </td>
                                            <td class="p-3 text-sm font-medium">
                                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                            </td>
                                            <td class="p-3 text-sm font-medium italic">
                                                {{ $item->keterangan ?? '...' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5"
                                                class="p-6 text-center text-sm font-semibold text-white/90">
                                                Belum ada data transaksi untuk filter ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $dataList->appends(['date' => $selectedDate->format('Y-m-d'), 'mode' => $mode, 'category' => $category, 'search' => $search])->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </main>
</div>

{{-- Script JavaScript untuk mengubah tanggal via kalender --}}
<script>
    function changeMainDate(selectedDate) {
        if (!selectedDate) return;
        
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('date', selectedDate);
        
        // Pertahankan parameter mode & category jika ada
        if (!urlParams.has('mode')) {
            urlParams.set('mode', '{{ $mode }}');
        }
        
        window.location.href = window.location.pathname + '?' + urlParams.toString();
    }
</script>
@endsection