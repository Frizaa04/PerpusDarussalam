@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Pemanggilan Sidebar -->
    @include('layouts.sidebar')

    <main class="flex-1 flex flex-col">

        <header class="bg-white border-b border-gray-200 px-8 flex justify-end items-center shadow-sm h-20">
            <div class="flex items-center h-full">
                <img src="{{ asset('image/covers/darussalam.png') }}" alt="Logo Darussalam" class="h-full py-1 object-contain">
            </div>
        </header>

        <!-- Area Konten -->
        <div class="p-8 space-y-6">
            <div class="max-w-md">
                <form action="{{ route('book.index') }}" method="GET" class="flex items-center border-2 border-[#004d40] rounded overflow-hidden bg-white">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari Data Buku" class="w-full px-4 py-2 text-gray-700 outline-none font-medium placeholder-gray-400">
                    <button type="submit" class="bg-[#004d40] text-white px-4 py-2 flex items-center justify-center hover:bg-[#003d30] transition">
                        <span class="material-icons">search</span>
                    </button>
                </form>
            </div>

            <!-- Box Tabel -->
            <div class="bg-[#b0bec5] p-6 rounded shadow-[0_4px_12px_rgba(0,0,0,0.15)] border border-gray-300/30">
                <h2 class="text-xl font-bold text-white mb-4 tracking-wide">Tabel Daftar Buku</h2>
                
                <div class="overflow-x-auto rounded">
                    <table class="min-w-full text-left border-collapse border border-white/40">
                        <thead>
                            <tr class="bg-[#004d40] text-white divide-x divide-white/40">
                                <th class="p-3 text-sm font-bold tracking-wider">Cover</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Judul</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Kategori</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Stok</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-white divide-y divide-white/40">
                            @forelse($books as $book)
                                <tr class="divide-x divide-white/40 hover:bg-white/10 transition-colors">
                                    <td class="p-3 text-sm text-center">
                                        <div class="w-10 h-14 bg-gray-400/50 text-[10px] text-white flex items-center justify-center rounded border border-white/30 mx-auto">
                                            No Pic
                                        </div>
                                    </td>
                                    <td class="p-3 text-sm font-bold text-white/90">{{ $book->judul }}</td>
                                    <td class="p-3 text-sm text-white/90">{{ $book->kategori }}</td>
                                    <td class="p-3 text-sm font-bold text-white/90">{{ $book->stok }}</td>
                                    <td class="p-3 text-sm font bold text-white/90">#</td>  
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-5 text-center text-sm font-semibold text-white/80">Data buku tidak ditemukan.</td>
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