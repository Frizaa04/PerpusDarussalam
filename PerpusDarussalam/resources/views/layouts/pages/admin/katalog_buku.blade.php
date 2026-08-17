@extends('layouts.pages.admin.provider.app')

@section('content')
    <div class="flex min-h-screen bg-[#f4f7f6]">

        <!-- Pemanggilan Sidebar -->
        @include('layouts.pages.admin.provider.sidebar')

        <main class="flex-1 flex flex-col">

            <!-- Area Konten -->
            <div class="p-8 space-y-6">

                @if (session('success'))
                    <div class="bg-emerald-600 text-white px-4 py-3 rounded shadow-md font-semibold text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-600 text-white px-4 py-3 rounded shadow-md font-semibold text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Pencarian & Tombol Buku Baru -->
                <div class="flex items-center gap-4">
                    <div class="max-w-md w-full">
                        <form action="{{ route('book.index') }}" method="GET"
                            class="flex items-center border-2 border-[#004d40] rounded overflow-hidden bg-white">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari Data Buku"
                                class="w-full px-4 py-2 text-gray-700 outline-none font-medium placeholder-gray-400">
                            <button type="submit"
                                class="bg-[#004d40] text-white px-4 py-2 flex items-center justify-center hover:bg-[#003d30] transition">
                                <span class="material-icons">search</span>
                            </button>
                        </form>
                    </div>

                    <!-- Tombol Buku Baru -->
                    <button type="button" onclick="openAddModal()"
                        class="border-2 border-[#004d40] text-[#004d40] font-bold px-4 py-2 rounded bg-white hover:bg-[#004d40] hover:text-white transition shadow-sm whitespace-nowrap">
                        + Buku Baru
                    </button>

                    <!-- Tombol Kelola Kategori -->
                    <button type="button" onclick="openAddCategoryModal()"
                        class="border-2 border-[#004d40] text-[#004d40] font-bold px-4 py-2 rounded bg-white hover:bg-[#004d40] hover:text-white transition shadow-sm whitespace-nowrap">
                        Kelola Kategori
                    </button>
                </div>



                <!-- Form/Wrapper Tabel untuk Aksi Hapus Massal -->
            <div id="deleteFormContainer">
                @csrf
                <!-- Box Tabel -->
                <div class="bg-[#a2b4ba] p-6 rounded shadow-[0_4px_12px_rgba(0,0,0,0.15)] border border-gray-300/30">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-white tracking-wide">Tabel Daftar Buku</h2>

                        <!-- Tombol Aksi di Header -->
                        <div class="flex items-center gap-2">
                            <!-- Pengecekan Select All -->
                            <div id="selectAllContainer" class="hidden flex items-center gap-1.5 px-2.5 py-1 select-none">
                                <input type="checkbox" id="selectAllCheckboxMain"
                                        onclick="toggleSelectAll(this, 'book-checkbox')"
                                        class="w-4 h-4 accent-red-600 cursor-pointer rounded">
                                <label for="selectAllCheckboxMain" class="text-xs font-semibold text-white cursor-pointer">Pilih Semua Isi Halaman</label>
                            </div>

                            <!-- Tombol Konfirmasi Hapus (Tipe diubah jadi button agar tidak submit form default) -->
                            <button type="button" id="btnConfirmDelete"
                                class="hidden bg-red-700 hover:bg-red-800 text-white font-bold px-3 py-1.5 rounded text-sm transition shadow-md">
                                Konfirmasi Hapus (<span id="jumlahTerpilih">0</span>)
                            </button>                      
                            
                            <!-- Tombol Mode Hapus / Batal -->
                            <button type="button" id="btnToggleDelete" onclick="toggleDeleteMode()"
                                class="bg-[#004d40] hover:bg-[#003d30] text-white font-bold px-3 py-1.5 rounded text-sm transition shadow flex items-center gap-1.5 select-none">
                                <svg id="trashIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span id="btnText">Hapus Buku</span>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded">
                        <table class="min-w-full text-left border-collapse border border-white/40">
                            <thead>
                                <tr class="bg-[#004d40] text-white divide-x divide-white/40">
                                    <th class="p-3 text-sm font-bold tracking-wider">Cover</th>
                                    <th class="p-3 text-sm font-bold tracking-wider">Judul</th>
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
                                            @if ($book->cover)
                                                <img src="{{ asset('storage/' . $book->cover) }}" alt="Cover" class="w-14 h-20 object-cover rounded border border-white/30 mx-auto shadow-md">
                                            @else
                                                <div class="w-14 h-20 bg-gray-400/50 text-[10px] text-white flex items-center justify-center rounded border border-white/30 mx-auto shadow-md">No Pic</div>
                                            @endif
                                        </td>
                                        <td class="p-3 text-sm font-bold text-white/90">{{ $book->judul }}</td>
                                        <td class="p-3 text-sm text-white/90">
                                            <div class="font-semibold">{{ $book->penulis }}</div>
                                            <div class="text-xs text-white/70 mt-0.5">ISBN: {{ $book->isbn }}</div>
                                        </td>
                                        <td class="p-3 text-sm text-white/90">{{ $book->categories->nama ?? ($book->kategori ?? '-') }}</td>
                                        <td class="p-3 text-sm font-bold text-white/90">{{ $book->rak ?? '-' }}</td>
                                        <td class="p-3 text-sm font-bold text-white/90">{{ $book->stok }}</td>

                                        <!-- KOLOM AKSI -->
                                        <td class="p-3 text-sm text-center">
                                            <!-- Mode Normal -->
                                            <div class="edit-mode-action flex items-center justify-center gap-2">
                                                <button type="button" onclick="openEditModal('{{ $book->id }}', '{{ $book->judul }}', '{{ $book->penulis }}', '{{ $book->penerbit }}', '{{ $book->deskripsi ?? '' }}', '{{ $book->isbn }}', '{{ $book->tanggal_pembelian }}', '{{ $book->categories_id }}', '{{ $book->stok }}', '{{ $book->rak ?? '' }}', '{{ $book->kode_buku }}', '{{ $book->tahun_terbit }}')" class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold tracking-wider hover:bg-[#003d30] transition shadow-sm">
                                                    Edit Data
                                                </button>
                                                <button type="button" onclick="openKelolaModal('{{ $book->id }}', '{{ $book->judul }}')" class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold tracking-wider hover:bg-[#003d30] transition shadow-sm">
                                                    Kelola data buku
                                                </button>
                                            </div>

                                            <!-- Mode Hapus (Checkbox) -->
                                            <div class="delete-mode-action hidden flex items-center justify-center gap-2">
                                                <input type="checkbox" value="{{ $book->id }}" class="book-checkbox w-5 h-5 accent-red-600 cursor-pointer rounded border-2 border-white">
                                                <span class="text-xs font-semibold text-red-200 italic">Centang untuk hapus</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-5 text-center text-sm font-semibold text-white/80">Data buku tidak ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $books->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>

    <!-- ====== POP-UP MODAL TAMBAH BUKU ====== -->
    <div id="addModal"
        class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
        <div
            class="bg-[#005a4e] text-white rounded-md shadow-2xl w-full max-w-xl p-6 relative border border-emerald-400/30">
            <!-- Tombol Close -->
            <button type="button" onclick="closeAddModal()"
                class="absolute top-3 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
                &#10005;
            </button>

            <h3 class="text-xl font-bold mb-5 tracking-wide">Tambah Buku Baru</h3>

            <!-- Kotak Error Tambah Buku -->
            <div
                class="mb-3 p-2 bg-red-600 text-white rounded text-xs {{ $errors->bookStoreForm->any() ? '' : 'hidden' }}">
                @if ($errors->bookStoreForm->any())
                    @foreach ($errors->bookStoreForm->all() as $error)
                        <div>- {{ $error }}</div>
                    @endforeach
                @endif
            </div>

            <form action="{{ route('book.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Cover</label>
                        <input type="file" name="cover"
                            class="w-full bg-[#b0bec5] text-gray-800 text-xs font-medium px-2 py-1.5 rounded outline-none file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-600 file:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">ISBN</label>
                        <input type="text" name="isbn" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">Judul</label>
                        <input type="text" name="judul" placeholder="..." required
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Tahun Pembelian</label>
                        <input type="date" name="tanggal_pembelian"
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">Tahun Terbit</label>
                        <input type="number" name="tahun_terbit" placeholder="..." 
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">Penulis</label>
                        <input type="text" name="penulis" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">Kategori</label>
                        <select name="categories_id" id="selectKategori" required
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                            <option value="">Pilih Kategori...</option>
                            @foreach ($allCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">Penerbit</label>
                        <input type="text" name="penerbit" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Stok</label>
                        <input type="number" name="stok" placeholder="..." required
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">Deskripsi</label>
                        <input type="text" name="deskripsi" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Rak</label>
                        <input type="text" name="rak" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>
                </div>

                <div class="pt-4 text-center">
                    <button type="submit"
                        class="bg-white text-[#004d40] hover:bg-emerald-50 px-8 py-2 rounded font-bold transition shadow-md">
                        Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ====== POP-UP MODAL EDIT BUKU ====== -->
    <div id="editModal"
        class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
        <div
            class="bg-[#005a4e] text-white rounded-md shadow-2xl w-full max-w-xl p-6 relative border border-emerald-400/30">
            <!-- Tombol Close -->
            <button type="button" onclick="closeEditModal()"
                class="absolute top-3 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
                &#10005;
            </button>

            <h3 class="text-xl font-bold mb-5 tracking-wide">Edit Data Buku</h3>

            <!-- Kotak Error Edit Buku -->
            <div
                class="mb-3 p-2 bg-red-600 text-white rounded text-xs {{ $errors->bookUpdateForm->any() ? '' : 'hidden' }}">
                @if ($errors->bookUpdateForm->any())
                    @foreach ($errors->bookUpdateForm->all() as $error)
                        <div>- {{ $error }}</div>
                    @endforeach
                @endif
            </div>

            <form id="editForm" action="{{ route('book.update') }}" method="POST" enctype="multipart/form-data"
                class="space-y-4">
                @csrf
                @method('PUT')

                <input type="hidden" id="editBookId" name="id">
                <input type="hidden" id="editKodeBuku" name="kode_buku">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Cover</label>
                        <input type="file" name="cover"
                            class="w-full bg-[#b0bec5] text-gray-800 text-xs font-medium px-2 py-1.5 rounded outline-none file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-600 file:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">ISBN</label>
                        <input type="text" id="editIsbn" name="isbn" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">Judul</label>
                        <input type="text" id="editJudul" name="judul" required
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Tahun Pembelian</label>
                        <input type="date" id="editTanggalPembelian" name="tanggal_pembelian"
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">Tahun Terbit</label>
                        <input type="number" id="editTahunTerbit" name="tahun_terbit" placeholder="2024" 
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">Penulis</label>
                        <input type="text" id="editPenulis" name="penulis" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Kategori</label>
                        <select id="editCategoriesId" name="categories_id"
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                            @foreach ($allCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">Penerbit</label>
                        <input type="text" id="editPenerbit" name="penerbit" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Stok</label>
                        <input type="number" id="editStok" name="stok" required
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">Deskripsi</label>
                        <input type="text" id="editDeskripsi" name="deskripsi" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Rak</label>
                        <input type="text" id="editRak" name="rak" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>
                </div>

                <div class="pt-4 text-center">
                    <button type="submit"
                        class="bg-white text-[#004d40] hover:bg-emerald-50 px-8 py-2 rounded font-bold transition shadow-md">
                        Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ====== POP-UP MODAL KELOLA KATEGORI ====== -->
    <div id="addCategoryModal"
        class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
        <div
            class="bg-[#005a4e] text-white rounded-md shadow-2xl w-full max-w-md p-6 relative border border-emerald-400/30 max-h-[85vh] overflow-y-auto">
            <!-- Tombol Close -->
            <button type="button" onclick="closeAddCategoryModal()" 
                class="absolute top-3 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
                &#10005;
            </button>

            <h3 class="text-xl font-bold mb-5 tracking-wide">Kelola Kategori</h3>

            <!-- Kotak Error Tambah Kategori -->
            <div
                class="mb-3 p-2 bg-red-600 text-white rounded text-xs {{ $errors->categoryStoreForm->any() ? '' : 'hidden' }}">
                @if ($errors->categoryStoreForm->any())
                    @foreach ($errors->categoryStoreForm->all() as $error)
                        <div>- {{ $error }}</div>
                    @endforeach
                @endif
            </div>

            <!-- Form Tambah Kategori -->
            <form action="{{ route('book.category.store') }}" method="POST" class="space-y-3 mb-6">
                @csrf
                <div>
                    <label class="block text-sm font-semibold mb-1">Tambah Kategori Baru</label>
                    <div class="flex gap-2">
                        <input type="text" name="nama_kategori" placeholder="Contoh: Ensiklopedia" required
                            value="{{ old('nama_kategori') }}"
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                        <button type="submit"
                            class="bg-white text-[#004d40] hover:bg-emerald-50 px-4 py-1.5 rounded text-sm font-bold transition shadow whitespace-nowrap">
                            Tambah
                        </button>
                    </div>
                </div>
            </form>

            <!-- Daftar Kategori yang Sudah Ada -->
            <div class="border-t border-white/20 pt-4">
                <label class="block text-sm font-semibold mb-2">Daftar Kategori</label>
                <div class="space-y-1.5">
                    @forelse ($allCategories as $cat)
                        <div class="flex items-center justify-between bg-[#004d40] px-3 py-2 rounded text-sm">
                            <span>{{ $cat->nama }}</span>
                            <form action="{{ route('book.category.destroy', $cat->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus kategori \'{{ $cat->nama }}\'?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-300 hover:text-red-100 text-xs font-bold underline">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-xs text-white/60 italic">Belum ada kategori.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- ====== POP-UP MODAL KELOLA BUKU ITEM (SATUAN/EKSEMPLAR) ====== -->
    <div id="kelolaModal"
        class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
        <div
            class="bg-[#005a4e] text-white rounded-md shadow-2xl w-full max-w-2xl p-6 relative border border-emerald-400/30 max-h-[90vh] overflow-y-auto">
            <!-- Tombol Close -->
            <button type="button" onclick="closeKelolaModal()"
                class="absolute top-3 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
                &#10005;
            </button>

            <h3 class="text-xl font-bold mb-1 tracking-wide">Kelola Data Buku Item</h3>
            <p id="kelolaBookTitle" class="text-xs text-emerald-200 mb-4 font-medium"></p>

            <!-- Form Tambah Item Baru untuk Buku Ini -->
            <form action="{{ route('book.item.store') }}" method="POST"
                class="bg-[#004d40] p-4 rounded-md mb-5 border border-white/25 space-y-3 shadow-inner">
                @csrf
                <input type="hidden" id="kelolaBookId" name="book_id">
                <h4 class="text-sm font-bold tracking-wide border-b border-white/20 pb-1">Tambah Eksemplar / Item Baru</h4>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1">Nomor Inventaris</label>
                        <input type="text" name="nomor_inventaris" placeholder="Contoh: INV-001" required
                            class="w-full bg-[#b0bec5] text-gray-800 text-xs font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">Kondisi</label>
                        <select name="kondisi"
                            class="w-full bg-[#b0bec5] text-gray-800 text-xs font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                            <option value="baik">Baik</option>
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="rusak_berat">Rusak Berat</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">Status Pinjam</label>
                        <select name="status_pinjam"
                            class="w-full bg-[#b0bec5] text-gray-800 text-xs font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                            <option value="tersedia">Tersedia</option>
                            <option value="dipinjam">Dipinjam</option>
                        </select>
                    </div>
                </div>
                <div class="text-right pt-1">
                    <button type="submit"
                        class="bg-white text-[#004d40] hover:bg-emerald-50 px-4 py-1.5 rounded text-xs font-bold transition shadow">
                        + Tambah Item
                    </button>
                </div>
            </form>

            <!-- Di dalam file view modal Anda, di atas tabel list item -->
            <div class="flex flex-wrap justify-between items-center mb-2 gap-2">
                <h4 class="text-sm font-bold tracking-wide">Pilih Eksemplar untuk Diedit / Dihapus</h4>

                <div class="flex items-center gap-2">
                    <!-- Tombol Cetak Terpilih (Baru) -->
                    <button type="button" onclick="printSelectedBarcodes()"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-1 rounded shadow transition">
                        🖨️ Cetak Barcode Terpilih (<span id="selectedCount">0</span>)
                    </button>

                    <!-- Tombol Cetak Semua Barcode Buku Ini -->
                    <a href="#" id="btnPrintAllBarcode" target="_blank"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3 py-1 rounded shadow transition hidden">
                        🖨️ Cetak Semua
                    </a>
                </div>
            </div>

            <!-- List / Daftar Item Eksemplar Buku -->
            <div class="overflow-x-auto rounded border border-white/30">
                <table class="min-w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#003d30] text-white border-b border-white/30">
                            <!-- Kolom Checkbox Pilih Semua -->
                            <th class="p-2.5 text-center w-10">
                                <input type="checkbox" id="selectAllHeaderTable"
                                    onclick="toggleSelectAll(this, 'item-checkbox')"
                                    class="cursor-pointer accent-red-600 w-4 h-4 rounded">
                            </th>
                            <th class="p-2.5">No. Inventaris</th>
                            <th class="p-2.5">Kondisi</th>
                            <th class="p-2.5">Status</th>
                            <th class="p-2.5 text-center">Aksi (Pilih Bagian)</th>
                        </tr>
                    </thead>
                    <tbody id="kelolaItemList" class="divide-y divide-white/20 bg-[#005a4e]">
                        <tr>
                            <td colspan="5" class="p-4 text-center text-white/70">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ====== SUB-MODAL EDIT ITEM SPESIFIK (MUNCUL KETIKA TOMBOL EDIT DI KLIK) ====== -->
    <div id="editItemModal" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-[60] p-4">
        <div class="bg-[#004d40] text-white rounded-md shadow-2xl w-full max-w-sm p-5 relative border border-white/30">
            <button type="button" onclick="closeEditItemModal()"
                class="absolute top-2.5 right-3 text-white hover:text-gray-300 text-lg font-bold">
                &#10005;
            </button>

            <h3 class="text-base font-bold mb-3 tracking-wide border-b border-white/20 pb-1">Edit Detail Eksemplar</h3>

            <form id="formEditItem" method="POST" class="space-y-3 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-semibold mb-1">Nomor Inventaris</label>
                    <input type="text" id="editNomorInventaris" name="nomor_inventaris" required readonly
                        class="w-full bg-[#b0bec5] text-gray-800 font-medium px-2.5 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Kondisi</label>
                    <select id="editKondisiItem" name="kondisi"
                        class="w-full bg-[#b0bec5] text-gray-800 font-medium px-2.5 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                        <option value="baik">Baik</option>
                        <option value="rusak_ringan">Rusak Ringan</option>
                        <option value="rusak_berat">Rusak Berat</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Status Pinjam</label>
                    <select id="editStatusPinjamItem" name="status_pinjam"
                        class="w-full bg-[#b0bec5] text-gray-800 font-medium px-2.5 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                        <option value="tersedia">Tersedia</option>
                        <option value="dipinjam">Dipinjam</option>
                    </select>
                </div>

                <div class="text-center pt-2">
                    <button type="submit"
                        class="bg-white text-[#004d40] hover:bg-emerald-50 px-5 py-1.5 rounded font-bold transition shadow">
                        Simpan Perubahan Item
                    </button>
                </div>
            </form>
        </div>
    </div>
<script>
    // Variable Global State
    let isDeleteModeActive = false;

    // 1. Event Listener Utama (DOMContentLoaded versi jQuery)
    $(document).ready(function() {

        // Auto open modal berdasarkan error/session server
        @if ($errors->bookStoreForm->any())
            openAddModal();
        @endif

        @if ($errors->bookUpdateForm->any())
            // Kode modal edit jika error bisa disesuaikan
        @endif

        @if ($errors->categoryStoreForm->any())
            openAddCategoryModal();
        @endif

        @if (session('error') && str_contains(session('error'), 'kategori'))
            openAddCategoryModal();
        @endif

        // Event Modal Close saat Klik Outside (Backdrop)
        $(window).on('click', function(e) {
            if ($(e.target).is('#addModal')) closeAddModal();
            if ($(e.target).is('#editModal')) closeEditModal();
            if ($(e.target).is('#addCategoryModal')) closeAddCategoryModal();
        });

        // ==========================================
        // FITUR: INISIALISASI MEMORI HAPUS BUKU LINTAS HALAMAN
        // ==========================================
        let selectedBookIds = JSON.parse(sessionStorage.getItem('selected_book_ids')) || [];

        // Jika sebelumnya user sedang dalam mode hapus di page lain, pertahankan modenya saat pindah page
        if (sessionStorage.getItem('delete_mode_active') === 'true') {
            setTimeout(function() {
                isDeleteModeActive = false; // reset state sebentar agar fungsi toggle memicu mode aktif
                toggleDeleteMode();
            }, 150);
        }

        // Otomatis centang ulang buku yang ID-nya ada di memori browser
        $('.book-checkbox').each(function() {
            if (selectedBookIds.includes($(this).val())) {
                $(this).prop('checked', true);
            }
        });

        // Pantau klik checkbox buku secara individual
        $(document).on('change', '.book-checkbox', function() {
            let id = $(this).val();
            if ($(this).is(':checked')) {
                if (!selectedBookIds.includes(id)) selectedBookIds.push(id);
            } else {
                selectedBookIds = selectedBookIds.filter(item => item !== id);
            }
            sessionStorage.setItem('selected_book_ids', JSON.stringify(selectedBookIds));
            updateConfirmDeleteButtonState();
        });

        // Event listener klik tombol Konfirmasi Hapus Buku Lintas Halaman
        $('#btnConfirmDelete').on('click', function(e) {
            e.preventDefault(); // Mencegah submit form bawaan browser
            
            let finalIds = JSON.parse(sessionStorage.getItem('selected_book_ids')) || [];
            
            if (finalIds.length === 0) {
                alert('Silakan pilih minimal satu buku untuk dihapus!');
                return;
            }

            if (confirm(`Yakin ingin menghapus ${finalIds.length} buku yang dipilih dari berbagai halaman?`)) {
                $.ajax({
                    url: "{{ route('book.destroyMultiple') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: finalIds
                    },
                    success: function(response) {
                        // Bersihkan memori setelah berhasil dihapus
                        sessionStorage.removeItem('selected_book_ids');
                        sessionStorage.removeItem('delete_mode_active');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Gagal menghapus data buku lintas halaman. Status: ' + xhr.status);
                    }
                });
            }
        });

        // Sinkronisasi tombol konfirmasi saat page baru dimuat
        updateConfirmDeleteButtonState();
    });

    // 2. Modal Functions (Tambah & Edit Utama)
    function openAddModal() { $('#addModal').removeClass('hidden'); }
    function closeAddModal() { $('#addModal').addClass('hidden'); }

    function openAddCategoryModal() { $('#addCategoryModal').removeClass('hidden'); }
    function closeAddCategoryModal() { $('#addCategoryModal').addClass('hidden'); }

    function openEditModal(id, judul, penulis, penerbit, deskripsi, isbn, tglPembelian, catId, stok, rak, kodeBuku, tahunTerbit) {
        $('#editBookId').val(id);
        $('#editJudul').val(judul);
        $('#editPenulis').val(penulis);
        $('#editPenerbit').val(penerbit);
        $('#editDeskripsi').val(deskripsi);
        $('#editIsbn').val(isbn);
        $('#editTanggalPembelian').val(tglPembelian);
        $('#editCategoriesId').val(catId);
        $('#editStok').val(stok);
        $('#editRak').val(rak);
        $('#editKodeBuku').val(kodeBuku);
        $('#editTahunTerbit').val(tahunTerbit);

        $('#editModal').removeClass('hidden');
    }

    function closeEditModal() { $('#editModal').addClass('hidden'); }

    // 3. Modal Kelola Eksemplar Buku & AJAX Load Data
    function openKelolaModal(bookId, bookTitle) {
        $('#kelolaBookId').val(bookId);
        $('#kelolaBookTitle').text("Judul Buku: " + bookTitle);

        // Set URL tombol cetak massal
        let $btnPrintAll = $('#btnPrintAllBarcode');
        $btnPrintAll.attr('href', `/book/${bookId}/print-all-barcodes`).removeClass('hidden');

        $('#kelolaModal').removeClass('hidden');

        let $tbody = $('#kelolaItemList');
        $tbody.html('<tr><td colspan="5" class="p-4 text-center text-white/70">Memuat data eksemplar...</td></tr>');

        // Fetch Data Eksemplar via jQuery AJAX
        $.ajax({
            url: `/book/${bookId}/items`,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $tbody.empty();

                if (data.length === 0) {
                    $tbody.html('<tr><td colspan="5" class="p-4 text-center text-white/70">Belum ada eksemplar terdaftar untuk buku ini.</td></tr>');
                    $btnPrintAll.addClass('hidden');
                    return;
                }

                let csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';

                $.each(data, function(index, item) {
                    let badgeColor = item.status_pinjam === 'tersedia' ? 'bg-emerald-600' : 'bg-amber-600';
                    let kondisiFormatted = item.kondisi.replace('_', ' ');
                    let printBarcodeUrl = `/book/item/${item.id}/print-barcode`;

                    let rowHtml = `
                    <tr class="hover:bg-white/10 transition-colors">
                        <td class="p-2.5 text-center">
                            <input type="checkbox" name="selected_items[]" value="${item.id}" onchange="updateSelectedCount()" class="item-checkbox cursor-pointer accent-blue-600 w-4 h-4 rounded">
                        </td>
                        <td class="p-2.5 font-bold">${item.nomor_inventaris}</td>
                        <td class="p-2.5 capitalize">${kondisiFormatted}</td>
                        <td class="p-2.5 uppercase"><span class="px-2 py-0.5 rounded text-[10px] font-bold ${badgeColor} text-white">${item.status_pinjam}</span></td>
                        <td class="p-2.5 text-center space-x-1">
                            <a href="${printBarcodeUrl}" target="_blank" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-2.5 py-1 rounded text-[10px] font-bold shadow inline-block">
                                Cetak Barcode
                            </a>
                            <button type="button" onclick="openEditItemModal(${item.id}, '${item.nomor_inventaris}', '${item.kondisi}', '${item.status_pinjam}')" 
                                class="bg-sky-600 hover:bg-sky-700 text-white px-2.5 py-1 rounded text-[10px] font-bold shadow">
                                Edit Bagian Ini
                            </button>
                            <form action="/book/item/${item.id}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus eksemplar ini?')">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-[10px] font-bold shadow">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>`;

                    $tbody.append(rowHtml);
                });

                updateSelectedCount();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                $tbody.html('<tr><td colspan="5" class="p-4 text-center text-red-300">Gagal memuat data eksemplar.</td></tr>');
            }
        });
    }

    function closeKelolaModal() { $('#kelolaModal').addClass('hidden'); }

    // Sub-modal Edit Item Spesifik
    function openEditItemModal(itemId, nomorInv, kondisi, statusPinjam) {
        $('#editNomorInventaris').val(nomorInv);
        $('#editKondisiItem').val(kondisi);
        $('#editStatusPinjamItem').val(statusPinjam);

        $('#formEditItem').attr('action', `/book/item/${itemId}`);
        $('#editItemModal').removeClass('hidden');
    }

    function closeEditItemModal() { $('#editItemModal').addClass('hidden'); }

    // 4. Mode Hapus Massal & Checkbox Actions
    function toggleDeleteMode() {
        isDeleteModeActive = !isDeleteModeActive;

        let $btnToggle = $('#btnToggleDelete');
        let $btnConfirm = $('#btnConfirmDelete');
        let $selectAllContainer = $('#selectAllContainer');
        let $btnText = $('#btnText');
        let $trashIcon = $('#trashIcon');

        if (isDeleteModeActive) {
            $btnToggle.removeClass('bg-[#004d40] hover:bg-[#003d30]').addClass('bg-gray-700 hover:bg-gray-800');
            $btnText.text('Batal');
            $trashIcon.html('<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />');

            $selectAllContainer.removeClass('hidden');
            sessionStorage.setItem('delete_mode_active', 'true');

            $('.edit-mode-action').addClass('hidden');
            $('.delete-mode-action').removeClass('hidden');
        } else {
            $btnToggle.removeClass('bg-gray-700 hover:bg-gray-800').addClass('bg-[#004d40] hover:bg-[#003d30]');
            $btnText.text('Hapus Buku');
            $trashIcon.html('<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />');

            $btnConfirm.addClass('hidden');
            $selectAllContainer.addClass('hidden');
            sessionStorage.setItem('delete_mode_active', 'false');

            $('.edit-mode-action').removeClass('hidden');
            $('.delete-mode-action').addClass('hidden');

            // Reset ingatan & centangan saat user membatalkan mode hapus
            sessionStorage.removeItem('selected_book_ids');
            $('#selectAllCheckboxMain').prop('checked', false);
            $('.book-checkbox').prop('checked', false);
        }
        updateConfirmDeleteButtonState();
    }

    function toggleSelectAll(master, targetClass = 'book-checkbox') {
        let checkboxes = $('.' + targetClass);
        checkboxes.prop('checked', master.checked);
        
        let selectedBookIds = JSON.parse(sessionStorage.getItem('selected_book_ids')) || [];

        checkboxes.each(function() {
            let id = $(this).val();
            if (master.checked) {
                if (!selectedBookIds.includes(id)) selectedBookIds.push(id);
            } else {
                selectedBookIds = selectedBookIds.filter(item => item !== id);
            }
        });

        sessionStorage.setItem('selected_book_ids', JSON.stringify(selectedBookIds));
        updateConfirmDeleteButtonState();
    }

    function updateConfirmDeleteButtonState() {
        let selectedBookIds = JSON.parse(sessionStorage.getItem('selected_book_ids')) || [];
        let totalCount = selectedBookIds.length;

        $('#jumlahTerpilih').text(totalCount);

        if (isDeleteModeActive && totalCount > 0) {
            $('#btnConfirmDelete').removeClass('hidden');
        } else {
            $('#btnConfirmDelete').addClass('hidden');
        }
    }

    // Fungsi penghitung checkbox sub-modal eksemplar buku
    function updateSelectedCount() {
        let $checkboxes = $('.item-checkbox');
        let checkedCount = $('.item-checkbox:checked').length;

        $('#selectedCount').text(checkedCount);

        let $master = $('#selectAllCheckbox');
        if ($master.length && $checkboxes.length > 0) {
            $master.prop('checked', checkedCount === $checkboxes.length);
        }
    }

    // 5. Cetak Barcode Terpilih
    function printSelectedBarcodes() {
        let $selected = $('.item-checkbox:checked');

        if ($selected.length === 0) {
            alert('Silakan pilih minimal satu eksemplar buku yang ingin dicetak barcodenya!');
            return;
        }

        let ids = $selected.map(function() { return $(this).val(); }).get();
        let baseUrl = "{{ route('book.item.print.selected.barcodes') }}";
        let queryString = $.param({ 'ids': ids });

        window.open(`${baseUrl}?${queryString}`, '_blank');
    }
</script>
@endsection