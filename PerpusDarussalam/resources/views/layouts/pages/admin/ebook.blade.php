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
                    <form action="{{ route('ebook.index') }}" method="GET" class="flex items-center border-2 border-[#004d40] rounded overflow-hidden bg-white">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari Data E-Book..." class="w-full px-4 py-2 text-gray-700 outline-none font-medium placeholder-gray-400 text-sm">
                        <button type="submit" class="bg-[#004d40] text-white px-4 py-2 flex items-center justify-center hover:bg-[#003d30] transition">
                            <span class="material-icons">search</span>
                        </button>
                    </form>
                </div>

                <!-- Tombol E-Book Baru -->
                <button type="button" onclick="openAddEbookModal()" class="border-2 border-[#004d40] text-[#004d40] font-bold px-4 py-2 rounded bg-white hover:bg-[#004d40] hover:text-white transition shadow-sm text-sm whitespace-nowrap">
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
                                    <button onclick="openEditEbookModal('{{ $ebook['id'] }}', '{{ $ebook['judul'] }}', '{{ $ebook['categories_id'] }}', '{{ $ebook['tahun_terbit'] }}')" class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-[#003d30] transition">
                                        Edit Data
                                    </button>
                                    <button onclick="window.open('{{ asset('storage/' . $ebook->file_pdf) }}', '_blank')" class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-[#003d30] transition">
                                        Baca PDF
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- ================= MODAL TAMBAH E-BOOK ================= -->
<div id="addEbookModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center hidden">
    <div class="bg-[#004d40] rounded-xl w-full max-w-xl p-6 shadow-2xl relative text-white">
        <button onclick="closeAddEbookModal()" class="absolute top-4 right-4 text-white hover:text-gray-200 text-xl font-bold">&times;</button>
        
        <h3 class="text-xl font-bold mb-6">Tambah E-Book Baru</h3>
        
        <form action="{{ route('ebook.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-1">Judul E-Book</label>
                    <input type="text" name="judul" class="w-full px-3 py-2 bg-gray-300 text-gray-800 rounded text-sm focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-1">Kategori</label>
                    <select name="categories_id" class="w-full px-3 py-2 bg-gray-300 text-gray-800 rounded text-sm focus:outline-none" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-1">Tahun Terbit</label>
                    <input type="number" name="tahun" class="w-full px-3 py-2 bg-gray-300 text-gray-800 rounded text-sm focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-1">File PDF E-Book</label>
                    <input type="file" name="file_pdf" accept=".pdf" class="w-full text-xs text-white">
                </div>
            </div>

            <div class="mt-8 flex justify-center">
                <button type="submit" class="bg-white text-[#004d40] px-8 py-2 rounded font-bold text-sm hover:bg-gray-100 transition shadow">
                    Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT E-BOOK ================= -->
<div id="editEbookModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center hidden">
    <div class="bg-[#004d40] rounded-xl w-full max-w-xl p-6 shadow-2xl relative text-white">
        <button onclick="closeEditEbookModal()" class="absolute top-4 right-4 text-white hover:text-gray-200 text-xl font-bold">&times;</button>
        
        <h3 class="text-xl font-bold mb-6">Edit Data E-Book</h3>
        
        <form id="formEditEbook" action="" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-1">Judul E-Book</label>
                    <input type="text" id="editEbookJudul" name="judul" class="w-full px-3 py-2 bg-gray-300 text-gray-800 rounded text-sm focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-1">Kategori</label>
                    <select id="editEbookKategori" name="categories_id" class="w-full px-3 py-2 bg-gray-300 text-gray-800 rounded text-sm focus:outline-none" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-1">Tahun Terbit</label>
                    <input type="number" id="editEbookTahun" name="tahun" class="w-full px-3 py-2 bg-gray-300 text-gray-800 rounded text-sm focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-1">Ganti File PDF (Opsional)</label>
                    <input type="file" name="file_pdf" accept=".pdf" class="w-full text-xs text-white">
                </div>
            </div>

            <div class="mt-8 flex justify-center">
                <button type="submit" class="bg-white text-[#004d40] px-8 py-2 rounded font-bold text-sm hover:bg-gray-100 transition shadow">
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
</script>
@endsection