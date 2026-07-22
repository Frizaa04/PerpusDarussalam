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

        <!-- Area Konten Sirkulasi -->
        <div class="p-8 space-y-6">
            
            <!-- Baris Pencarian & Tombol Peminjaman Baru -->
            <div class="flex items-center gap-4">
                <div class="max-w-md w-full">
                    <form id="searchForm" action="{{ route('circulation.index') }}" method="GET" class="flex items-center border-2 border-[#004d40] rounded overflow-hidden bg-white">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari Data Peminjaman" class="w-full px-4 py-2 text-gray-700 outline-none font-medium placeholder-gray-400">
                        @if(request('late'))
                            <input type="hidden" name="late" value="1">
                        @endif
                        <button type="submit" class="bg-[#004d40] text-white px-4 py-2 flex items-center justify-center hover:bg-[#003d30] transition">
                            <span class="material-icons">search</span>
                        </button>
                    </form>
                </div>

                <!-- Tombol + Peminjaman Baru -->
                <button type="button" onclick="openBorrowModal()" class="border-2 border-[#004d40] text-[#004d40] font-bold px-4 py-2 rounded bg-white hover:bg-[#004d40] hover:text-white transition shadow-sm">
                    + Peminjaman Baru
                </button>
            </div>

            <!-- Box Tabel Sirkulasi -->
            <div class="bg-[#a2b4ba] p-6 rounded shadow-[0_4px_12px_rgba(0,0,0,0.15)] border border-gray-300/30">
                
                <!-- Header Tabel & Filter Peminjaman Telat -->
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-white tracking-wide">Sirkulasi Peminjaman & Pengembalian</h2>
                    
                    <!-- Checkbox Peminjaman Telat (Sesuai Foto 1 & 2) -->
                    <form id="lateFilterForm" action="{{ route('circulation.index') }}" method="GET" class="flex items-center gap-2 text-white font-medium text-sm">
                        @if($search)
                            <input type="hidden" name="search" value="{{ $search }}">
                        @endif
                        <label for="lateCheckbox" class="cursor-pointer select-none">Peminjaman Telat</label>
                        <input type="checkbox" id="lateCheckbox" name="late" value="1" onchange="document.getElementById('lateFilterForm').submit()" {{ request('late') ? 'checked' : '' }} class="w-4 h-4 accent-[#004d40] cursor-pointer rounded">
                    </form>
                </div>
                
                <div class="overflow-x-auto rounded">
                    <table class="min-w-full text-left border-collapse border border-white/40">
                        <thead>
                            <tr class="bg-[#004d40] text-white divide-x divide-white/40">
                                <th class="p-3 text-sm font-bold tracking-wider w-12 text-center">No</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Nis</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Judul Buku</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Status</th>
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
                                <td class="p-3 text-sm text-white/90">{{ $item->book_title }}</td>
                                <td class="p-3 text-sm font-bold {{ $item->status == 'Telat' ? 'text-red-600' : 'text-white/90' }}">
                                    {{ $item->status }}
                                </td>
                                <td class="p-3 text-sm text-white/90">{{ $item->borrow_date }}</td>
                                <td class="p-3 text-sm text-white/90">{{ $item->return_date }}</td>
                                <td class="p-3 text-sm text-center">
                                    @if($item->status != 'Selesai' && $item->status != 'dikembalikan')
                                        <div class="flex justify-center items-center gap-1">
                                            <!-- Tombol Batalkan / Hapus -->
                                            <form action="{{ route('circulation.cancel',$item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan peminjaman ini?')">
                                                @csrf
                                                <button type="submit" class="bg-red-600 text-white p-1 rounded hover:bg-red-700 transition flex items-center justify-center w-6 h-6 text-xs font-bold shadow" title="Batalkan">
                                                    &#10005;
                                                </button>
                                            </form>

                                            <!-- Tombol Selesai / Dikembalikan -->
                                            <form action="{{ route('circulation.return', $item->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-[#004d40] text-white p-1 rounded hover:bg-[#003d30] transition flex items-center justify-center w-6 h-6 text-xs font-bold shadow" title="Selesai / Dikembalikan">
                                                    &#10003;
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-white/70">-</span>
                                    @endif
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

                <!-- Navigasi Paginasi -->
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

<!-- ================= POP-UP MODAL PEMINJAMAN BARU ================= -->
<div id="borrowModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
    <div class="bg-[#005a4e] text-white rounded-md shadow-2xl w-full max-w-xs p-5 relative border border-emerald-400/30">
        
        <button type="button" onclick="closeBorrowModal()" class="absolute top-3 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
            &#10005;
        </button>

        <h3 class="text-xl font-bold mb-4 tracking-wide">Peminjaman Baru</h3>

        <!-- Tampilkan Error Validasi jika ada -->
        @if ($errors->any())
            <div class="mb-3 p-2 bg-red-600 text-white rounded text-xs">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('circulation.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Nis</label>
                <input type="text" name="nis_nip" placeholder="..." value="{{ old('nis_nip') }}" required class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none placeholder-gray-600 focus:ring-2 focus:ring-white">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Nama</label>
                <input type="text" name="nama" placeholder="..." value="{{ old('nama') }}" class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none placeholder-gray-600 focus:ring-2 focus:ring-white">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Judul Buku</label>
                <input type="text" name="judul_buku" placeholder="..." value="{{ old('judul_buku') }}" required class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none placeholder-gray-600 focus:ring-2 focus:ring-white">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Tanggal Pinjam</label>
                <!-- Ubah name="due_date" menjadi name="tanggal_pinjam" -->
                <input type="date" name="tanggal_pinjam" value="{{ old('tanggal_pinjam') }}" class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
            </div>

            <div class="pt-2 text-center">
                <button type="submit" class="bg-white text-[#004d40] hover:bg-emerald-50 px-6 py-1.5 rounded font-bold transition shadow-md w-full">
                    Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPT JS MODAL -->
<script>
    function openBorrowModal() {
        document.getElementById('borrowModal').classList.remove('hidden');
    }

    function closeBorrowModal() {
        document.getElementById('borrowModal').classList.add('hidden');
    }

    window.onclick = function(event) {
        const modal = document.getElementById('borrowModal');
        if (event.target === modal) {
            closeBorrowModal();
        }
    }
</script>
@endsection