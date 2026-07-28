@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Pemanggilan Sidebar -->
    @include('layouts.sidebar')

    <main class="flex-1 flex flex-col">

        <!-- Area Konten -->
        <div class="p-8 space-y-6">
            
            <!-- Pencarian & Tombol Buku Baru -->
            <div class="flex items-center gap-4">
                <div class="max-w-md w-full">
                    <form action="{{ route('book.index') }}" method="GET" class="flex items-center border-2 border-[#004d40] rounded overflow-hidden bg-white">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari Data Buku" class="w-full px-4 py-2 text-gray-700 outline-none font-medium placeholder-gray-400">
                        <button type="submit" class="bg-[#004d40] text-white px-4 py-2 flex items-center justify-center hover:bg-[#003d30] transition">
                            <span class="material-icons">search</span>
                        </button>
                    </form>
                </div>

                <!-- Tombol dan Buku Baru -->
                <button type="button" onclick="openAddModal()" class="border-2 border-[#004d40] text-[#004d40] font-bold px-4 py-2 rounded bg-white hover:bg-[#004d40] hover:text-white transition shadow-sm">
                    + Buku Baru
                </button>
            </div>

            <!-- Form/Wrapper Tabel untuk Aksi Hapus Massal -->
            <form id="deleteForm" action="{{ route('book.destroyMultiple') }}" method="POST">
                @csrf
                @method('DELETE')

                <!-- Box Tabel -->
                <div class="bg-[#a2b4ba] p-6 rounded shadow-[0_4px_12px_rgba(0,0,0,0.15)] border border-gray-300/30">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-white tracking-wide">Tabel Daftar Buku</h2>
                        

                        <!-- Tombol Aksi di Header (Hapus Buku & Konfirmasi Hapus Berdampingan) -->
                        <div class="flex items-center gap-2">
                            <!-- Pengecekan Select All (Hanya muncul saat mode hapus aktif) -->
                            <div id="selectAllContainer" class="hidden flex items-center gap-1.5 px-2.5 py-1 select-none">
                                <input type="checkbox" id="selectAllCheckbox" onclick="toggleSelectAll(this)" class="w-4 h-4 accent-red-600 cursor-pointer rounded">
                                <label for="selectAllCheckbox" class="text-xs font-semibold text-white cursor-pointer">Pilih Semua</label>
                            </div>
                            <!-- Tombol Konfirmasi Hapus (Awalnya Tersembunyi di Sebelah Kiri Tombol Hapus Buku) -->
                            <button type="submit" id="btnConfirmDelete" onclick="return confirm('Yakin ingin menghapus buku yang dipilih?')" class="hidden bg-red-700 hover:bg-red-800 text-white font-bold px-3 py-1.5 rounded text-sm transition shadow-md">
                                Konfirmasi Hapus
                            </button>

                            <!-- Tombol Mode Hapus -->
                            <button type="button" id="btnToggleDelete" onclick="toggleDeleteMode()" class="bg-[#004d40] hover:bg-[#003d30] text-white font-bold px-3 py-1.5 rounded text-sm transition shadow flex items-center gap-1.5 select-none">
                                <svg id="trashIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Hapus Buku</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto rounded">
                        <table class="min-w-full text-left border-collapse border border-white/40">
                            <thead>
                                <thead>
                                    <tr class="bg-[#004d40] text-white divide-x divide-white/40">
                                        <th class="p-3 text-sm font-bold tracking-wider">Cover</th>
                                        <th class="p-3 text-sm font-bold tracking-wider">Judul</th>
                                        <!-- KOLOM BARU -->
                                        <th class="p-3 text-sm font-bold tracking-wider">Penulis & ISBN</th>
                                        <th class="p-3 text-sm font-bold tracking-wider">Kategori</th>
                                        <th class="p-3 text-sm font-bold tracking-wider">Rak</th>
                                        <th class="p-3 text-sm font-bold tracking-wider">Stok</th>
                                        <th class="p-3 text-sm font-bold tracking-wider text-center">Aksi</th>
                                    </tr>
                                </thead>
                            <tbody class="text-white divide-y divide-white/40">
                                @forelse($books as $book)
                                    <tr class="divide-x divide-white/40 hover:bg-white/10 transition-colors">
                                        <td class="p-3 text-sm text-center w-20">
                                            @if($book->cover)
                                                <img src="{{ asset('storage/' . $book->cover) }}" alt="Cover" class="w-14 h-20 object-cover rounded border border-white/30 mx-auto shadow-md">
                                            @else
                                                <div class="w-14 h-20 bg-gray-400/50 text-[10px] text-white flex items-center justify-center rounded border border-white/30 mx-auto shadow-md">
                                                    No Pic
                                                </div>
                                            @endif
                                        </td>
                                        <td class="p-3 text-sm font-bold text-white/90">{{ $book->judul }}</td>
                                        <!-- KOLOM BARU: PENULIS & ISBN -->
                                        <td class="p-3 text-sm text-white/90">
                                            <div class="font-semibold">{{ $book->penulis }}</div>
                                            <div class="text-xs text-white/70 mt-0.5">ISBN: {{ $book->isbn }}</div>
                                        </td>

                                        <!-- KATEGORI -->
                                        <td class="p-3 text-sm text-white/90">
                                            {{ $book->categories->first()->nama ?? ($book->kategori ?? '-') }}
                                        </td>

                                        <!-- RAK -->
                                        <td class="p-3 text-sm font-bold text-white/90">
                                            {{ $book->rak ?? '-' }}
                                        </td>

                                        <!-- STOK -->
                                        <td class="p-3 text-sm font-bold text-white/90">{{ $book->stok }}</td>

                                        <!-- AKSI -->
                                        <td class="p-3 text-sm text-center">
                                            <!-- Mode Normal: Tombol Edit Data & Kelola -->
                                            <div class="edit-mode-action flex items-center justify-center gap-2">
                                                <button type="button" 
                                                        onclick="openEditModal('{{ $book->id }}', '{{ $book->judul }}', '{{ $book->penulis }}', '{{ $book->penerbit }}', '{{ $book->deskripsi ?? '' }}', '{{ $book->isbn }}', '{{ $book->tanggal_pembelian }}', '{{ $book->categories_id }}', '{{ $book->stok }}', '{{ $book->rak ?? '' }}', '{{ $book->kode_buku }}', '{{ $book->tahun_terbit }}')"
                                                        class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold tracking-wider hover:bg-[#003d30] transition shadow-sm">
                                                    Edit Data
                                                </button>

                                                <button type="button" 
                                                        onclick="openKelolaModal('{{ $book->id }}', '{{ $book->judul }}')"
                                                        class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold tracking-wider hover:bg-[#003d30] transition shadow-sm">
                                                    Kelola data buku
                                                </button>
                                            </div>

                                            <!-- Mode Hapus: Checkbox di Kolom Aksi -->
                                            <div class="delete-mode-action hidden flex items-center justify-center gap-2">
                                                <input type="checkbox" name="ids[]" value="{{ $book->id }}" class="book-checkbox w-5 h-5 accent-red-600 cursor-pointer rounded border-2 border-white">
                                                <span class="text-xs font-semibold text-red-200 italic">Centang untuk hapus</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-5 text-center text-sm font-semibold text-white/80">Data buku tidak ditemukan.</td>
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
            </form>
        </div>
    </main>
</div>

<!-- ====== POP-UP MODAL TAMBAH BUKU ====== -->
<div id="addModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
    <div class="bg-[#005a4e] text-white rounded-md shadow-2xl w-full max-w-xl p-6 relative border border-emerald-400/30">
        <!-- Tombol Close -->
        <button type="button" onclick="closeAddModal()" class="absolute top-3 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
            &#10005;
        </button>

        <h3 class="text-xl font-bold mb-5 tracking-wide">Tambah Buku Baru</h3>

        <form action="{{ route('book.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <!-- Form Grid 2 Kolom -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Cover</label>
                    <input type="file" name="cover" class="w-full bg-[#b0bec5] text-gray-800 text-xs font-medium px-2 py-1.5 rounded outline-none file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-600 file:text-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">ISBN</label>
                    <input type="text" name="isbn" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Judul</label>
                    <input type="text" name="judul" placeholder="..." required class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Tahun Pembelian</label>
                    <input type="date" name="tanggal_pembelian" class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Tahun Terbit</label>
                    <input type="number" name="tahun_terbit" placeholder="..." required class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Penulis</label>
                    <input type="text" name="penulis" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Kategori</label>
                    <select name="kategori" class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                        <option value="">...</option>
                        @foreach($allCategories as $cat)
                            <option value="{{ $cat->nama }}">{{ $cat->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Penerbit</label>
                    <input type="text" name="penerbit" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Stok</label>
                    <input type="number" name="stok" placeholder="..." required class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Deskripsi</label>
                    <input type="text" name="deskripsi" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Rak</label>
                    <input type="text" name="rak" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>
            </div>

            <div class="pt-4 text-center">
                <button type="submit" class="bg-white text-[#004d40] hover:bg-emerald-50 px-8 py-2 rounded font-bold transition shadow-md">
                    Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ====== POP-UP MODAL EDIT BUKU ====== -->
<div id="editModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
    <div class="bg-[#005a4e] text-white rounded-md shadow-2xl w-full max-w-xl p-6 relative border border-emerald-400/30">
        <!-- Tombol Close -->
        <button type="button" onclick="closeEditModal()" class="absolute top-3 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
            &#10005;
        </button>

        <h3 class="text-xl font-bold mb-5 tracking-wide">Edit Data Buku</h3>

        <form id="editForm" action="{{ route('book.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <input type="hidden" id="editBookId" name="id">
            <input type="hidden" id="editKodeBuku" name="kode_buku">

            <!-- Grid 2 Kolom -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Cover</label>
                    <input type="file" name="cover" class="w-full bg-[#b0bec5] text-gray-800 text-xs font-medium px-2 py-1.5 rounded outline-none file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-600 file:text-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">ISBN</label>
                    <input type="text" id="editIsbn" name="isbn" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Judul</label>
                    <input type="text" id="editJudul" name="judul" required class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Tahun Pembelian</label>
                    <input type="date" id="editTanggalPembelian" name="tanggal_pembelian" class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Tahun Terbit</label>
                    <input type="number" id="editTahunTerbit" name="tahun_terbit" placeholder="2024" required class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Penulis</label>
                    <input type="text" id="editPenulis" name="penulis" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Kategori</label>
                    <select id="editCategoriesId" name="categories_id" class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                        @foreach($allCategories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Penerbit</label>
                    <input type="text" id="editPenerbit" name="penerbit" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Stok</label>
                    <input type="number" id="editStok" name="stok" required class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Deskripsi</label>
                    <input type="text" id="editDeskripsi" name="deskripsi" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Rak</label>
                    <input type="text" id="editRak" name="rak" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>
            </div>

            <div class="pt-4 text-center">
                <button type="submit" class="bg-white text-[#004d40] hover:bg-emerald-50 px-8 py-2 rounded font-bold transition shadow-md">
                    Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ====== POP-UP MODAL KELOLA BUKU ITEM (SATUAN/EKSEMPLAR) ====== -->
<div id="kelolaModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
    <div class="bg-[#005a4e] text-white rounded-md shadow-2xl w-full max-w-2xl p-6 relative border border-emerald-400/30 max-h-[90vh] overflow-y-auto">
        <!-- Tombol Close -->
        <button type="button" onclick="closeKelolaModal()" class="absolute top-3 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
            &#10005;
        </button>

        <h3 class="text-xl font-bold mb-1 tracking-wide">Kelola Data Buku Item</h3>
        <p id="kelolaBookTitle" class="text-xs text-emerald-200 mb-4 font-medium"></p>

        <!-- Form Tambah Item Baru untuk Buku Ini -->
        <form action="{{ route('book.item.store') }}" method="POST" class="bg-[#004d40] p-4 rounded-md mb-5 border border-white/25 space-y-3 shadow-inner">
            @csrf
            <input type="hidden" id="kelolaBookId" name="book_id">
            <h4 class="text-sm font-bold tracking-wide border-b border-white/20 pb-1">Tambah Eksemplar / Item Baru</h4>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold mb-1">Nomor Inventaris</label>
                    <input type="text" name="nomor_inventaris" placeholder="Contoh: INV-001" required class="w-full bg-[#b0bec5] text-gray-800 text-xs font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Kondisi</label>
                    <select name="kondisi" class="w-full bg-[#b0bec5] text-gray-800 text-xs font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                        <option value="baik">Baik</option>
                        <option value="rusak_ringan">Rusak Ringan</option>
                        <option value="rusak_berat">Rusak Berat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Status Pinjam</label>
                    <select name="status_pinjam" class="w-full bg-[#b0bec5] text-gray-800 text-xs font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                        <option value="tersedia">Tersedia</option>
                        <option value="dipinjam">Dipinjam</option>
                    </select>
                </div>
            </div>
            <div class="text-right pt-1">
                <button type="submit" class="bg-white text-[#004d40] hover:bg-emerald-50 px-4 py-1.5 rounded text-xs font-bold transition shadow">
                    + Tambah Item
                </button>
            </div>
        </form>

        <!-- List / Daftar Item Eksemplar Buku -->
        <h4 class="text-sm font-bold mb-2 tracking-wide">Pilih Eksemplar untuk Diedit / Dihapus</h4>
        <div class="overflow-x-auto rounded border border-white/30">
            <table class="min-w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#003d30] text-white border-b border-white/30">
                        <th class="p-2.5">No. Inventaris</th>
                        <th class="p-2.5">Kondisi</th>
                        <th class="p-2.5">Status</th>
                        <th class="p-2.5 text-center">Aksi (Pilih Bagian)</th>
                    </tr>
                </thead>
                <tbody id="kelolaItemList" class="divide-y divide-white/20 bg-[#005a4e]">
                    <tr>
                        <td colspan="4" class="p-4 text-center text-white/70">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ====== SUB-MODAL EDIT ITEM SPESIFIK (MUNCUL KETIKA TOMBOL EDIT DI KLIK) ====== -->
<div id="editItemModal" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-[60] p-4">
    <div class="bg-[#004d40] text-white rounded-md shadow-2xl w-full max-w-sm p-5 relative border border-white/30">
        <button type="button" onclick="closeEditItemModal()" class="absolute top-2.5 right-3 text-white hover:text-gray-300 text-lg font-bold">
            &#10005;
        </button>

        <h3 class="text-base font-bold mb-3 tracking-wide border-b border-white/20 pb-1">Edit Detail Eksemplar</h3>

        <form id="formEditItem" method="POST" class="space-y-3 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-semibold mb-1">Nomor Inventaris</label>
                <input type="text" id="editNomorInventaris" name="nomor_inventaris" required class="w-full bg-[#b0bec5] text-gray-800 font-medium px-2.5 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
            </div>
            <div>
                <label class="block font-semibold mb-1">Kondisi</label>
                <select id="editKondisiItem" name="kondisi" class="w-full bg-[#b0bec5] text-gray-800 font-medium px-2.5 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    <option value="baik">Baik</option>
                    <option value="rusak_ringan">Rusak Ringan</option>
                    <option value="rusak_berat">Rusak Berat</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold mb-1">Status Pinjam</label>
                <select id="editStatusPinjamItem" name="status_pinjam" class="w-full bg-[#b0bec5] text-gray-800 font-medium px-2.5 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    <option value="tersedia">Tersedia</option>
                    <option value="dipinjam">Dipinjam</option>
                </select>
            </div>

            <div class="text-center pt-2">
                <button type="submit" class="bg-white text-[#004d40] hover:bg-emerald-50 px-5 py-1.5 rounded font-bold transition shadow">
                    Simpan Perubahan Item
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPT JS KONTROL MODAL & FITUR HAPUS -->
<script>
    // Mode Hapus / Checkbox Aksi
    function toggleDeleteMode(checkbox) {
        const editActions = document.querySelectorAll('.edit-mode-action');
        const deleteActions = document.querySelectorAll('.delete-mode-action');
        const confirmBtn = document.getElementById('deleteConfirmContainer');

        if (checkbox.checked) {
            editActions.forEach(el => el.classList.add('hidden'));
            deleteActions.forEach(el => el.classList.remove('hidden'));
            confirmBtn.classList.remove('hidden');
        } else {
            editActions.forEach(el => el.classList.remove('hidden'));
            deleteActions.forEach(el => el.classList.add('hidden'));
            confirmBtn.classList.add('hidden');
        }
    }

    // Modal Tambah Buku
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }
    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    // Modal Edit Data
    function openEditModal(id, judul, penulis, penerbit, deskripsi, isbn, tglPembelian, catId, stok, rak, kodeBuku, tahunTerbit) {
        document.getElementById('editBookId').value = id;
        document.getElementById('editJudul').value = judul;
        document.getElementById('editPenulis').value = penulis;
        document.getElementById('editPenerbit').value = penerbit;
        document.getElementById('editDeskripsi').value = deskripsi;
        document.getElementById('editIsbn').value = isbn;
        document.getElementById('editTanggalPembelian').value = tglPembelian;
        document.getElementById('editCategoriesId').value = catId;
        document.getElementById('editStok').value = stok;
        document.getElementById('editRak').value = rak;
        document.getElementById('editKodeBuku').value = kodeBuku;
        document.getElementById('editTahunTerbit').value = tahunTerbit;

        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Membuka modal utama list item buku
    function openKelolaModal(bookId, bookTitle) {
        document.getElementById('kelolaBookId').value = bookId;
        document.getElementById('kelolaBookTitle').innerText = "Judul Buku: " + bookTitle;
        document.getElementById('kelolaModal').classList.remove('hidden');

        let tbody = document.getElementById('kelolaItemList');
        tbody.innerHTML = `<tr><td colspan="4" class="p-4 text-center text-white/70">Memuat data eksemplar...</td></tr>`;

        // Ambil data item berdasarkan ID buku lewat endpoint
        fetch(`/book/${bookId}/items`)
            .then(response => response.json())
            .then(data => {
                tbody.innerHTML = '';
                if(data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="4" class="p-4 text-center text-white/70">Belum ada eksemplar terdaftar untuk buku ini.</td></tr>`;
                    return;
                }

                // Render list item ke dalam tabel di dalam pop-up
                data.forEach(item => {
                    let badgeColor = item.status_pinjam === 'tersedia' ? 'bg-emerald-600' : 'bg-amber-600';
                    let kondisiFormatted = item.kondisi.replace('_', ' ');

                    tbody.innerHTML += `
                        <tr class="hover:bg-white/10 transition-colors">
                            <td class="p-2.5 font-bold">${item.nomor_inventaris}</td>
                            <td class="p-2.5 capitalize">${kondisiFormatted}</td>
                            <td class="p-2.5 uppercase"><span class="px-2 py-0.5 rounded text-[10px] font-bold ${badgeColor} text-white">${item.status_pinjam}</span></td>
                            <td class="p-2.5 text-center space-x-1">
                                <!-- Tombol Pilih untuk Edit Item Tertentu -->
                                <button type="button" 
                                    onclick="openEditItemModal(${item.id}, '${item.nomor_inventaris}', '${item.kondisi}', '${item.status_pinjam}')" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-2.5 py-1 rounded text-[10px] font-bold shadow">
                                    Edit Bagian Ini
                                </button>
                                
                                <!-- Tombol Hapus Item -->
                                <form action="/book/item/${item.id}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus eksemplar ini?')">
                                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-[10px] font-bold shadow">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    `;
                });
            })
            .catch(error => {
                console.error('Error:', error);
                tbody.innerHTML = `<tr><td colspan="4" class="p-4 text-center text-red-300">Gagal memuat data eksemplar.</td></tr>`;
            });
    }

    function closeKelolaModal() {
        document.getElementById('kelolaModal').classList.add('hidden');
    }

    // Membuka sub-modal edit item spesifik yang dipilih dari list
    function openEditItemModal(itemId, nomorInv, kondisi, statusPinjam) {
        document.getElementById('editNomorInventaris').value = nomorInv;
        document.getElementById('editKondisiItem').value = kondisi;
        document.getElementById('editStatusPinjamItem').value = statusPinjam;
        
        // Arahkan action form update ke URL route item yang bersangkutan
        document.getElementById('formEditItem').action = `/book/item/${itemId}`;
        
        // Tampilkan modal edit item kecil di atas modal kelola
        document.getElementById('editItemModal').classList.remove('hidden');
    }

    function closeEditItemModal() {
        document.getElementById('editItemModal').classList.add('hidden');
    }

    // Menutup modal saat klik overlay luar
    window.onclick = function(event) {
        const addModal = document.getElementById('addModal');
        const editModal = document.getElementById('editModal');
        if (event.target === addModal) closeAddModal();
        if (event.target === editModal) closeEditModal();
    }

    let isDeleteModeActive = false;

    function toggleDeleteMode() {
        isDeleteModeActive = !isDeleteModeActive;

        let btnToggle = document.getElementById('btnToggleDelete');
        let btnConfirm = document.getElementById('btnConfirmDelete');
        let selectAllContainer = document.getElementById('selectAllContainer'); // <-- Tambahan ini
        let editActions = document.querySelectorAll('.edit-mode-action');
        let deleteActions = document.querySelectorAll('.delete-mode-action');

        if (isDeleteModeActive) {
            btnToggle.classList.remove('bg-[#004d40]', 'hover:bg-[#003d30]');
            btnToggle.classList.add('bg-red-600', 'hover:bg-red-700');

            btnConfirm.classList.remove('hidden');
            selectAllContainer.classList.remove('hidden'); // <-- Munculkan centang all

            editActions.forEach(el => el.classList.add('hidden'));
            deleteActions.forEach(el => el.classList.remove('hidden'));
        } else {
            btnToggle.classList.remove('bg-red-600', 'hover:bg-red-700');
            btnToggle.classList.add('bg-[#004d40]', 'hover:bg-[#003d30]');

            btnConfirm.classList.add('hidden');
            selectAllContainer.classList.add('hidden'); // <-- Sembunyikan centang all

            editActions.forEach(el => el.classList.remove('hidden'));
            deleteActions.forEach(el => el.classList.add('hidden'));

            document.getElementById('selectAllCheckbox').checked = false;
            document.querySelectorAll('.book-checkbox').forEach(cb => cb.checked = false);
        }
    }
    function toggleSelectAll(source) {
    let checkboxes = document.querySelectorAll('.book-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = source.checked;
    });
}
</script>
@endsection