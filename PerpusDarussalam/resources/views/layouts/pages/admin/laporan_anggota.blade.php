@extends('layouts.app')

@section('title', 'Laporan Anggota - Madrasah Darussalam')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Sidebar Navigasi -->
    @include('layouts.sidebar')

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col">

        <!-- Area Isi Laporan -->
        <div class="p-8 space-y-8">
            
            <!-- Bilah Navigasi Tanggal & Tombol Aksi -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                
                <!-- Kiri: Filter Mode & Tanggal -->
                <div class="flex flex-wrap items-center gap-3">
                    
                    <!-- Toggle Harian / Per Minggu -->
                    <div class="inline-flex bg-[#004d40] p-1 rounded-lg border border-[#004d40] shadow-sm">
                        <a href="{{ route('laporan.anggota', ['date' => $selectedDate->format('Y-m-d'), 'mode' => 'harian']) }}" 
                           class="px-3 py-1.5 text-xs font-bold rounded transition-colors {{ $mode === 'harian' ? 'bg-amber-400 text-[#004d40]' : 'text-white hover:bg-white/10' }}">
                            Harian
                        </a>
                        <a href="{{ route('laporan.anggota', ['date' => $selectedDate->format('Y-m-d'), 'mode' => 'mingguan']) }}" 
                           class="px-3 py-1.5 text-xs font-bold rounded transition-colors {{ $mode === 'mingguan' ? 'bg-amber-400 text-[#004d40]' : 'text-white hover:bg-white/10' }}">
                            Per Minggu
                        </a>
                    </div>

                    <!-- Label Bulan & Tahun -->
                    <div class="bg-[#004d40] text-amber-300 px-3.5 py-2 rounded text-sm font-bold shadow-sm">
                        {{ $monthYearLabel }}
                    </div>

                    <!-- Tanggal Harian / Rentang Minggu -->
                    @if($mode === 'harian')
                        <div class="inline-flex bg-[#004d40] rounded border border-[#004d40] overflow-hidden shadow-sm">
                            @foreach($dates as $d)
                                <a href="{{ route('laporan.anggota', ['date' => $d['full_date'], 'mode' => 'harian']) }}" 
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
                    <form action="{{ route('laporan.anggota') }}" method="GET" class="flex items-center relative">
                        <input type="hidden" name="mode" value="{{ $mode }}">
                        <button type="button" 
                                onclick="document.getElementById('date-picker-anggota').showPicker()" 
                                class="bg-[#004d40] text-white p-2.5 rounded hover:bg-[#003d30] transition shadow flex items-center justify-center focus:outline-none" 
                                title="Pilih Tanggal Kalender">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </button>
                        <input type="date" id="date-picker-anggota" name="date" class="opacity-0 absolute pointer-events-none w-0 h-0" onchange="this.form.submit()" value="{{ $selectedDate->format('Y-m-d') }}">
                    </form>
                </div>

                <!-- Kanan: Tombol Import, Kembali, Unduh Excel -->
                <div class="flex items-center gap-3">
                    <button type="button" onclick="document.getElementById('modalImport').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2.5 rounded hover:bg-blue-700 transition shadow flex items-center gap-2 text-sm font-bold" title="Import Data Excel">
                        <span class="material-icons text-xl">file_upload</span>
                        <span>Import Excel</span>
                    </button>
                    
                    <a href="{{ route('laporan.index', ['date' => $selectedDate->format('Y-m-d'), 'mode' => $mode]) }}" class="inline-flex items-center gap-2 bg-[#004d40] text-white px-4 py-2.5 rounded font-bold hover:bg-[#003d30] transition shadow text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>

                    <a href="{{ route('laporan.anggota.export', ['date' => $selectedDate->format('Y-m-d')]) }}" class="bg-[#004d40] text-white px-4 py-2.5 rounded hover:bg-[#003d30] transition shadow flex items-center gap-2 text-sm font-bold" title="Unduh Laporan Excel">
                        <span class="material-icons text-xl">file_download</span>
                        <span>Unduh Excel</span>
                    </a>
                </div>
            </div>

            <!-- Modal Popup Import Excel -->
            <div id="modalImport" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center hidden">
                 <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Import Data Anggota</h3>
                        <button onclick="document.getElementById('modalImport').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">&times;</button>
                    </div>

                    <form action="{{ route('laporan.anggota.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel (.xlsx / .xls)</label>
                            <input type="file" name="file_excel" required accept=".xlsx, .xls, .csv" class="w-full text-sm text-gray-500 border border-gray-300 rounded-lg p-2 focus:outline-none">
                        </div>

                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="document.getElementById('modalImport').classList.add('hidden')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm font-medium">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">Upload & Import</button>
                         </div>
                     </form>
                 </div>
             </div>

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

                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Umum</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $umum }}</p>
                </div>
            </div>

        </div>
    </main>
</div>
@endsection