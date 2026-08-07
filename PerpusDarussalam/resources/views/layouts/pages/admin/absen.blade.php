@extends('layouts.pages.admin.provider.app')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">

    <!-- Pemanggilan Sidebar -->
    @include('layouts.pages.admin.provider.sidebar')

    <main class="flex-1 flex flex-col">

        <div class="p-8 space-y-6">

            {{-- ALERT / NOTIFIKASI FLASH MESSAGE --}}
            @if(session('success'))
                <div class="bg-emerald-600 text-white p-4 rounded-md shadow-md flex items-center justify-between">
                    <span class="font-semibold text-sm">{{ session('success') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-white hover:text-gray-200 font-bold">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-600 text-white p-4 rounded-md shadow-md flex items-center justify-between">
                    <span class="font-semibold text-sm">{{ session('error') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-white hover:text-gray-200 font-bold">&times;</button>
                </div>
            @endif
            {{-- AKHIR ALERT --}}

            <!-- Input untuk Barcode Scanner -->
            <form action="{{ route('absen.store') }}" method="POST" class="mb-4">
                @csrf
                <div class="flex items-center gap-2">
                    <input type="text" name="kode" id="scanner-input"
                        class="bg-gray-700 text-white px-4 py-2 rounded border border-gray-600 focus:outline-none focus:border-green-500 w-full md:w-1/3"
                        placeholder="Klik di sini lalu scan barcode..." autofocus autocomplete="off" required>
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold">
                        Proses
                    </button>
                </div>
            </form>

            <!-- Box Tabel Daftar Kunjungan -->
            <div class="bg-[#a2b4ba] p-6 rounded shadow-[0_4px_12px_rgba(0,0,0,0.15)] border border-gray-300/30">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
                    <h2 class="text-xl font-bold text-white tracking-wide">Daftar Kunjungan</h2>

                    <!-- Tombol Filter Peran -->
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-sm font-semibold text-white/90">Filter:</span>

                        <!-- Tombol Semua -->
                        <a href="{{ route('absen.index') }}"
                            class="px-3 py-1.5 rounded text-xs font-bold transition 
                        {{ request('role') == '' ? 'bg-white text-slate-800 shadow' : 'bg-[#004d40] text-white hover:bg-[#00332c]' }}">
                            Semua
                        </a>

                        <!-- Tombol Siswa -->
                        <a href="{{ route('absen.index', ['role' => 'siswa']) }}"
                            class="px-3 py-1.5 rounded text-xs font-bold transition 
                        {{ request('role') == 'siswa' ? 'bg-white text-slate-800 shadow' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
                            Siswa
                        </a>

                        <!-- Tombol Guru -->
                        <a href="{{ route('absen.index', ['role' => 'guru']) }}"
                            class="px-3 py-1.5 rounded text-xs font-bold transition 
                        {{ request('role') == 'guru' ? 'bg-white text-slate-800 shadow' : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                            Guru
                        </a>

                        <!-- Tombol Umum -->
                        <a href="{{ route('absen.index', ['role' => 'umum']) }}"
                            class="px-3 py-1.5 rounded text-xs font-bold transition 
                        {{ request('role') == 'umum' ? 'bg-white text-slate-800 shadow' : 'bg-amber-600 text-white hover:bg-amber-700' }}">
                            Umum
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto rounded">
                    <table class="min-w-full text-left border-collapse border border-white/40">
                        <thead>
                            <tr class="bg-[#004d40] text-white divide-x divide-white/40">
                                <th class="p-3 text-sm font-bold tracking-wider">Waktu</th>
                                <th class="p-3 text-sm font-bold tracking-wider">No Identitas</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Nama</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Peran</th> <!-- Kolom Baru -->
                            </tr>
                        </thead>
                        <tbody class="text-white divide-y divide-white/40">
                            @forelse($visits as $visit)
                            <tr class="divide-x divide-white/40 hover:bg-white/10 transition-colors">
                                <!-- Format Waktu -->
                                <td class="p-3 text-sm font-semibold text-white/90">
                                    {{ $visit->visited_at ? \Carbon\Carbon::parse($visit->visited_at)->format('d M Y, H:i') : '-' }}
                                </td>

                                <!-- Identitas -->
                                <td class="p-3 text-sm font-semibold text-white/90">
                                    {{ $visit->user->nisn ?? $visit->user->nik ?? '-' }}
                                </td>

                                <!-- Nama User -->
                                <td class="p-3 text-sm font-semibold text-white/90">
                                    {{ $visit->user->name ?? '-' }}
                                </td>

                                <!-- Peran User (Siswa / Guru / Umum) dengan Badge Kustom -->
                                <td class="p-3 text-sm font-semibold text-white/90">
                                    @if($visit->user)
                                    <span class="px-2.5 py-1 rounded text-xs uppercase font-bold 
                                                    @if($visit->user->role == 'guru') bg-blue-600 text-white 
                                                    @elseif($visit->user->role == 'siswa') bg-emerald-600 text-white 
                                                    @else bg-amber-600 text-white @endif">
                                        {{ $visit->user->role }}
                                    </span>
                                    @else
                                    -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-5 text-center text-sm font-semibold text-white/80">
                                    Belum ada data kunjungan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginasi -->
                @if ($visits->hasPages())
                <div class="flex justify-center items-center gap-3 mt-6 text-white font-bold select-none">
                    {{-- Tombol Previous (<) --}}
                    @if ($visits->onFirstPage())
                    <span class="px-2 py-1 text-white/40 cursor-not-allowed">&lt;</span>
                    @else
                    <a href="{{ $visits->previousPageUrl() }}"
                        class="px-2 py-1 text-white hover:text-white/80 transition">&lt;</a>
                    @endif

                    {{-- Nomor Halaman (1, 2, dst) --}}
                    @foreach ($visits->getUrlRange(1, $visits->lastPage()) as $page => $url)
                    @if ($page == $visits->currentPage())
                    {{-- Halaman Aktif: Kotak Putih Teks Gelap --}}
                    <span
                        class="w-8 h-8 flex items-center justify-center bg-white text-slate-800 font-extrabold rounded-md shadow">
                        {{ $page }}
                    </span>
                    @else
                    {{-- Halaman Lain: Teks Putih Tanpa Background --}}
                    <a href="{{ $url }}"
                        class="w-8 h-8 flex items-center justify-center text-white hover:bg-white/10 rounded-md transition">
                        {{ $page }}
                    </a>
                    @endif
                    @endforeach

                    {{-- Tombol Next (>) --}}
                    @if ($visits->hasMorePages())
                    <a href="{{ $visits->nextPageUrl() }}"
                        class="px-2 py-1 text-white hover:text-white/80 transition">&gt;</a>
                    @else
                    <span class="px-2 py-1 text-white/40 cursor-not-allowed">&gt;</span>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </main>
</div>

<script>
    // Memastikan kursor selalu fokus ke input scanner agar siap tembak kapan saja
    document.addEventListener("click", function () {
        document.getElementById("scanner-input").focus();
    });
</script>

@endsection