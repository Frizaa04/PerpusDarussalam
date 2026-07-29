@extends('layouts.app')

@section('content')
    <div class="flex min-h-screen bg-[#f4f7f6]">

        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <main class="flex-1 flex flex-col">

            <!-- Body Area -->
            <div class="p-8 space-y-6">

                <!-- Pencarian & Tombol E-Book Baru -->
                <div class="flex items-center gap-4 mb-6">
                    <div class="max-w-md w-full">
                        <form action="{{ route('ebook.index') }}" method="GET"
                            class="flex items-center border-2 border-[#004d40] rounded overflow-hidden bg-white">
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari Data E-Book..."
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

                <!-- Form Pembungkus Penghapusan Massal untuk Tabel Bawah -->
                <form id="deleteForm" action="{{ route('ebook.destroy-multiple') }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <!-- Tabel Daftar E-Book (Tabel Bawah yang Dipertahankan & Dilengkapi Tombol Hapus) -->
                    <div class="bg-[#b0bec5] rounded-lg shadow-md overflow-hidden p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-white text-xl font-bold tracking-wide">Tabel Daftar E-Book</h2>

                            <!-- Bagian Tombol Aksi (Pilih Semua, Konfirmasi, & Toggle Mode Hapus) -->
                            <div class="flex items-center gap-2">
                                <!-- Checkbox Pilih Semua (Awalnya Tersembunyi) -->
                                <div id="selectAllContainer"
                                    class="hidden flex items-center gap-1.5 px-2.5 py-1 select-none">
                                    <input type="checkbox" id="selectAllCheckbox" onclick="toggleSelectAll(this)"
                                        class="w-4 h-4 accent-red-600 cursor-pointer rounded">
                                    <label for="selectAllCheckbox"
                                        class="text-xs font-semibold text-white cursor-pointer">Pilih Semua</label>
                                </div>

                                <!-- Tombol Konfirmasi Hapus (Awalnya Tersembunyi) -->
                                <button type="submit" id="btnConfirmDelete"
                                    class="hidden bg-red-700 hover:bg-red-800 text-white font-bold px-3 py-1.5 rounded text-sm transition shadow-md">
                                    Konfirmasi Hapus
                                </button>

                                <!-- Tombol Toggle Mode Hapus -->
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
                                    @forelse($ebooks as $ebook)
                                        <tr class="hover:bg-gray-400/20 transition">
                                            <td class="p-3">
                                                <div
                                                    class="w-12 h-16 bg-gray-300 border border-gray-400 rounded flex items-center justify-center text-xs text-gray-600 font-bold">
                                                    PDF
                                                </div>
                                            </td>
                                            <td class="p-3 font-semibold text-gray-900">{{ $ebook->judul }}</td>
                                            <td class="p-3">{{ $ebook->category->nama ?? ($ebook->kategori ?? '-') }}
                                            </td>
                                            <td class="p-3">{{ $ebook->tahun_terbit }}</td>

                                            <td class="p-3 text-center">
                                                <!-- Mode Normal: Tombol Edit & Baca PDF -->
                                                <div class="edit-mode-action flex items-center justify-center gap-2">
                                                    <button type="button"
                                                        onclick="openEditEbookModal('{{ $ebook->id }}', '{{ $ebook->judul }}', '{{ $ebook->categories_id }}', '{{ $ebook->tahun_terbit }}')"
                                                        class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-[#003d30] transition shadow-sm">
                                                        Edit Data
                                                    </button>
                                                    <button type="button"
                                                        onclick="window.open('{{ asset('storage/' . $ebook->file_pdf) }}', '_blank')"
                                                        class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-[#003d30] transition shadow-sm">
                                                        Baca PDF
                                                    </button>
                                                </div>

                                                <!-- Mode Hapus: Checkbox Hapus Massal -->
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
                                            <td colspan="5" class="p-5 text-center text-sm font-semibold text-gray-800">
                                                Belum ada data e-book.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>

            </div>
        </main>
    </div>

    <!-- ================= MODAL TAMBAH E-BOOK ================= -->
    <div id="addEbookModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center hidden">
        <div class="bg-[#004d40] rounded-xl w-full max-w-xl p-6 shadow-2xl relative text-white">
            <button onclick="closeAddEbookModal()"
                class="absolute top-4 right-4 text-white hover:text-gray-200 text-xl font-bold">&times;</button>

            <h3 class="text-xl font-bold mb-6">Tambah E-Book Baru</h3>

            <form action="{{ route('ebook.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-1">Judul E-Book</label>
                        <input type="text" name="judul"
                            class="w-full px-3 py-2 bg-gray-300 text-gray-800 rounded text-sm focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-1">Kategori</label>
                        <select name="categories_id"
                            class="w-full px-3 py-2 bg-gray-300 text-gray-800 rounded text-sm focus:outline-none" required>
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-1">Tahun Terbit</label>
                        <input type="number" name="tahun"
                            class="w-full px-3 py-2 bg-gray-300 text-gray-800 rounded text-sm focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-1">File PDF E-Book</label>
                        <input type="file" name="file_pdf" accept=".pdf" class="w-full text-xs text-white">
                    </div>
                </div>

                <div class="mt-8 flex justify-center">
                    <button type="submit"
                        class="bg-white text-[#004d40] px-8 py-2 rounded font-bold text-sm hover:bg-gray-100 transition shadow">
                        Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL EDIT E-BOOK ================= -->
    <div id="editEbookModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center hidden">
        <div class="bg-[#004d40] rounded-xl w-full max-w-xl p-6 shadow-2xl relative text-white">
            <button onclick="closeEditEbookModal()"
                class="absolute top-4 right-4 text-white hover:text-gray-200 text-xl font-bold">&times;</button>

            <h3 class="text-xl font-bold mb-6">Edit Data E-Book</h3>

            <form id="formEditEbook" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-1">Judul E-Book</label>
                        <input type="text" id="editEbookJudul" name="judul"
                            class="w-full px-3 py-2 bg-gray-300 text-gray-800 rounded text-sm focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-1">Kategori</label>
                        <select id="editEbookKategori" name="categories_id"
                            class="w-full px-3 py-2 bg-gray-300 text-gray-800 rounded text-sm focus:outline-none" required>
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-1">Tahun Terbit</label>
                        <input type="number" id="editEbookTahun" name="tahun"
                            class="w-full px-3 py-2 bg-gray-300 text-gray-800 rounded text-sm focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-1">Ganti File PDF
                            (Opsional)</label>
                        <input type="file" name="file_pdf" accept=".pdf" class="w-full text-xs text-white">
                    </div>
                </div>

                <div class="mt-8 flex justify-center">
                    <button type="submit"
                        class="bg-white text-[#004d40] px-8 py-2 rounded font-bold text-sm hover:bg-gray-100 transition shadow">
                        Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Fungsi untuk membuka modal Tambah E-Book
        function openAddEbookModal() {
            document.getElementById('addEbookModal').classList.remove('hidden');
        }

        // Fungsi untuk menutup modal Tambah E-Book
        function closeAddEbookModal() {
            document.getElementById('addEbookModal').classList.add('hidden');
        }

        function openEditEbookModal(id, judul, categoriesId, tahun) {
            document.getElementById('editEbookJudul').value = judul;
            document.getElementById('editEbookKategori').value = categoriesId;
            document.getElementById('editEbookTahun').value = tahun;

            // Mengatur action route update dinamis berdasarkan ID
            document.getElementById('formEditEbook').action = '/e-book/' + id;

            document.getElementById('editEbookModal').classList.remove('hidden');
        }

        function closeEditEbookModal() {
            document.getElementById('editEbookModal').classList.add('hidden');
        }

        function bacaPdf(url) {
            window.open(url, '_blank');
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
            
            // Ubah ikon tombol jadi silang
            btnIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />`;

            btnConfirm.classList.remove('hidden');
            selectAllContainer.classList.remove('hidden');

            editActions.forEach(el => el.classList.add('hidden'));
            deleteActions.forEach(el => el.classList.remove('hidden'));
        } else {
            btnToggle.classList.remove('bg-gray-600', 'hover:bg-gray-700');
            btnToggle.classList.add('bg-[#004d40]', 'hover:bg-[#003d30]');
            btnText.textContent = "Hapus E-Book";
            
            // Kembalikan ikon tempat sampah
            btnIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />`;

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
