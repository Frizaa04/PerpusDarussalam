@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Pemanggilan Sidebar -->
    @include('layouts.sidebar')

    <main class="flex-1 flex flex-col">

        <!-- Header Atas (Disesuaikan dengan Foto 2/5: Lonceng + LogOut) -->
        <header class="bg-white border-b border-gray-200 px-8 flex justify-end items-center gap-4 shadow-sm h-20">
            <button type="button" class="text-gray-600 hover:text-[#004d40] transition">
                <span class="material-icons text-2xl">notifications</span>
            </button>
            <form action="{{ route('logout') ?? '#' }}" method="POST">
                @csrf
                <button type="submit" class="bg-[#005a4e] hover:bg-[#004d40] text-white px-4 py-1.5 rounded font-bold text-sm tracking-wide transition shadow-sm">
                    LogOut
                </button>
            </form>
            <div class="flex items-center h-full pl-2">
                <img src="{{ asset('image/covers/darussalam.png') }}" alt="Logo Darussalam" class="h-14 py-1 object-contain">
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

                <!-- Tombol + Buku Baru (Pemicu Modal Tambah Buku Foto 4) -->
                <button type="button" onclick="openAddModal()" class="border-2 border-[#004d40] text-[#004d40] font-bold px-4 py-2 rounded bg-white hover:bg-[#004d40] hover:text-white transition shadow-sm">
                    + Buku Baru
                </button>
            </div>

            <!-- Form/Wrapper Tabel untuk Aksi Hapus Massal -->
            <form id="deleteForm" action="#" method="POST">
                @csrf
                @method('DELETE')

                <!-- Box Tabel -->
                <div class="bg-[#a2b4ba] p-6 rounded shadow-[0_4px_12px_rgba(0,0,0,0.15)] border border-gray-300/30">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-white tracking-wide">Tabel Daftar Buku</h2>
                        
                        <!-- Toggle Mode Hapus Buku (Sesuai Foto 2 & Foto 5) -->
                        <div class="flex items-center gap-2">
                            <label for="toggleDeleteMode" class="text-white font-bold text-sm select-none cursor-pointer">Hapus Buku</label>
                            <input type="checkbox" id="toggleDeleteMode" onchange="toggleDeleteMode(this)" class="w-5 h-5 rounded accent-[#004d40] cursor-pointer">
                        </div>
                    </div>
                    
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
                                            @if($book->cover)
                                                <img src="{{ asset('storage/' . $book->cover) }}" alt="Cover" class="w-10 h-14 object-cover rounded border border-white/30 mx-auto">
                                            @else
                                                <div class="w-10 h-14 bg-gray-400/50 text-[10px] text-white flex items-center justify-center rounded border border-white/30 mx-auto">
                                                    No Pic
                                                </div>
                                            @endif
                                        </td>
                                        <td class="p-3 text-sm font-bold text-white/90">{{ $book->judul }}</td>
                                        <td class="p-3 text-sm text-white/90">
                                            {{ $book->categories->first()->nama ?? ($book->kategori ?? '-') }}
                                        </td>
                                        <td class="p-3 text-sm font-bold text-white/90">{{ $book->stok }}</td>
                                        <td class="p-3 text-sm text-center">
                                            <!-- Mode Normal: Tombol Edit Data -->
                                            <div class="edit-mode-action">
                                                <button type="button" 
                                                        onclick="openEditModal('{{ $book->id }}', '{{ $book->judul }}', '{{ $book->penulis }}', '{{ $book->penerbit }}', '{{ $book->deskripsi ?? '' }}', '{{ $book->isbn }}', '{{ $book->tanggal_pembelian }}', '{{ $book->categories_id }}', '{{ $book->stok }}', '{{ $book->rak ?? '' }}', '{{ $book->kode_buku }}')"
                                                        class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold tracking-wider hover:bg-[#003d30] transition shadow-sm">
                                                    Edit Data
                                                </button>
                                            </div>

                                            <!-- Mode Hapus: Kotak Pilihan/Checkbox (Foto 5) -->
                                            <div class="delete-mode-action hidden flex justify-center">
                                                <input type="checkbox" name="book_ids[]" value="{{ $book->id }}" class="w-5 h-5 accent-[#004d40] cursor-pointer rounded border-2 border-white">
                                            </div>
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

                    <!-- Tombol Konfirmasi Hapus Buku (Hanya Tampil Saat Mode Hapus Aktif) -->
                    <div id="deleteConfirmContainer" class="hidden mt-4 text-right">
                        <button type="submit" onclick="return confirm('Yakin ingin menghapus buku yang dipilih?')" class="bg-red-700 hover:bg-red-800 text-white font-bold px-5 py-2 rounded text-sm transition shadow-md">
                            Konfirmasi Hapus
                        </button>
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

<!-- ================= POP-UP MODAL TAMBAH BUKU (FOTO 4) ================= -->
<div id="addModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
    <div class="bg-[#005a4e] text-white rounded-md shadow-2xl w-full max-w-xl p-6 relative border border-emerald-400/30">
        <!-- Tombol Close (X) -->
        <button type="button" onclick="closeAddModal()" class="absolute top-3 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
            &#10005;
        </button>

        <h3 class="text-xl font-bold mb-5 tracking-wide">Tambah Buku Baru</h3>

        <form action="{{ route('book.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <!-- Form Grid 2 Kolom -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Kolom Kiri -->
                <div>
                    <label class="block text-sm font-semibold mb-1">Cover</label>
                    <input type="file" name="cover" class="w-full bg-[#b0bec5] text-gray-800 text-xs font-medium px-2 py-1.5 rounded outline-none file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-600 file:text-white">
                </div>
                <!-- Kolom Kanan -->
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

<!-- ================= POP-UP MODAL EDIT BUKU (FOTO 3) ================= -->
<div id="editModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
    <div class="bg-[#005a4e] text-white rounded-md shadow-2xl w-full max-w-xl p-6 relative border border-emerald-400/30">
        <!-- Tombol Close (X) -->
        <button type="button" onclick="closeEditModal()" class="absolute top-3 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
            &#10005;
        </button>

        <h3 class="text-xl font-bold mb-5 tracking-wide">Edit Data Buku</h3>

        <form id="editForm" action="{{ route('book.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <input type="hidden" id="editBookId" name="id">
            <input type="hidden" id="editKodeBuku" name="kode_buku">

            <!-- Form Grid 2 Kolom -->
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

<!-- SCRIPT JS KONTROL MODAL & FITUR HAPUS -->
<script>
    // Toggle Mode Hapus / Checkbox Aksi
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

    // Modal Edit Buku
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

    // Menutup modal saat klik overlay luar
    window.onclick = function(event) {
        const addModal = document.getElementById('addModal');
        const editModal = document.getElementById('editModal');
        if (event.target === addModal) closeAddModal();
        if (event.target === editModal) closeEditModal();
    }
</script>
@endsection