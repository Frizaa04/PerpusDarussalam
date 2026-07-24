@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Pemanggilan Sidebar -->
    @include('layouts.sidebar')

    <main class="flex-1 flex flex-col">

        <!-- Header Atas -->
        <header class="bg-white border-b border-gray-200 px-8 flex justify-end items-center gap-4 shadow-sm h-20">
            <button type="button" class="text-gray-600 hover:text-[#004d40] transition">
                <span class="material-icons text-2xl">notifications</span>
            </button>
            <form action="{{ Route::has('logout') ? route('logout') : '#' }}" method="POST">
                @csrf
                <button type="submit" class="bg-[#005a4e] hover:bg-[#004d40] text-white px-4 py-1.5 rounded font-bold text-sm tracking-wide transition shadow-sm">
                    LogOut
                </button>
            </form>
            <div class="flex items-center h-full pl-2">
                <img src="{{ asset('image/covers/darussalam.png') }}" alt="Logo Darussalam" class="h-14 py-1 object-contain">
            </div>
        </header>

        <div class="p-8 space-y-6">
            
            <!-- Filter Tanggal/Hari (Kotak Tanggal Atas) -->
            <div class="inline-flex bg-[#004d40] rounded text-white overflow-hidden shadow-sm">
                <a href="#" class="px-4 py-2 hover:bg-[#003d30] font-bold border-r border-white/20 transition">13</a>
                <a href="#" class="px-4 py-2 hover:bg-[#003d30] font-bold border-r border-white/20 transition">14</a>
                <a href="#" class="px-4 py-2 hover:bg-[#003d30] font-bold border-r border-white/20 transition">15</a>
                <a href="#" class="px-4 py-2 hover:bg-[#003d30] font-bold border-r border-white/20 transition">16</a>
                <a href="#" class="px-4 py-2 hover:bg-[#003d30] font-bold border-r border-white/20 transition">17</a>
                <a href="#" class="px-4 py-2 hover:bg-[#003d30] font-bold border-r border-white/20 transition">18</a>
                <a href="#" class="px-4 py-2 hover:bg-[#003d30] font-bold transition">19</a>
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
                                <th class="p-3 text-sm font-bold tracking-wider">Nis</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Nama</th>
                            </tr>
                        </thead>
                        <tbody class="text-white divide-y divide-white/40">
                           @forelse($visits as $visit)
                                <tr class="divide-x divide-white/40 hover:bg-white/10 transition-colors">
                                    <!-- Format Waktu: 20 Jul 2026, 13:20 -->
                                    <td class="p-3 text-sm font-semibold text-white/90">
                                        {{ $visit->visited_at ? \Carbon\Carbon::parse($visit->visited_at)->format('d M Y, H:i') : '-' }}
                                    </td>
                                    
                                    <!-- Identitas (Mendukung NIS, NIP, atau NIK) -->
                                    <td class="p-3 text-sm font-semibold text-white/90">
                                        {{ $visit->user->nis ?? $visit->user->nip ?? $visit->user->nik ?? '-' }}
                                    </td>
                                    
                                    <!-- Nama User -->
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

                <!-- Paginasi -->
                <div class="flex justify-center items-center gap-2 mt-6 text-white font-bold">
                    <span class="px-2.5 py-1 bg-white text-gray-700 rounded text-sm shadow">1</span>
                    <a href="#" class="px-2.5 py-1 hover:bg-white/20 rounded text-sm transition">2</a>
                    <a href="#" class="px-2.5 py-1 hover:bg-white/20 rounded text-sm transition">3</a>
                    <a href="#" class="px-2.5 py-1 hover:bg-white/20 rounded text-sm transition">&gt;</a>
                </div>

            </div>
        </div>
    </main>
</div>

<script>
    // Memastikan kursor selalu fokus ke input scanner agar siap tembak kapan saja
    document.addEventListener("click", function() {
        document.getElementById("scanner-input").focus();
    });
</script>

@endsection