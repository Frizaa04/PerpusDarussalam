@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-white">
    
    <!-- Sidebar -->
    @include('layouts.sidebar')

    <main class="flex-1 flex flex-col">
        <!-- Header Atas -->
        <header class="bg-white border-b border-gray-200 px-8 flex justify-end items-center shadow-sm h-20">
            <div class="flex items-center gap-4">
                <button class="text-[#004d40] hover:text-[#003d30] transition p-1">
                    <span class="material-icons text-2xl">notifications_none</span>
                </button>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-[#004d40] text-white px-4 py-1.5 rounded text-sm font-semibold hover:bg-[#003d30] transition">
                        LogOut
                    </button>
                </form>

                <img src="{{ asset('image/covers/darussalam.png') }}" alt="Logo Darussalam" class="h-12 object-contain ml-2">
            </div>
        </header>

        <!-- Area Isi Konten Laporan Keuangan -->
        <div class="p-8 space-y-6">
            
            {{-- ================= TAMPILAN 1: 3 CARD UTAMA ================= --}}
            @if(!$category)
                
                <!-- Navigasi Tanggal Horizontal & Picker -->
                <div class="flex items-center gap-3">
                    <div class="inline-flex bg-[#004d40] rounded border border-[#004d40] overflow-hidden shadow-sm">
                        @foreach($dates as $d)
                            <a href="{{ route('laporan.keuangan', ['date' => $d['full_date']]) }}" 
                               class="px-4 py-2 text-sm font-bold border-r border-white/30 last:border-r-0 transition-colors duration-150 {{ $d['is_active'] ? 'bg-[#003d30] text-amber-300' : 'text-white hover:bg-white/10' }}">
                                {{ $d['day'] }}
                            </a>
                        @endforeach
                    </div>

                    <form action="{{ route('laporan.keuangan') }}" method="GET" class="flex items-center">
                        <label for="date-picker-keuangan" class="cursor-pointer bg-[#004d40] text-white p-2.5 rounded hover:bg-[#003d30] transition shadow" title="Pilih Tanggal">
                            <span class="material-icons text-xl">calendar_today</span>
                        </label>
                        <input type="date" id="date-picker-keuangan" name="date" class="hidden" onchange="this.form.submit()" value="{{ $selectedDate }}">
                    </form>
                </div>

                <hr class="border-t-2 border-[#004d40]">

                <!-- Grid Card Ringkasan -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Card 1: Pembuatan Kartu -->
                    <a href="{{ route('laporan.keuangan', ['date' => $selectedDate, 'category' => 'pembuatan_kartu']) }}" 
                       class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                        <h3 class="text-sm font-bold text-white/90 tracking-wide">Pembuatan Kartu</h3>
                        <p class="text-4xl font-extrabold mt-4">{{ $pembuatanKartuCount }}</p>
                    </a>

                    <!-- Card 2: Kehilangan Kartu -->
                    <a href="{{ route('laporan.keuangan', ['date' => $selectedDate, 'category' => 'kehilangan_kartu']) }}" 
                       class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                        <h3 class="text-sm font-bold text-white/90 tracking-wide">Kehilangan Kartu</h3>
                        <p class="text-4xl font-extrabold mt-4">{{ $kehilanganKartuCount }}</p>
                    </a>

                    <!-- Card 3: Keterlambatan Buku (Terhubung ke Transaksi denda_keterlambatan) -->
                    <a href="{{ route('laporan.keuangan', ['date' => $selectedDate, 'category' => 'denda_keterlambatan']) }}" 
                       class="block bg-[#b0bec5] hover:bg-[#004d40] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30 hover:scale-105 transition-all duration-300 transform cursor-pointer">
                        <h3 class="text-sm font-bold text-white/90 tracking-wide">Keterlambatan Buku</h3>
                        <p class="text-4xl font-extrabold mt-4">{{ $keterlambatanBukuCount }}</p>
                    </a>

                </div>

            {{-- ================= TAMPILAN 2: TABEL DETAIL KATEGORI ================= --}}
            @else

                <!-- Header Top Bar Detail -->
                <div class="flex flex-wrap items-center justify-between gap-4">
                    
                    <!-- Kiri: Filter Tanggal Horizontal -->
                    <div class="flex items-center gap-3">
                        <div class="inline-flex bg-[#004d40] rounded border border-[#004d40] overflow-hidden shadow-sm">
                            @foreach($dates as $d)
                                <a href="{{ route('laporan.keuangan', ['date' => $d['full_date'], 'category' => $category]) }}" 
                                   class="px-3.5 py-1.5 text-xs font-bold border-r border-white/30 last:border-r-0 transition-colors {{ $d['is_active'] ? 'bg-[#003d30] text-amber-300' : 'text-white hover:bg-white/10' }}">
                                    {{ $d['day'] }}
                                </a>
                            @endforeach
                        </div>

                        <form action="{{ route('laporan.keuangan') }}" method="GET">
                            <input type="hidden" name="category" value="{{ $category }}">
                            <label for="date-picker-detail" class="cursor-pointer bg-[#004d40] text-white p-2 rounded hover:bg-[#003d30] transition shadow flex items-center">
                                <span class="material-icons text-lg">calendar_today</span>
                            </label>
                            <input type="date" id="date-picker-detail" name="date" class="hidden" onchange="this.form.submit()" value="{{ $selectedDate }}">
                        </form>
                    </div>

                    <!-- Kanan: Tombol Kembali (Gaya Foto 4), Total, & Print -->
                    <div class="flex items-center gap-3">
                        <a href="{{ route('laporan.keuangan', ['date' => $selectedDate]) }}" 
                           class="bg-[#004d40] hover:bg-[#003d30] text-white px-4 py-2 rounded font-semibold text-sm transition shadow flex items-center gap-2">
                            <span class="material-icons text-sm">arrow_back</span>
                            Kembali
                        </a>

                        <div class="bg-[#004d40] text-white px-5 py-2 rounded font-bold text-sm shadow">
                            Total: Rp. {{ number_format($totalCategory, 0, ',', '.') }}
                        </div>

                        <button onclick="window.print()" class="bg-[#004d40] text-white p-2 rounded hover:bg-[#003d30] transition shadow flex items-center">
                            <span class="material-icons text-xl">print</span>
                        </button>
                    </div>
                </div>

                <hr class="border-t-2 border-[#004d40]">

                <!-- Box Tabel Daftar Transaksi -->
                <div class="bg-[#b0bec5] p-6 rounded shadow-md border border-gray-300">
                    <h2 class="text-xl font-bold text-white mb-4">
                        @if($category === 'pembuatan_kartu')
                            Tabel Daftar Transaksi Pembuatan Kartu
                        @elseif($category === 'kehilangan_kartu')
                            Tabel Daftar Transaksi Kehilangan Kartu
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
                                        <td colspan="5" class="p-6 text-center text-sm font-semibold text-white/90">
                                            Belum ada data transaksi untuk tanggal ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $dataList->appends(['date' => $selectedDate, 'category' => $category, 'search' => $search])->links() }}
                    </div>
                </div>

            @endif

        </div>
    </main>
</div>
@endsection