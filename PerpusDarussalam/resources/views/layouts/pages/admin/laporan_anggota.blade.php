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
            <div class="flex items-center justify-between">
                <!-- Bagian Kiri: Filter Tanggal -->
                <div class="flex items-center gap-3">
                    <div class="inline-flex bg-[#004d40] rounded border border-[#004d40] overflow-hidden shadow-sm">
                        @foreach($dates as $d)
                            <a href="{{ route('laporan.anggota', ['date' => $d['full_date']]) }}" 
                               class="px-4 py-2 text-sm font-bold border-r border-white/30 last:border-r-0 transition-colors duration-150 {{ $d['is_active'] ? 'bg-[#003d30] text-amber-300' : 'text-white hover:bg-white/10' }}">
                                {{ $d['day'] }}
                            </a>
                        @endforeach
                    </div>

                    <!-- Picker Kalender -->
                    <form action="{{ route('laporan.anggota') }}" method="GET" class="flex items-center">
                        <label for="date-picker" class="cursor-pointer bg-[#004d40] text-white p-2.5 rounded hover:bg-[#003d30] transition shadow" title="Pilih Tanggal Lain">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </label>
                        <input type="date" id="date-picker" name="date" class="hidden" onchange="this.form.submit()" value="{{ $selectedDate->format('Y-m-d') }}">
                    </form>
                </div>

                <!-- Bagian Kanan: Tombol Kembali & Cetak Laporan -->
                <div class="flex items-center gap-3">
                    <button type="button" onclick="document.getElementById('modalImport').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2.5 rounded hover:bg-blue-700 transition shadow flex items-center gap-2 text-sm font-bold" title="Import Data Excel">
                        <span class="material-icons text-xl">file_upload</span>
                        <span>Import Excel</span>
                    </button>
                    <a href="{{ route('laporan.index') }}" class="inline-flex items-center gap-2 bg-[#004d40] text-white px-4 py-2.5 rounded font-bold hover:bg-[#003d30] transition shadow text-sm">
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
                
                <!-- Card 1: Total Anggota -->
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Total Anggota</h3>
                    <p class="text-4xl font-extrabold mt-4">{{ $totalAnggota }}</p>
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