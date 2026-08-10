@extends('layouts.pages.admin.provider.app')

@section('content')
    <div class="flex min-h-screen bg-[#f4f7f6]">

        <!-- Sidebar -->
        @include('layouts.pages.admin.provider.sidebar')

        <!-- Main Content -->
        <main class="flex-1 flex flex-col">

            <!-- Body Area -->
            <div class="p-8 space-y-6">

                <!-- NOTIFIKASI SUCCESS / ERROR -->
                @if (session('success'))
                    <div class="bg-emerald-600 text-white p-4 rounded-lg shadow font-bold text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-600 text-white p-4 rounded-lg shadow font-bold text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-500 text-white p-4 rounded-lg shadow text-sm">
                        <p class="font-bold mb-1">Gagal Menyimpan Data:</p>
                        <ul class="list-disc list-inside space-y-0.5 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Pencarian & Tombol E-Book Baru -->
                <div class="flex items-center gap-4 mb-6">
                    <div class="max-w-md w-full">
                        <form action="{{ route('admin.ebook.index') }}" method="GET"
                            class="flex items-center border-2 border-[#004d40] rounded overflow-hidden bg-white">
                            
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari Data E-Book..."
                                class="w-full px-4 py-2 text-gray-700 outline-none font-medium placeholder-gray-400 text-sm">
                                
                            <button type="submit"
                                class="bg-[#004d40] text-white px-4 py-2 flex items-center justify-center hover:bg-[#003d30] transition">
                                <span class="material-icons">search</span>
                            </button>
                        </form>
                    </div>

                    <!-- Tombol E-Book Baru -->
                    <button type="button" onclick="openAddEbookModal()"
                        class="border-2 border-[#004d40] text-[#004d40] font-bold px-4 py-2 rounded bg-white hover:bg-[#004d40] hover:text-white transition shadow-sm text-sm whitespace-nowrap">
                        + E-Book Baru
                    </button>
                </div>

                <!-- Form Pembungkus Penghapusan Massal -->
                <form id="deleteForm" action="{{ route('admin.ebook.destroy-multiple') }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <!-- Tabel Daftar E-Book -->
                    <div class="bg-[#b0bec5] rounded-lg shadow-md overflow-hidden p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-white text-xl font-bold tracking-wide">Tabel Daftar E-Book</h2>

                            <!-- Bagian Tombol Aksi -->
                            <div class="flex items-center gap-2">
                                <div id="selectAllContainer"
                                    class="hidden flex items-center gap-1.5 px-2.5 py-1 select-none">
                                    <input type="checkbox" id="selectAllCheckbox" onclick="toggleSelectAll(this)"
                                        class="w-4 h-4 accent-red-600 cursor-pointer rounded">
                                    <label for="selectAllCheckbox"
                                        class="text-xs font-semibold text-white cursor-pointer">Pilih Semua</label>
                                </div>

                                <button type="submit" id="btnConfirmDelete"
                                    class="hidden bg-red-700 hover:bg-red-800 text-white font-bold px-3 py-1.5 rounded text-sm transition shadow-md">
                                    Konfirmasi Hapus
                                </button>

                                <button type="button" id="btnToggleDelete" onclick="toggleDeleteMode()"
                                    class="bg-[#004d40] hover:bg-[#003d30] text-white font-bold px-3 py-1.5 rounded text-sm transition shadow flex items-center gap-1.5 select-none">
                                    <svg id="btnIcon" xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 text-white transition-colors duration-200" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span id="btnText">Hapus E-Book</span>
                                </button>
                            </div>
                        </div>

                        <!-- Grid Tabel -->
                        <div class="overflow-x-auto rounded">
                            <table class="min-w-full text-left border-collapse border border-white/40">
                                <thead>
                                    <tr class="bg-[#004d40] text-white divide-x divide-white/40">
                                        <th class="p-3 text-xs font-bold uppercase tracking-wider text-center">Cover</th>
                                        <th class="p-3 text-xs font-bold uppercase tracking-wider">Kode E-Book</th>
                                        <th class="p-3 text-xs font-bold uppercase tracking-wider">Judul E-Book</th>
                                        <th class="p-3 text-xs font-bold uppercase tracking-wider">Kategori</th>
                                        <th class="p-3 text-xs font-bold uppercase tracking-wider">Penulis</th>
                                        <th class="p-3 text-xs font-bold uppercase tracking-wider">Penerbit</th>
                                        <th class="p-3 text-xs font-bold uppercase tracking-wider">Tahun Terbit</th>
                                        <th class="p-3 text-xs font-bold uppercase tracking-wider">ISBN</th>
                                        <th class="p-3 text-xs font-bold uppercase tracking-wider text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-white">
                                    @forelse($ebooks as $ebook)
                                        <tr class="divide-x divide-white/40 hover:bg-white/10 transition-colors">
                                            <td class="p-3 text-sm text-center">
                                                @if ($ebook->cover)
                                                    <img src="{{ asset('storage/' . $ebook->cover) }}"
                                                        class="w-10 h-14 object-cover rounded shadow mx-auto">
                                                @else
                                                    <div
                                                        class="w-10 h-14 bg-gray-300 border border-white/40 rounded flex items-center justify-center text-[10px] text-gray-700 font-bold mx-auto">
                                                        PDF
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="p-3 text-sm font-mono font-bold">{{ $ebook->kode_ebook }}</td>
                                            <td class="p-3 text-sm font-bold">{{ $ebook->judul }}</td>
                                            <td class="p-3 text-sm font-medium">{{ $ebook->category->nama ?? '-' }}</td>
                                            <td class="p-3 text-sm font-medium">{{ $ebook->penulis }}</td>
                                            <td class="p-3 text-sm font-medium">{{ $ebook->penerbit }}</td>
                                            <td class="p-3 text-sm font-medium">{{ $ebook->tahun_terbit }}</td>
                                            <td class="p-3 text-sm font-mono">{{ $ebook->isbn ?? '-' }}</td>
                                            <td class="p-3 text-sm text-center">
                                                <div class="edit-mode-action flex items-center justify-center gap-2">
                                                    <button type="button"
                                                        onclick="openEditEbookModal('{{ $ebook->id }}', '{{ $ebook->kode_ebook }}', '{{ addslashes($ebook->judul) }}', '{{ $ebook->categories_id }}', '{{ addslashes($ebook->penulis) }}', '{{ addslashes($ebook->penerbit) }}', '{{ $ebook->tahun_terbit }}', '{{ $ebook->isbn }}')"
                                                        class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold uppercase tracking-wider hover:bg-[#003d30] transition shadow-sm">
                                                        Edit Data
                                                    </button>
                                                    <button type="button"
                                                        onclick="window.open('{{ asset('storage/' . $ebook->file_pdf) }}', '_blank')"
                                                        class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold uppercase tracking-wider hover:bg-[#003d30] transition shadow-sm">
                                                        Baca PDF
                                                    </button>
                                                </div>

                                                <div
                                                    class="delete-mode-action hidden flex items-center justify-center gap-2">
                                                    <input type="checkbox" name="ids[]" value="{{ $ebook->id }}"
                                                        class="ebook-checkbox w-5 h-5 accent-red-600 cursor-pointer rounded border-2 border-white">
                                                    <span class="text-xs font-semibold text-red-900 italic">Centang untuk
                                                        hapus</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="p-5 text-center text-sm font-semibold text-gray-800">
                                                Belum ada data e-book.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $ebooks->links('vendor.pagination.custom') }}
                        </div>

                    </div>
                </form>

            </div>
        </main>
    </div>

    <!-- ================= MODAL TAMBAH E-BOOK ================= -->
    <div id="addEbookModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center hidden">
        <div class="bg-[#004d40] rounded-xl w-full max-w-3xl p-8 shadow-2xl relative text-white">
            <button onclick="closeAddEbookModal()"
                class="absolute top-4 right-4 text-white hover:text-gray-200 text-2xl font-bold">&times;</button>

            <h3 class="text-2xl font-bold mb-6">Tambah E-Book Baru</h3>

            <!-- Kotak Pesan Error untuk Tambah E-Book -->
            <div
                class="mb-3 p-2 bg-red-600 text-white rounded text-xs {{ $errors->ebookStoreForm->any() ? '' : 'hidden' }}">
                @if ($errors->ebookStoreForm->any())
                    @foreach ($errors->ebookStoreForm->all() as $error)
                        <div>- {{ $error }}</div>
                    @endforeach
                @endif
            </div>

            <form action="{{ route('admin.ebook.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">KODE E-BOOK</label>
                        <input type="text" name="kode_ebook" value="{{ old('kode_ebook') }}"
                            placeholder="Contoh: EB-1001"
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white placeholder-gray-500"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">JUDUL E-BOOK</label>
                        <input type="text" name="judul" value="{{ old('judul') }}" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">KATEGORI</label>
                        <select name="categories_id"
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white"
                            required>
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('categories_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">PENULIS</label>
                        <input type="text" name="penulis" value="{{ old('penulis') }}" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">PENERBIT</label>
                        <input type="text" name="penerbit" value="{{ old('penerbit') }}" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">TAHUN TERBIT</label>
                        <input type="number" name="tahun_terbit" value="{{ old('tahun_terbit') }}" placeholder="2024"
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white placeholder-gray-500"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">ISBN (OPSIONAL)</label>
                        <input type="text" name="isbn" value="{{ old('isbn') }}" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">COVER E-BOOK (OPSIONAL)</label>
                        <input type="file" name="cover" accept="image/*"
                            class="w-full bg-[#b0bec5] text-gray-800 text-xs font-medium px-2 py-1.5 rounded outline-none file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-600 file:text-white">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold mb-1">FILE PDF E-BOOK</label>
                        <input type="file" name="file_pdf" accept=".pdf"
                            class="w-full bg-[#b0bec5] text-gray-800 text-xs font-medium px-2 py-1.5 rounded outline-none file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-600 file:text-white"
                            required>
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

    <!-- ================= MODAL EDIT E-BOOK ================= -->
    <div id="editEbookModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center hidden">
        <div class="bg-[#004d40] rounded-xl w-full max-w-3xl p-8 shadow-2xl relative text-white">
            <button onclick="closeEditEbookModal()"
                class="absolute top-4 right-4 text-white hover:text-gray-200 text-2xl font-bold">&times;</button>

            <h3 class="text-2xl font-bold mb-6">Edit Data E-Book</h3>

            <!-- Kotak Pesan Error untuk Edit E-Book -->
            <div
                class="mb-3 p-2 bg-red-600 text-white rounded text-xs {{ $errors->ebookUpdateForm->any() ? '' : 'hidden' }}">
                @if ($errors->ebookUpdateForm->any())
                    @foreach ($errors->ebookUpdateForm->all() as $error)
                        <div>- {{ $error }}</div>
                    @endforeach
                @endif
            </div>

            <form id="formEditEbook" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">KODE E-BOOK</label>
                        <input type="text" id="editEbookKode" name="kode_ebook" placeholder="Contoh: EB-1001"
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white placeholder-gray-500"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">JUDUL E-BOOK</label>
                        <input type="text" id="editEbookJudul" name="judul" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">KATEGORI</label>
                        <select id="editEbookKategori" name="categories_id"
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white"
                            required>
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">PENULIS</label>
                        <input type="text" id="editEbookPenulis" name="penulis" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">PENERBIT</label>
                        <input type="text" id="editEbookPenerbit" name="penerbit" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">TAHUN TERBIT</label>
                        <input type="number" id="editEbookTahun" name="tahun_terbit" placeholder="2024"
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white placeholder-gray-500"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">ISBN (OPSIONAL)</label>
                        <input type="text" id="editEbookIsbn" name="isbn" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">GANTI COVER (OPSIONAL)</label>
                        <input type="file" name="cover" accept="image/*"
                            class="w-full bg-[#b0bec5] text-gray-800 text-xs font-medium px-2 py-1.5 rounded outline-none file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-600 file:text-white">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold mb-1">GANTI FILE PDF (OPSIONAL)</label>
                        <input type="file" name="file_pdf" accept=".pdf"
                            class="w-full bg-[#b0bec5] text-gray-800 text-xs font-medium px-2 py-1.5 rounded outline-none file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-600 file:text-white">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- OTOMATIS BUKA MODAL TAMBAH EBOOK JIKA ADA ERROR DARI SERVER ---
            @if ($errors->ebookStoreForm->any())
                openAddEbookModal();
            @endif
        });

        function openAddEbookModal() {
            document.getElementById('addEbookModal').classList.remove('hidden');
        }

        function closeAddEbookModal() {
            document.getElementById('addEbookModal').classList.add('hidden');
        }

        function openEditEbookModal(id, kodeEbook, judul, categoriesId, penulis, penerbit, tahunTerbit, isbn) {
            document.getElementById('editEbookKode').value = kodeEbook;
            document.getElementById('editEbookJudul').value = judul;
            document.getElementById('editEbookKategori').value = categoriesId;
            document.getElementById('editEbookPenulis').value = penulis;
            document.getElementById('editEbookPenerbit').value = penerbit;
            document.getElementById('editEbookTahun').value = tahunTerbit;
            document.getElementById('editEbookIsbn').value = (isbn !== 'null' && isbn !== 'undefined') ? isbn : '';

            // Disesuaikan agar cocok dengan Route::put('/e-book/update/{id}')
            document.getElementById('formEditEbook').action = '/e-book/update/' + id;
            document.getElementById('editEbookModal').classList.remove('hidden');
        }

        function closeEditEbookModal() {
            document.getElementById('editEbookModal').classList.add('hidden');
        }

        let isDeleteModeActive = false;

        function toggleDeleteMode() {
            isDeleteModeActive = !isDeleteModeActive;

            let btnToggle = document.getElementById('btnToggleDelete');
            let btnConfirm = document.getElementById('btnConfirmDelete');
            let selectAllContainer = document.getElementById('selectAllContainer');
            let btnText = document.getElementById('btnText');
            let btnIcon = document.getElementById('btnIcon');
            let editActions = document.querySelectorAll('.edit-mode-action');
            let deleteActions = document.querySelectorAll('.delete-mode-action');

            if (isDeleteModeActive) {
                btnToggle.classList.remove('bg-[#004d40]', 'hover:bg-[#003d30]');
                btnToggle.classList.add('bg-gray-600', 'hover:bg-gray-700');
                btnText.textContent = "Batal";
                btnIcon.innerHTML =
                    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />`;

                btnConfirm.classList.remove('hidden');
                selectAllContainer.classList.remove('hidden');

                editActions.forEach(el => el.classList.add('hidden'));
                deleteActions.forEach(el => el.classList.remove('hidden'));
            } else {
                btnToggle.classList.remove('bg-gray-600', 'hover:bg-gray-700');
                btnToggle.classList.add('bg-[#004d40]', 'hover:bg-[#003d30]');
                btnText.textContent = "Hapus E-Book";
                btnIcon.innerHTML =
                    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />`;

                btnConfirm.classList.add('hidden');
                selectAllContainer.classList.add('hidden');

                editActions.forEach(el => el.classList.remove('hidden'));
                deleteActions.forEach(el => el.classList.add('hidden'));

                document.getElementById('selectAllCheckbox').checked = false;
                document.querySelectorAll('.ebook-checkbox').forEach(cb => cb.checked = false);
            }
        }

        function toggleSelectAll(source) {
            let checkboxes = document.querySelectorAll('.ebook-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = source.checked;
            });
        }

        document.getElementById('deleteForm').addEventListener('submit', function(e) {
            let checkedBoxes = document.querySelectorAll('.ebook-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Pilih minimal satu e-book yang ingin dihapus!');
                e.preventDefault();
            } else {
                if (!confirm('Yakin ingin menghapus e-book yang dipilih?')) {
                    e.preventDefault();
                }
            }
        });
    </script>
@endsection
