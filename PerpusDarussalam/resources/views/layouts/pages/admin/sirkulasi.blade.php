@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Pemanggilan Sidebar -->
    @include('layouts.sidebar')

    <main class="flex-1 flex flex-col">

        <!-- Header Atas -->
        <header class="bg-white border-b border-gray-200 px-8 flex justify-end items-center gap-4 shadow-sm h-20">
            <button type="button" class="text-gray-600 hover:text-[#004d40] transition">
                <span class="material-icons text-2xl">notifications</span>
            </button>
            <form action="{{ Route::has('logout') ? route('logout') : '#' }}" method="POST">
                @csrf
                <button type="submit" class="bg-[#005a4e] hover:bg-[#004d40] text-white px-4 py-1.5 rounded font-bold text-sm tracking-wide transition shadow-sm">
                    LogOut
                </button>
            </form>
            <div class="flex items-center h-full pl-2">
                <img src="{{ asset('image/covers/darussalam.png') }}" alt="Logo Darussalam" class="h-14 py-1 object-contain">
            </div>
        </header>

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
                    
                    <!-- box Peminjaman Telat -->
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
                                <th class="p-3 text-sm font-bold tracking-wider">No Identitas</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Judul Buku</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Status</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Tanggal Pinjam</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Tanggal Jatuh Tempo</th> 
                                <th class="p-3 text-sm font-bold tracking-wider">Tanggal Kembali</th>
                                <th class="p-3 text-sm font-bold tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-white divide-y divide-white/40">
                        @forelse($circulations as $index => $item)
                            <tr class="divide-x divide-white/40 hover:bg-white/10 transition-colors">
                                <td class="p-3 text-sm font-bold text-center text-white/90">{{ $index + 1 }}</td>
                                <td class="p-3 text-sm font-bold text-white/90">{{ $item->identitas }}</td>
                                <td class="p-3 text-sm text-white/90">{{ $item->book_title }}</td>
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
                                <td colspan="8" class="p-5 text-center text-sm font-semibold text-white/80">Data sirkulasi tidak ditemukan.</td>
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

<!-- ====== POP-UP MODAL PEMINJAMAN BARU ====== -->
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
                <input type="text" id="inputScanBuku" name="book_item_id" placeholder="Scan Barcode Buku (Cth: FIK-2026-001-INV-001)..." value="{{ old('book_item_id') }}" required class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none">
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
        document.getElementById('borrowModal').classList.remove('hidden');
        // Fokuskan otomatis ke input kartu perpus saat modal dibuka
        setTimeout(() => {
            document.getElementById('inputScanKartu').focus();
        }, 100);
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

    // Logika Scan Barcode Kartu & Buku
    document.addEventListener('DOMContentLoaded', function () {
        const inputIdentitas = document.getElementById('inputScanKartu');
        const inputNama = document.getElementById('inputNama');
        const inputBookItem = document.getElementById('inputScanBuku');

        // 1. Saat Barcode Kartu Anggota di-scan (diakhiri tombol Enter dari scanner)
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
                                    // Pindahkan fokus secara otomatis ke input scan buku setelah kartu berhasil
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

        if (inputBookItem) {
            inputBookItem.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endsection