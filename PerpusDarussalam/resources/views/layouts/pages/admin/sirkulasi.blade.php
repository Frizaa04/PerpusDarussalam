@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Panggil Sidebar yang Sudah Dipisah -->
    @include('layouts.sidebar')

    <!-- KONTEN UTAMA -->
    <main class="flex-1 flex flex-col">
        <!-- Header Atas -->
        <header class="bg-white border-b border-gray-200 px-8 flex justify-end items-center shadow-sm h-20">
            <div class="flex items-center h-full">
                <img src="{{ asset('image/covers/darussalam.png') }}" alt="Logo Darussalam" class="h-full py-1 object-contain">
            </div>
        </header>

        <!-- Area Konten Sirkulasi -->
        <div class="p-8 space-y-6">
            
            <!-- Filter Bar & Tambah Data -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <!-- Form Pencarian -->
                <div class="w-full max-w-md">
                    <form action="{{ route('circulation.index') }}" method="GET" class="flex items-center border-2 border-[#004d40] rounded overflow-hidden bg-white">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari Data Peminjaman" class="w-full px-4 py-2 text-gray-700 outline-none font-medium placeholder-gray-400">
                        <button type="submit" class="bg-[#004d40] text-white px-4 py-2 flex items-center justify-center hover:bg-[#003d30] transition">
                            <span class="material-icons">search</span>
                        </button>
                    </form>
                </div>

                <!-- Tombol Tambah Peminjaman Baru -->
                <button class="bg-white border-2 border-[#004d40] text-[#004d40] px-5 py-2 rounded font-semibold hover:bg-gray-50 transition shadow-sm">
                    + Peminjaman Baru
                </button>
            </div>

            <!-- Box Tabel Sirkulasi -->
            <div class="bg-[#b0bec5] p-6 rounded shadow-[0_4px_12px_rgba(0,0,0,0.15)] border border-gray-300/30">
                <h2 class="text-xl font-bold text-white mb-4 tracking-wide">Sirkulasi Peminjaman & Pengembalian</h2>
                
                <div class="overflow-x-auto rounded">
                    <table class="min-w-full text-left border-collapse border border-white/40">
                        <thead>
                            <tr class="bg-[#004d40] text-white divide-x divide-white/40">
                                <th class="p-3 text-sm font-bold tracking-wider w-12 text-center">No</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Nis</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Nama</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Judul Buku</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Tgl Pinjam</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Tgl Kembali</th>
                                <th class="p-3 text-sm font-bold tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-white divide-y divide-white/40">
                            @forelse($circulations as $index => $item)
                                <tr class="divide-x divide-white/40 hover:bg-white/10 transition-colors">
                                    <td class="p-3 text-sm font-bold text-center text-white/90">{{ $index + 1 }}</td>
                                    <td class="p-3 text-sm font-bold text-white/90">{{ $item->nis }}</td>
                                    <td class="p-3 text-sm font-bold text-white/90">{{ $item->name }}</td>
                                    <td class="p-3 text-sm text-white/90">{{ $item->book_title }}</td>
                                    <td class="p-3 text-sm text-white/90">{{ $item->borrow_date }}</td>
                                    <td class="p-3 text-sm text-white/90">{{ $item->return_date }}</td>
                                    <td class="p-3 text-sm text-center">
                                        <a href="#" class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold uppercase tracking-wider hover:bg-[#003d30] transition shadow-sm inline-block">
                                            Edit Data
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-5 text-center text-sm font-semibold text-white/80">Data sirkulasi tidak ditemukan.</td>
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