@extends('layouts.pages.admin.provider.app')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Pemanggilan Sidebar -->
    @include('layouts.pages.admin.provider.sidebar')

    <main class="flex-1 flex flex-col">
        <!-- Area Sirkulasi -->
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
                    
                    <!-- Filter Peminjaman Telat (Button dengan Checkbox Terlihat) -->
                    <form id="lateFilterForm" action="{{ route('circulation.index') }}" method="GET" class="flex items-center">
                        @if($search)
                            <input type="hidden" name="search" value="{{ $search }}">
                        @endif
                        
                        <label for="lateCheckbox" class="cursor-pointer select-none flex items-center gap-2.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm border {{ request('late') ? 'bg-red-700 text-white border-red-800 shadow-md' : 'bg-[#004d40] hover:bg-[#003d30] text-white border-[#003d30]' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Peminjaman Telat</span>
                            <input type="checkbox" id="lateCheckbox" name="late" value="1" onchange="document.getElementById('lateFilterForm').submit()" {{ request('late') ? 'checked' : '' }} class="w-4 h-4 accent-red-600 rounded cursor-pointer">
                        </label>
                    </form>
                </div>
                
                <div class="overflow-x-auto rounded">
                    <table class="min-w-full text-left border-collapse border border-white/40">
                        <thead>
                            <tr class="bg-[#004d40] text-white divide-x divide-white/40">
                                <th class="p-3 text-sm font-bold tracking-wider w-12 text-center">No</th>
                                <th class="p-3 text-sm font-bold tracking-wider">No Identitas</th>
<<<<<<< Updated upstream
                                <th class="p-3 text-sm font-bold tracking-wider">Nama Peminjam</th>
=======
                                <th class="p-3 text-sm font-bold tracking-wider">Nama Peminjam</th> 
>>>>>>> Stashed changes
                                <th class="p-3 text-sm font-bold tracking-wider">Judul Buku</th>
                                <th class="p-3 text-sm font-bold tracking-wider">No. Inventaris</th> <!-- Kolom Baru -->
                                <th class="p-3 text-sm font-bold tracking-wider">Status</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Tanggal Pinjam</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Tanggal Jatuh Tempo</th> 
                                <th class="p-3 text-sm font-bold tracking-wider">Tanggal Kembali</th>
                                <th class="p-3 text-sm font-bold tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-white divide-y divide-white/40">
                        @forelse($circulations as $item)
                            <tr class="divide-x divide-white/40 hover:bg-white/10 transition-colors">
                                <!-- Penomoran Dinamis Mengikut Halaman Paginasi -->
                                <td class="p-3 text-sm font-bold text-center text-white/90">
                                    {{ ($circulations->currentPage() - 1) * $circulations->perPage() + $loop->iteration }}
                                </td>
                                <td class="p-3 text-sm font-bold text-white/90">{{ $item->identitas }}</td>
<<<<<<< Updated upstream
                                <td class="p-3 text-sm text-white/90">{{ $item->name }}</td>
=======
                                <td class="p-3 text-sm text-white/90">{{ $item->name }}</td> 
>>>>>>> Stashed changes
                                <td class="p-3 text-sm text-white/90">{{ $item->book_title }}</td>
                                <td class="p-3 text-sm font-mono text-white/90">{{ $item->nomor_inv }}</td> <!-- Data No. Inventaris Baru -->
                                <td class="p-3 text-sm font-bold {{ $item->status == 'Telat' ? 'text-red-600' : 'text-white/90' }}">
                                    {{ $item->status }}
                                </td>
                                <td class="p-3 text-sm text-white/90">{{ $item->borrow_date }}</td>
                                <td class="p-3 text-sm text-white/90">{{ $item->due_date ?? '-' }}</td>
                                <td class="p-3 text-sm text-white/90">{{ $item->return_date ?? '-' }}</td>
                                <td class="p-3 text-sm text-center">
                                    @if($item->status != 'Selesai' && $item->status != 'dikembalikan')
                                        <div class="flex justify-center items-center gap-1">
                                            <!-- Tombol Batalkan -->
                                            <form action="{{ route('circulation.cancel', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan peminjaman ini?')">
                                                @csrf
                                                <button type="submit" class="bg-red-600 text-white p-1 rounded hover:bg-red-700 transition flex items-center justify-center w-6 h-6 text-xs font-bold shadow" title="Batalkan">
                                                    &#10005;
                                                </button>
                                            </form>

                                            <!-- Tombol Selesai -->
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
                                <td colspan="10" class="p-5 text-center text-sm font-semibold text-white/80">Data sirkulasi tidak ditemukan.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginasi Bersih dan Minimalis -->
                <div class="flex justify-center items-center gap-2 mt-6 text-white font-bold">
                    
                    {{-- Tombol Sebelumnya (<) --}}
                    @if ($circulations->onFirstPage())
                        <span class="px-2.5 py-1 rounded text-sm opacity-50 cursor-not-allowed">&lt;</span>
                    @else
                        <a href="{{ $circulations->previousPageUrl() }}" class="px-2.5 py-1 hover:bg-white/20 rounded text-sm transition">&lt;</a>
                    @endif

                    {{-- Nomor Halaman (Selalu Tampil Minimal 1) --}}
                    @foreach ($circulations->getUrlRange(1, max($circulations->lastPage(), 1)) as $page => $url)
                        @if ($page == $circulations->currentPage())
                            {{-- Halaman Aktif (Kotak Putih, Teks Gelap) --}}
                            <span class="px-2.5 py-1 bg-white text-gray-700 rounded text-sm shadow">{{ $page }}</span>
                        @else
                            {{-- Halaman Lain --}}
                            <a href="{{ $url }}" class="px-2.5 py-1 hover:bg-white/20 rounded text-sm transition">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Tombol Selanjutnya (>) --}}
                    @if ($circulations->hasMorePages())
                        <a href="{{ $circulations->nextPageUrl() }}" class="px-2.5 py-1 hover:bg-white/20 rounded text-sm transition">&gt;</a>
                    @else
                        <span class="px-2.5 py-1 rounded text-sm opacity-50 cursor-not-allowed">&gt;</span>
                    @endif

                </div>

            </div>
        </div>
    </main>
</div>

<!-- ====== POP-UP MODAL PEMINJAMAN BARU ====== -->
<div id="borrowModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
    <div class="bg-[#005a4e] text-white rounded-md shadow-2xl w-full max-w-xs p-5 relative border border-emerald-400/30">
        
        <button type="button" onclick="closeBorrowModal()" class="absolute top-3 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
            &#10005;
        </button>

        <h3 class="text-xl font-bold mb-4 tracking-wide">Peminjaman Baru</h3>

        <!-- Tempat Error Validasi / Exception -->
        <div id="modalErrorContainer" class="mb-3 p-2 bg-red-600 text-white rounded text-xs {{ $errors->borrowForm->any() ? '' : 'hidden' }}">
            @if ($errors->borrowForm->any())
                @foreach ($errors->borrowForm->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            @endif
        </div>

        <form action="{{ route('circulation.store') }}" method="POST" class="space-y-3">
            @csrf
            
            <!-- Input Scan Kartu Anggota -->
            <div>
                <label class="block text-sm font-semibold mb-1 text-white">No. Identitas (NIS / NIP / NIK)</label>
                <input type="text" id="inputScanKartu" name="identitas" placeholder="Scan Barcode Kartu Perpus..." value="{{ old('identitas') }}" required class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none">
            </div>

            <!-- Input Nama Otomatis Muncul -->
            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Nama Anggota</label>
                <input type="text" id="inputNama" name="nama" placeholder="Otomatis terisi..." readonly class="w-full bg-gray-300 text-gray-700 text-sm font-medium px-3 py-1.5 rounded outline-none cursor-not-allowed" value="{{ old('nama') }}">
            </div>

            <!-- Input Scan Buku (Nomor Inventaris) -->
            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Nomor Inventaris Buku</label>
                <input type="text" id="inputScanBuku" name="book_item_id" placeholder="Scan Barcode Buku..." value="{{ old('book_item_id') }}" required class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none">
            </div>

            <!-- Input Judul Buku Otomatis Muncul (Baru) -->
            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Judul Buku</label>
                <input type="text" id="inputJudulBuku" placeholder="Otomatis terisi..." readonly class="w-full bg-gray-300 text-gray-700 text-sm font-medium px-3 py-1.5 rounded outline-none cursor-not-allowed">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1 text-white">Tanggal Pinjam</label>
                <input type="date" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none">
            </div>

            <div class="pt-2 text-center">
                <button type="submit" class="bg-white text-[#004d40] hover:bg-emerald-50 px-6 py-1.5 rounded font-bold transition shadow-md w-full">
                    Konfirmasi Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPT JS MODAL -->
<script>
    function openBorrowModal() {
        const modal = document.getElementById('borrowModal');
        if (modal) {
            modal.classList.remove('hidden');
            // Fokus otomatis ke input pertama saat modal terbuka (opsional tapi disarankan)
            const inputIdentitas = document.getElementById('inputScanKartu');
            if (inputIdentitas) inputIdentitas.focus();
        }
    }

    // Fungsi untuk menutup modal
    function closeBorrowModal() {
        const modal = document.getElementById('borrowModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const inputIdentitas = document.getElementById('inputScanKartu');
        const inputNama = document.getElementById('inputNama');
        const inputBookItem = document.getElementById('inputScanBuku');

        // --- 1. OTOMATIS BUKA MODAL JIKA ADA ERROR DARI SERVER ---
        @if ($errors->borrowForm->any())
            openBorrowModal();
        @endif

        // --- 2. LISTENER SCAN KARTU ANGGOTA ---
        if (inputIdentitas) {
            inputIdentitas.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); 
                    let nomor = this.value.trim();

                    if (nomor.length > 0) {
                        fetch(`/api/check-member/${nomor}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    inputNama.value = data.name;
                                    if (inputBookItem) {
                                        inputBookItem.focus();
                                    }
                                } else {
                                    inputNama.value = 'Anggota tidak ditemukan';
                                    inputIdentitas.value = '';
                                    inputIdentitas.focus();
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    }
                }
            });
        }

        // --- 3. LISTENER SCAN BUKU (NOMOR INVENTARIS) ---
        if (inputBookItem) {
            inputBookItem.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Mencegah form tersubmit otomatis saat menekan Enter pada barcode scanner
                    let nomorInv = this.value.trim();

                    if (nomorInv.length > 0) {
                        fetch(`/api/check-book/${nomorInv}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    inputJudulBuku.value = data.title;
                                    
                                    // Opsional: Validasi status langsung di sisi client jika buku sedang dipinjam
                                    if (data.status === 'dipinjam') {
                                        alert('Peringatan: Buku ini sedang dalam status dipinjam!');
                                    }
                                } else {
                                    inputJudulBuku.value = 'Buku tidak ditemukan';
                                    inputBookItem.value = '';
                                    inputBookItem.focus();
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    }
                }
            });
        }
    });
</script>
@endsection