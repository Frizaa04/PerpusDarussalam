@extends('layouts.pages.admin.provider.app')

@section('title', 'Laporan Keuangan Perpustakaan - Madrasah Darussalam')

@section('content')
    <div class="flex min-h-screen bg-[#f4f7f6]">

        <!-- Sidebar -->
        @include('layouts.pages.admin.provider.sidebar')

        <!-- Main Content -->
        <main class="flex-1 flex flex-col">

            <!-- Area Isi Konten Laporan Keuangan -->
            <div class="p-8 space-y-8">

                {{-- ================= TAMPILAN 1: 3 CARD UTAMA ================= --}}
                @if (!$category)

                    <!-- Bar Navigasi Filter Tanggal, Mode, & Tombol Excel -->
                    <div class="flex flex-wrap items-center justify-between gap-4 w-full">

                        <!-- Kiri: Tombol Mode (Harian / Mingguan), Label Bulan, Tanggal, & Kalender -->
                        <div class="flex flex-wrap items-center gap-3">

                            <!-- Toggle Tombol Harian / Mingguan -->
                            <div class="inline-flex bg-[#004d40] p-1 rounded-lg border border-[#004d40] shadow-sm">
                                <a href="{{ route('laporan.keuangan', ['date' => $selectedDate->format('Y-m-d'), 'mode' => 'harian']) }}"
                                    class="px-3 py-1.5 text-xs font-bold rounded transition-colors {{ $mode === 'harian' ? 'bg-amber-400 text-[#004d40]' : 'text-white hover:bg-white/10' }}">
                                    Harian
                                </a>
                                <a href="{{ route('laporan.keuangan', ['date' => $selectedDate->format('Y-m-d'), 'mode' => 'mingguan']) }}"
                                    class="px-3 py-1.5 text-xs font-bold rounded transition-colors {{ $mode === 'mingguan' ? 'bg-amber-400 text-[#004d40]' : 'text-white hover:bg-white/10' }}">
                                    Per Minggu
                                </a>
                            </div>

                            <!-- Label Bulan & Tahun -->
                            <div class="bg-[#004d40] text-amber-300 px-3.5 py-2 rounded text-sm font-bold shadow-sm">
                                {{ $monthYearLabel }}
                            </div>

                            <!-- Pilihan Tanggal -->
                            @if ($mode === 'harian')
                                <div
                                    class="inline-flex bg-[#004d40] rounded border border-[#004d40] overflow-hidden shadow-sm">
                                    @foreach ($dates as $d)
                                        <a href="{{ route('laporan.keuangan', ['date' => $d['full_date'], 'mode' => 'harian']) }}"
                                            class="px-3 py-2 text-sm font-bold border-r border-white/30 last:border-r-0 transition-colors duration-150 {{ $d['is_active'] ? 'bg-[#003d30] text-amber-300' : 'text-white hover:bg-white/10' }}">
                                            {{ $d['day'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <!-- Badge Rentang Tanggal Minggu Ini (Senin - Minggu) -->
                                <div
                                    class="bg-[#003d30] border border-amber-400/50 text-amber-300 px-4 py-2 rounded text-sm font-bold shadow-sm">
                                    Rentang: {{ \Carbon\Carbon::parse($startOfWeekDate)->format('d M') }} -
                                    {{ \Carbon\Carbon::parse($endOfWeekDate)->format('d M Y') }}
                                </div>
                            @endif

                            <!-- Date Picker Kalender Popup -->
                            <form action="{{ route('laporan.keuangan') }}" method="GET"
                                class="flex items-center relative">
                                <input type="hidden" name="mode" value="{{ $mode }}">

                                <button type="button"
                                    onclick="document.getElementById('date-picker-keuangan').showPicker()"
                                    class="bg-[#004d40] text-white p-2.5 rounded hover:bg-[#003d30] transition shadow flex items-center justify-center focus:outline-none"
                                    title="Pilih Tanggal Kalender">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </button>

                                <input type="date" id="date-picker-keuangan" name="date"
                                    class="opacity-0 absolute pointer-events-none w-0 h-0" onchange="this.form.submit()"
                                    value="{{ $selectedDate->format('Y-m-d') }}">
                            </form>
                        </div>

                        <div class="flex items-center gap-3">
                            <!-- Kotak Total -->
                            @if (!$category)
                                <div
                                    class="inline-flex items-center justify-center bg-[#004d40] text-amber-300 px-4 py-2.5 rounded font-bold text-sm shadow">
                                    Total: Rp. {{ number_format($totalSemua, 0, ',', '.') }}
                                </div>
                            @else
                                <div
                                    class="inline-flex items-center justify-center bg-[#004d40] text-amber-300 px-4 py-2.5 rounded font-bold text-sm shadow">
                                    Total: Rp. {{ number_format($totalCategory, 0, ',', '.') }}
                                </div>
                            @endif

                            <!-- Tombol Unduh Excel -->
                            <a href="{{ route('laporan.transaksi.export', request()->all()) }}" 
                            class="inline-flex items-center justify-center gap-2 bg-[#004d40] text-white px-4 py-2.5 rounded font-bold hover:bg-[#003d30] transition shadow text-sm">
                                <span class="material-icons text-xl">file_download</span>
                                <span>Unduh Excel</span>
                            </a>
                        </div>

                    </div>

                    <!-- Garis Pembatas Hijau -->
                    <hr class="border-t-2 border-[#004d40]">

                    <!-- Grid Card Ringkasan -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                        <!-- Card 1: Pembuatan Kartu -->
                        <a href="{{ route('laporan.keuangan', ['date' => $selectedDate, 'mode' => $mode, 'category' => 'pembuatan_kartu']) }}"
                            class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                            <h3 class="text-sm font-bold text-white/90 tracking-wide">Pembuatan Kartu</h3>
                            <p class="text-4xl font-extrabold mt-4">{{ $pembuatanKartuCount }}</p>
                        </a>

                        <!-- Card 2: Kehilangan Kartu -->
                        <a href="{{ route('laporan.keuangan', ['date' => $selectedDate, 'mode' => $mode, 'category' => 'kehilangan_kartu']) }}"
                            class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                            <h3 class="text-sm font-bold text-white/90 tracking-wide">Kehilangan Kartu</h3>
                            <p class="text-4xl font-extrabold mt-4">{{ $kehilanganKartuCount }}</p>
                        </a>

                        <!-- Card 3: Keterlambatan Buku -->
                        <a href="{{ route('laporan.keuangan', ['date' => $selectedDate, 'mode' => $mode, 'category' => 'denda_keterlambatan']) }}"
                            class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                            <h3 class="text-sm font-bold text-white/90 tracking-wide">Keterlambatan Buku</h3>
                            <p class="text-4xl font-extrabold mt-4">{{ $keterlambatanBukuCount }}</p>
                        </a>

                        <a href="{{ route('laporan.keuangan', ['date' => $selectedDate, 'mode' => $mode, 'category' => 'kehilangan_buku']) }}"
                            class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                            <h3 class="text-sm font-bold text-white/90 tracking-wide">Kehilangan Buku</h3>
                            <p class="text-4xl font-extrabold mt-4">{{ $kehilanganBukuCount }}</p>
                        </a>

                    </div>

                    {{-- ================= TAMPILAN 2: TABEL DETAIL KATEGORI ================= --}}
                @else
                    <!-- Header Top Bar Detail (Disamakan strukturnya dengan Halaman Utama Card) -->
                    <div class="flex flex-wrap items-center justify-between gap-4">

                        <!-- Kiri: Toggle Mode, Bulan, Tanggal/Range, & Kalender -->
                        <div class="flex flex-wrap items-center gap-3">

                            <!-- Toggle Tombol Harian / Mingguan -->
                            <div class="inline-flex bg-[#004d40] p-1 rounded-lg border border-[#004d40] shadow-sm">
                                <a href="{{ route('laporan.keuangan', ['date' => $selectedDate, 'mode' => 'harian', 'category' => $category]) }}"
                                    class="px-3 py-1.5 text-xs font-bold rounded transition-colors {{ $mode === 'harian' ? 'bg-amber-400 text-[#004d40]' : 'text-white hover:bg-white/10' }}">
                                    Harian
                                </a>
                                <a href="{{ route('laporan.keuangan', ['date' => $selectedDate, 'mode' => 'mingguan', 'category' => $category]) }}"
                                    class="px-3 py-1.5 text-xs font-bold rounded transition-colors {{ $mode === 'mingguan' ? 'bg-amber-400 text-[#004d40]' : 'text-white hover:bg-white/10' }}">
                                    Per Minggu
                                </a>
                            </div>

                            <!-- Label Bulan & Tahun -->
                            <div class="bg-[#004d40] text-amber-300 px-3.5 py-2 rounded text-sm font-bold shadow-sm">
                                {{ $monthYearLabel }}
                            </div>

                            <!-- Pilihan Tanggal / Rentang Minggu -->
                            @if ($mode === 'harian')
                                <div
                                    class="inline-flex bg-[#004d40] rounded border border-[#004d40] overflow-hidden shadow-sm">
                                    @foreach ($dates as $d)
                                        <a href="{{ route('laporan.keuangan', ['date' => $d['full_date'], 'mode' => 'harian', 'category' => $category]) }}"
                                            class="px-3 py-2 text-sm font-bold border-r border-white/30 last:border-r-0 transition-colors duration-150 {{ $d['is_active'] ? 'bg-[#003d30] text-amber-300' : 'text-white hover:bg-white/10' }}">
                                            {{ $d['day'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <!-- Badge Rentang Tanggal Minggu Ini (Senin - Minggu) -->
                                <div
                                    class="bg-[#003d30] border border-amber-400/50 text-amber-300 px-4 py-2 rounded text-sm font-bold shadow-sm">
                                    Rentang: {{ \Carbon\Carbon::parse($startOfWeekDate)->format('d M') }} -
                                    {{ \Carbon\Carbon::parse($endOfWeekDate)->format('d M Y') }}
                                </div>
                            @endif

                            <!-- Date Picker Kalender Popup -->
                            <form action="{{ route('laporan.keuangan') }}" method="GET"
                                class="flex items-center relative">
                                <input type="hidden" name="mode" value="{{ $mode }}">
                                <input type="hidden" name="category" value="{{ $category }}">

                                <button type="button" onclick="document.getElementById('date-picker-detail').showPicker()"
                                    class="bg-[#004d40] text-white p-2.5 rounded hover:bg-[#003d30] transition shadow flex items-center justify-center focus:outline-none"
                                    title="Pilih Tanggal Kalender">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </button>

                                <input type="date" id="date-picker-detail" name="date"
                                    class="opacity-0 absolute pointer-events-none w-0 h-0" onchange="this.form.submit()"
                                    value="{{ $selectedDate }}">
                            </form>
                        </div>

                        <!-- Kanan: Tombol Kembali, Total, & Print -->
                        <div class="flex items-center gap-3">
                            <!-- Tombol Kembali -->
                            <a href="{{ route('laporan.keuangan', ['date' => $selectedDate->format('Y-m-d'), 'mode' => $mode]) }}"
                                class="inline-flex items-center justify-center gap-2 bg-[#004d40] text-white px-4 py-2.5 rounded font-bold hover:bg-[#003d30] transition shadow text-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Kembali
                            </a>

                            <!-- Kotak Total -->
                            <div
                                class="inline-flex items-center justify-center bg-[#004d40] text-amber-300 px-4 py-2.5 rounded font-bold text-sm shadow">
                                Total: Rp. {{ number_format($totalCategory, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <hr class="border-t-2 border-[#004d40]">

                    <!-- Box Tabel Daftar Transaksi -->
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
                            {{ $dataList->appends(['date' => $selectedDate, 'mode' => $mode, 'category' => $category, 'search' => $search])->links() }}
                        </div>
                    </div>

                @endif

            </div>
        </main>
    </div>
@endsection
