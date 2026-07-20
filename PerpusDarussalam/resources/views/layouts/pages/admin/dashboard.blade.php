@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Pemanggilan Sidebar -->
    @include('layouts.sidebar')

    
    <main class="flex-1 flex flex-col">
        <!-- Header Atas -->
        <header class="bg-white border-b border-gray-200 px-8 flex justify-end items-center shadow-sm h-20">
            <div class="flex items-center h-full">
                <img src="{{ asset('image/covers/darussalam.png') }}" alt="Logo Darussalam" class="h-full py-1 object-contain">
            </div>
        </header>

        <!-- Area Konten -->
        <div class="p-8 space-y-8">
            <h1 class="text-2xl font-bold text-[#004d40] tracking-wide">Selamat Datang, ADMIN BESAR</h1>

            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Pengunjung hari ini</h3>
                    <p class="text-4xl font-extrabold mt-3">{{ $todayVisitors }}</p>
                </div>
                
                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Peminjaman Buku</h3>
                    <p class="text-4xl font-extrabold mt-3">{{ $todayBorrowings }}</p>
                </div>

                <div class="bg-[#b0bec5] text-white p-6 rounded shadow-[0_4px_10px_rgba(0,0,0,0.15)] text-center border border-gray-300/30">
                    <h3 class="text-sm font-bold text-white/90 tracking-wide">Pengembalian Buku</h3>
                    <p class="text-4xl font-extrabold mt-3">{{ $todayReturns }}</p>
                </div>
            </div>

            <!-- Box Tabel Aktivitas -->
            <div class="bg-[#b0bec5] p-6 rounded shadow-[0_4px_12px_rgba(0,0,0,0.15)] border border-gray-300/30">
                <h2 class="text-xl font-bold text-white mb-4 tracking-wide">Aktivitas Terbaru</h2>
                
                <div class="overflow-x-auto rounded">
                    <table class="min-w-full text-left border-collapse border border-white/40">
                        <thead>
                            <tr class="bg-[#004d40] text-white divide-x divide-white/40">
                                <th class="p-3 text-sm font-bold tracking-wider">Waktu</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Tindakan</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Detail Buku</th>
                                <th class="p-3 text-sm font-bold tracking-wider">User</th>
                            </tr>
                        </thead>
                        <tbody class="text-white divide-y divide-white/40">
                            @forelse($recentActivities as $activity)
                                <tr class="divide-x divide-white/40 hover:bg-white/10 transition-colors">
                                    <td class="p-3 text-sm font-bold text-white/90">{{ $activity['waktu'] }}</td>
                                    <td class="p-3 text-sm text-white/90">{{ $activity['tindakan'] }}</td>
                                    <td class="p-3 text-sm text-white/90">{{ $activity['detail_buku'] }}</td>
                                    <td class="p-3 text-sm font-bold text-white/90">{{ $activity['user'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-5 text-center text-sm font-semibold text-white/80">Tidak ada aktivitas hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection