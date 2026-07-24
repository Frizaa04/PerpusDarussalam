@extends('layouts.app')

@section('title', 'Katalog E-Book - Perpustakaan')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Main Content -->
    <main class="flex-1 flex flex-col">
        
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-8 flex justify-end items-center shadow-sm h-20 gap-4">
            <button class="text-[#004d40] hover:opacity-80 transition" title="Notifikasi">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                </svg>
            </button>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-[#004d40] text-white px-4 py-1.5 rounded text-sm font-bold hover:bg-[#003d30] transition shadow">
                    LogOut
                </button>
            </form>

            <div class="flex items-center h-full ml-2">
                <img src="{{ asset('image/covers/darussalam.png') }}" alt="Madrasah Darussalam IBS" class="h-14 object-contain">
            </div>
        </header>

        <!-- Body Area -->
        <div class="p-8 space-y-6">
            
            <!-- Filter & Action Bar -->
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center w-full max-w-md">
                    <input type="text" placeholder="Cari Data E-Book..." class="w-full px-4 py-2 border border-gray-300 rounded-l focus:outline-none focus:ring-1 focus:ring-[#004d40] text-sm">
                    <button class="bg-[#004d40] text-white px-4 py-2 rounded-r hover:bg-[#003d30] transition flex items-center justify-center">
                        <span class="material-icons text-lg">search</span>
                    </button>
                </div>

                <button class="bg-white border-2 border-[#004d40] text-[#004d40] font-bold px-4 py-2 rounded hover:bg-[#004d40] hover:text-white transition text-sm flex items-center gap-1 shadow-sm">
                    + E-Book Baru
                </button>
            </div>

            <!-- Tabel Daftar E-Book -->
            <div class="bg-[#b0bec5] rounded-lg shadow-md overflow-hidden p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-white text-xl font-bold tracking-wide">Tabel Daftar E-Book</h2>
                    <label class="inline-flex items-center text-white text-sm font-medium cursor-pointer">
                        <span>Hapus E-Book</span>
                        <input type="checkbox" class="ml-2 rounded border-gray-300 text-[#004d40] focus:ring-0">
                    </label>
                </div>

                <div class="overflow-x-auto rounded">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#004d40] text-white text-sm uppercase">
                                <th class="p-3">Cover</th>
                                <th class="p-3">Judul E-Book</th>
                                <th class="p-3">Kategori</th>
                                <th class="p-3">Tahun</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-300 bg-[#b0bec5] text-gray-800 text-sm font-medium">
                            @foreach($ebooks as $ebook)
                            <tr class="hover:bg-gray-400/20 transition">
                                <td class="p-3">
                                    <div class="w-12 h-16 bg-gray-300 border border-gray-400 rounded flex items-center justify-center text-xs text-gray-600 font-bold">
                                        PDF
                                    </div>
                                </td>
                                <td class="p-3 font-semibold text-gray-900">{{ $ebook['judul'] }}</td>
                                <td class="p-3">{{ $ebook['kategori'] }}</td>
                                <td class="p-3">{{ $ebook['tahun'] }}</td>
                                <td class="p-3 text-center space-x-2">
                                    <button class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-[#003d30] transition">
                                        Edit Data
                                    </button>
                                    <button class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-[#003d30] transition">
                                        Baca PDF
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Dummy -->
                <div class="flex justify-center items-center mt-6 gap-2">
                    <button class="w-8 h-8 rounded bg-white text-gray-800 font-bold text-xs shadow flex items-center justify-center">1</button>
                    <button class="w-8 h-8 rounded bg-transparent text-white font-bold text-xs hover:bg-white/20 flex items-center justify-center">2</button>
                    <button class="w-8 h-8 rounded bg-transparent text-white font-bold text-xs hover:bg-white/20 flex items-center justify-center">3</button>
                    <button class="w-8 h-8 rounded bg-transparent text-white font-bold text-xs hover:bg-white/20 flex items-center justify-center">&gt;</button>
                </div>
            </div>

        </div>
    </main>
</div>
@endsection