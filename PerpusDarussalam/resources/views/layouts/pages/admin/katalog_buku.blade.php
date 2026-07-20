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
        <div class="p-8 space-y-6">
            
            <!-- Baris Pencarian & Tombol Buku Baru -->
            <div class="flex items-center gap-4">
                <div class="max-w-md w-full">
                    <form action="{{ route('book.index') }}" method="GET" class="flex items-center border-2 border-[#004d40] rounded overflow-hidden bg-white">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari Data Buku" class="w-full px-4 py-2 text-gray-700 outline-none font-medium placeholder-gray-400">
                        <button type="submit" class="bg-[#004d40] text-white px-4 py-2 flex items-center justify-center hover:bg-[#003d30] transition">
                            <span class="material-icons">search</span>
                        </button>
                    </form>
                </div>

                <!-- Tombol + Buku Baru (Pemicu Modal Tambah Buku) -->
                <button type="button" onclick="openAddModal()" class="border-2 border-[#004d40] text-[#004d40] font-bold px-4 py-2 rounded bg-white hover:bg-[#004d40] hover:text-white transition shadow-sm">
                    + Buku Baru
                </button>
            </div>

            <!-- Box Tabel -->
            <div class="bg-[#a2b4ba] p-6 rounded shadow-[0_4px_12px_rgba(0,0,0,0.15)] border border-gray-300/30">
                <h2 class="text-xl font-bold text-white mb-4 tracking-wide">Tabel Daftar Buku</h2>
                
                <div class="overflow-x-auto rounded">
                    <table class="min-w-full text-left border-collapse border border-white/40">
                        <thead>
                            <tr class="bg-[#004d40] text-white divide-x divide-white/40">
                                <th class="p-3 text-sm font-bold tracking-wider">Cover</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Judul</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Kategori</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Stok</th>
                                <th class="p-3 text-sm font-bold tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-white divide-y divide-white/40">
                            @forelse($books as $book)
                                <tr class="divide-x divide-white/40 hover:bg-white/10 transition-colors">
                                    <td class="p-3 text-sm text-center w-16">
                                        <div class="w-10 h-14 bg-gray-400/50 text-[10px] text-white flex items-center justify-center rounded border border-white/30 mx-auto">
                                            No Pic
                                        </div>
                                    </td>
                                    <td class="p-3 text-sm font-bold text-white/90">{{ $book->judul }}</td>
                                    <td class="p-3 text-sm text-white/90">{{ $book->kategori }}</td>
                                    <td class="p-3 text-sm font-bold text-white/90">{{ $book->stok }}</td>
                                    <td class="p-3 text-sm text-center">
                                        <!-- Tombol Pemicu Modal Edit Buku -->
                                        <button type="button" 
                                                onclick="openEditModal('{{ $book->judul }}', '{{ $book->deskripsi ?? '' }}', '{{ $book->kategori }}', '{{ $book->stok }}', '{{ $book->rak ?? '01' }}')"
                                                class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold tracking-wider hover:bg-[#003d30] transition shadow-sm">
                                            Edit Data
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-5 text-center text-sm font-semibold text-white/80">Data buku tidak ditemukan.</td>
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

<!-- ================= POP-UP MODAL TAMBAH BUKU (FOTO 2) ================= -->
<div id="addModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
    <div class="bg-[#005a4e] text-white rounded-md shadow-2xl w-full max-w-xs p-5 relative border border-emerald-400/30">
        <!-- Tombol Close (X) -->
        <button type="button" onclick="closeAddModal()" class="absolute top-3 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
            &#10005;
        </button>

        <h3 class="text-xl font-bold mb-4 tracking-wide">Tambah Buku</h3>

        <form action="{{ route('book.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Cover</label>
                <input type="file" name="cover" class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-2 py-1.5 rounded outline-none file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-600 file:text-white hover:file:bg-gray-700">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Judul</label>
                <input type="text" name="judul" placeholder="..." required class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none placeholder-gray-600 focus:ring-2 focus:ring-white">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Deskripsi</label>
                <input type="text" name="deskripsi" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none placeholder-gray-600 focus:ring-2 focus:ring-white">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Kategori</label>
                <select name="kategori" class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    <option value="">...</option>
                    <option value="Novel">Novel</option>
                    <option value="Pelajaran">Pelajaran</option>
                    <option value="Agama">Agama</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Stok</label>
                <input type="number" name="stok" placeholder="..." required class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none placeholder-gray-600 focus:ring-2 focus:ring-white">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Rak</label>
                <input type="text" name="rak" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none placeholder-gray-600 focus:ring-2 focus:ring-white">
            </div>

            <div class="pt-2 text-center">
                <button type="submit" class="bg-white text-[#004d40] hover:bg-emerald-50 px-6 py-1.5 rounded font-bold transition shadow-md w-full">
                    Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= POP-UP MODAL EDIT BUKU (FOTO 1) ================= -->
<div id="editModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
    <div class="bg-[#005a4e] text-white rounded-md shadow-2xl w-full max-w-xs p-5 relative border border-emerald-400/30">
        <!-- Tombol Close (X) -->
        <button type="button" onclick="closeEditModal()" class="absolute top-3 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
            &#10005;
        </button>

        <h3 class="text-xl font-bold mb-4 tracking-wide">Edit Data Buku</h3>

        <form action="{{ route('book.update') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Cover</label>
                <input type="file" name="cover" class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-2 py-1.5 rounded outline-none file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-600 file:text-white hover:file:bg-gray-700">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Judul</label>
                <input type="text" id="editJudul" name="judul" required class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Deskripsi</label>
                <input type="text" id="editDeskripsi" name="deskripsi" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none placeholder-gray-600 focus:ring-2 focus:ring-white">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Kategori</label>
                <select id="editKategori" name="kategori" class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    <option value="Novel">Novel</option>
                    <option value="Pelajaran">Pelajaran</option>
                    <option value="Agama">Agama</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Stok</label>
                <input type="number" id="editStok" name="stok" required class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Rak</label>
                <input type="text" id="editRak" name="rak" class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
            </div>

            <div class="pt-2 text-center">
                <button type="submit" class="bg-white text-[#004d40] hover:bg-emerald-50 px-6 py-1.5 rounded font-bold transition shadow-md w-full">
                    Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPT JS KONTROL MODAL -->
<script>
    // Modal Tambah Buku
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }
    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    // Modal Edit Buku
    function openEditModal(judul, deskripsi, kategori, stok, rak) {
        document.getElementById('editJudul').value = judul;
        document.getElementById('editDeskripsi').value = deskripsi;
        document.getElementById('editKategori').value = kategori;
        document.getElementById('editStok').value = stok;
        document.getElementById('editRak').value = rak;
        
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Menutup modal saat klik area luar/overlay
    window.onclick = function(event) {
        const addModal = document.getElementById('addModal');
        const editModal = document.getElementById('editModal');
        if (event.target === addModal) closeAddModal();
        if (event.target === editModal) closeEditModal();
    }
</script>
@endsection