@extends('layouts.app') {{-- Sesuaikan dengan master layout Anda --}}

@section('content')
<div class="flex min-h-screen bg-gray-100">
    
    <aside class="w-64 bg-[#004d3d] text-white flex flex-col justify-between">
        <div class="p-6">
            <h2 class="text-xl font-bold tracking-wider">PERPUSTAKAAN</h2>
            <p class="text-xs text-gray-300">MADRASAH DARUSSALAM</p>
            <hr class="border-emerald-700 my-4">
            
            <nav class="space-y-4">
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded bg-[#003d30] font-medium text-white">
                    <span class="material-icons">dashboard</span> Dashboard
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded text-gray-300 hover:bg-[#003d30] hover:text-white transition">
                    <span class="material-icons">people</span> Manajemen Siswa
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded text-gray-300 hover:bg-[#003d30] hover:text-white transition">
                    <span class="material-icons">menu_book</span> Katalog Buku
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded text-gray-300 hover:bg-[#003d30] hover:text-white transition">
                    <span class="material-icons">swap_horiz</span> Sirkulasi
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded text-gray-300 hover:bg-[#003d30] hover:text-white transition">
                    <span class="material-icons">qr_code_scanner</span> Absen
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded text-gray-300 hover:bg-[#003d30] hover:text-white transition">
                    <span class="material-icons">description</span> Laporan
                </a>
            </nav>
        </div>
    </aside>

    <main class="flex-1 flex flex-col">
        <header class="bg-[#fffde6] border-b border-gray-200 py-4 px-8 flex justify-between items-center">
            <div></div> <div class="flex items-center gap-2">
                <img src="{{ asset('images/logo_darussalam.png') }}" alt="Logo Darussalam" class="h-10">
            </div>
        </header>

        <div class="p-8 space-y-6">
            <h1 class="text-2xl font-bold text-[#004d3d]">Selamat Datang, ADMIN BESAR</h1>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-[#9bb2b1] text-white p-6 rounded-lg shadow text-center">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-100">Pengunjung hari ini</h3>
                    <p class="text-4xl font-bold mt-2">{{ $todayVisitors }}</p>
                </div>
                
                <div class="bg-[#9bb2b1] text-white p-6 rounded-lg shadow text-center">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-100">Peminjaman Buku</h3>
                    <p class="text-4xl font-bold mt-2">{{ $todayBorrowings }}</p>
                </div>

                <div class="bg-[#9bb2b1] text-white p-6 rounded-lg shadow text-center">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-100">Pengembalian Buku</h3>
                    <p class="text-4xl font-bold mt-2">{{ $todayReturns }}</p>
                </div>
            </div>

            <div class="bg-[#9bb2b1] p-6 rounded-lg shadow">
                <h2 class="text-lg font-bold text-white mb-4">Aktivitas Terbaru</h2>
                
                <div class="overflow-x-auto rounded-lg">
                    <table class="min-w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#004d3d] text-white">
                                <th class="p-3 text-sm font-semibold">Waktu</th>
                                <th class="p-3 text-sm font-semibold">Tindakan</th>
                                <th class="p-3 text-sm font-semibold">Detail Buku</th>
                                <th class="p-3 text-sm font-semibold">User</th>
                            </tr>
                        </thead>
                        <tbody class="text-white divide-y divide-gray-300/40">
                            @forelse($recentActivities as $activity)
                                <tr class="hover:bg-[#8da7a6]/50">
                                    <td class="p-3 text-sm font-medium">{{ $activity['waktu'] }}</td>
                                    <td class="p-3 text-sm">{{ $activity['tindakan'] }}</td>
                                    <td class="p-3 text-sm italic">{{ $activity['detail_buku'] }}</td>
                                    <td class="p-3 text-sm font-medium">{{ $activity['user'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-sm">Tidak ada aktivitas hari ini.</td>
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