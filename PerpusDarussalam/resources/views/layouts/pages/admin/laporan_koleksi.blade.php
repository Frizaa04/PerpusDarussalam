@extends('layouts.pages.admin.provider.app')

@section('title', 'Laporan Total Koleksi - Madrasah Darussalam')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Sidebar Navigasi -->
    @include('layouts.pages.admin.provider.sidebar')

    <!-- Main Content -->
    <main class="flex-1 flex flex-col">

        <!-- Laporan Total Koleksi -->
        <div class="p-8 space-y-8">
            
            <!-- Bilah Navigasi Tanggal, Mode & Tombol Aksi -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                
                <!-- Kiri: Filter Mode & Tanggal -->
                <div class="flex flex-wrap items-center gap-3">
                    
                        <!-- Toggle Tombol Harian / Mingguan -->
                        <div class="inline-flex bg-[#004d40] p-1 rounded-lg border border-[#004d40] shadow-sm">
                            <a href="{{ route('laporan.koleksi', [
                                'date' => $selectedDate->format('Y-m-d'),
                                'mode' => 'bulanan'
                            ]) }}"
                            class="px-3 py-1.5 text-xs font-bold rounded transition-colors
                                {{ $mode === 'bulanan'
                                    ? 'bg-amber-400 text-[#004d40]'
                                    : 'text-white hover:bg-white/10' }}">
                                Bulanan
                            </a>

                            <a href="{{ route('laporan.koleksi', [
                                'date' => $selectedDate->format('Y-m-d'),
                                'mode' => 'tahunan'
                            ]) }}"
                            class="px-3 py-1.5 text-xs font-bold rounded transition-colors
                                {{ $mode === 'tahunan'
                                    ? 'bg-amber-400 text-[#004d40]'
                                    : 'text-white hover:bg-white/10' }}">
                                Tahunan
                            </a>
                        </div>

                    <!-- Label Bulan & Tahun -->
                    @if($mode === 'bulanan')

                        <div class="bg-[#003d30] border border-amber-400/50 text-amber-300 px-4 py-2 rounded text-sm font-bold shadow-sm">
                            Rentang:
                            {{ $selectedDate->copy()->startOfMonth()->format('d M Y') }}
                            -
                            {{ $selectedDate->copy()->endOfMonth()->format('d M Y') }}
                        </div>

                    @elseif($mode === 'tahunan')

                        <div class="bg-[#003d30] border border-amber-400/50 text-amber-300 px-4 py-2 rounded text-sm font-bold shadow-sm">
                            Rentang:
                            {{ $selectedDate->copy()->startOfYear()->format('d M Y') }}
                            -
                            {{ $selectedDate->copy()->endOfYear()->format('d M Y') }}
                        </div>

                    @endif

                    <!-- Date Picker Kalender Popup -->
                    <form action="{{ route('laporan.koleksi') }}" method="GET" class="flex items-center relative">
                        <input type="hidden" name="mode" value="{{ $mode }}">
                        <button type="button" 
                                onclick="document.getElementById('date-picker-koleksi').showPicker()" 
                                class="bg-[#004d40] text-white p-2.5 rounded hover:bg-[#003d30] transition shadow flex items-center justify-center focus:outline-none" 
                                title="Pilih Tanggal Kalender">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </button>
                        <input type="date" id="date-picker-koleksi" name="date" class="opacity-0 absolute pointer-events-none w-0 h-0" onchange="this.form.submit()" value="{{ $selectedDate->format('Y-m-d') }}">
                    </form>
                </div>

                <!-- Kanan: Tombol Kembali, Import Excel, Unduh Excel -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('laporan.index', ['date' => $selectedDate->format('Y-m-d'), 'mode' => $mode]) }}" class="inline-flex items-center gap-2 bg-[#004d40] text-white px-4 py-2.5 rounded font-bold hover:bg-[#003d30] transition shadow text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>

                    <!-- Tombol Import Excel -->
                    <button type="button" onclick="document.getElementById('modalImportKoleksi').classList.remove('hidden')" class="bg-[#004d40] text-white px-4 py-2.5 rounded hover:bg-[#003d30] transition shadow flex items-center gap-2 text-sm font-bold" title="Import Data Koleksi">
                        <span class="material-icons text-xl">file_upload</span>
                        <span>Import Excel</span>
                    </button>

                    <a href="{{ route('laporan.koleksi.export', ['date' => $selectedDate->format('Y-m-d'),'mode' => $mode]) }}"
                        class="bg-[#004d40] text-white px-4 py-2.5 rounded hover:bg-[#003d30] transition shadow flex items-center gap-2 text-sm font-bold" title="Unduh Laporan Excel">
                        <span class="material-icons text-xl">file_download</span>
                        <span>Unduh Excel</span>
                    </a>
                </div>
            </div>

            <!-- Modal Popup Import Excel Koleksi -->
            <div id="modalImportKoleksi" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center hidden">
                 <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Import Koleksi Buku</h3>
                        <button onclick="document.getElementById('modalImportKoleksi').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">&times;</button>
                    </div>

                    <form action="{{ route('laporan.koleksi.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel (.xlsx / .xls)</label>
                            <input type="file" name="file_excel" required accept=".xlsx, .xls, .csv" class="w-full text-sm text-gray-500 border border-gray-300 rounded-lg p-2 focus:outline-none">
                        </div>

                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="document.getElementById('modalImportKoleksi').classList.add('hidden')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm font-medium">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-[#004d40] text-white rounded-md hover:bg-[#003d30] text-sm font-medium">Upload & Import</button>
                         </div>
                     </form>
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

            <!-- Grid 6 Card Statistik Koleksi -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Total Koleksi</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $totalKoleksi }}</p>
                </div>

                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Total Judul Buku Fisik</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $totalJudulBukuFisik }}</p>
                </div>

                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Total E-Book</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $totalEbook }}</p>
                </div>

                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Total Stok Buku Fisik</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $totalStokBukuFisik }}</p>
                </div>


            </div>

        </div>
    </main>
</div>
@endsection