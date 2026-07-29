@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Pemanggilan Sidebar -->
    @include('layouts.sidebar')

    <main class="flex-1 flex flex-col">

        <div class="p-8 space-y-6">
            
            <!-- Filter Tanggal & Mode -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    
                    <!-- Toggle Harian / Per Minggu -->
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
                                onclick="document.getElementById('date-picker-absen').showPicker()" 
                                class="bg-[#004d40] text-white p-2.5 rounded hover:bg-[#003d30] transition shadow flex items-center justify-center focus:outline-none" 
                                title="Pilih Tanggal Kalender">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </button>
                        <input type="date" id="date-picker-absen" name="date" class="opacity-0 absolute pointer-events-none w-0 h-0" onchange="this.form.submit()" value="{{ $selectedDate->format('Y-m-d') }}">
                    </form>
                </div>

                <!-- Tombol Kembali -->
                <a href="{{ route('laporan.index', ['date' => $selectedDate->format('Y-m-d'), 'mode' => $mode]) }}" class="inline-flex items-center gap-2 bg-[#004d40] text-white px-4 py-2.5 rounded font-bold hover:bg-[#003d30] transition shadow text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>

            <!-- Input untuk Barcode Scanner -->
            <form action="{{ route('absen.store') }}" method="POST" class="mb-4">
                @csrf
                <div class="flex items-center gap-2">
                    <input type="text" name="kode" id="scanner-input" 
                        class="bg-gray-700 text-white px-4 py-2 rounded border border-gray-600 focus:outline-none focus:border-green-500 w-full md:w-1/3" 
                        placeholder="Klik di sini lalu scan barcode..." 
                        autofocus autocomplete="off" required>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold">
                        Proses
                    </button>
                </div>
            </form>

            <!-- Box Tabel Daftar Kunjungan -->
            <div class="bg-[#a2b4ba] p-6 rounded shadow-[0_4px_12px_rgba(0,0,0,0.15)] border border-gray-300/30">
                <h2 class="text-xl font-bold text-white mb-4 tracking-wide">Daftar Kunjungan</h2>
                
                <div class="overflow-x-auto rounded">
                    <table class="min-w-full text-left border-collapse border border-white/40">
                        <thead>
                            <tr class="bg-[#004d40] text-white divide-x divide-white/40">
                                <th class="p-3 text-sm font-bold tracking-wider">Waktu</th>
                                <th class="p-3 text-sm font-bold tracking-wider">NIS / NIP / NIK</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Nama</th>
                            </tr>
                        </thead>
                        <tbody class="text-white divide-y divide-white/40">
                            @forelse($visits as $visit)
                                <tr class="divide-x divide-white/40 hover:bg-white/10 transition-colors">
                                    <td class="p-3 text-sm font-semibold text-white/90">
                                        {{ $visit->visited_at ? \Carbon\Carbon::parse($visit->visited_at)->format('d M Y, H:i') : '-' }}
                                    </td>
                                    <td class="p-3 text-sm font-semibold text-white/90">
                                        {{ $visit->user->nis ?? $visit->user->nip ?? $visit->user->nik ?? '-' }}
                                    </td>
                                    <td class="p-3 text-sm font-semibold text-white/90">
                                        {{ $visit->user->name ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-5 text-center text-sm font-semibold text-white/80">
                                        Belum ada data kunjungan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginasi Dinamis Laravel -->
                <div class="mt-6 text-white">
                    {{ $visits->appends(request()->query())->links() }}
                </div>

            </div>
        </div>
    </main>
</div>

<script>
    document.addEventListener("click", function(e) {
        // Mencegah gangguan fokus scanner jika mengklik input/tombol lain
        if (!['INPUT', 'BUTTON', 'A'].includes(e.target.tagName)) {
            const scanner = document.getElementById("scanner-input");
            if(scanner) scanner.focus();
        }
    });
</script>
@endsection