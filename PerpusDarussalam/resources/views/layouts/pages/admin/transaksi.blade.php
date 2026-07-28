@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Pemanggilan Sidebar -->
    @include('layouts.sidebar')

    <main class="flex-1 flex flex-col">
                <!-- Isi Data Transaksi -->
        <div class="p-8 space-y-6">

            <!-- Notifikasi Pesan Sukses / Error Validasi -->
            @if (session('success'))
                <div class="bg-green-600 text-white p-4 rounded shadow">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-600 text-white p-4 rounded shadow">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <!-- Baris Pencarian & Tombol Tambah -->
            <div class="flex items-center gap-3">
                <!-- Form Cari Data -->
                <form action="{{ route('transaction.index') }}" method="GET" class="flex items-center border-2 border-[#004d40] rounded overflow-hidden bg-white w-full sm:w-80 shadow-sm">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari Data Transaksi" class="w-full px-4 py-2 text-gray-700 outline-none font-medium placeholder-gray-400">
                    <button type="submit" class="bg-[#004d40] text-white px-4 py-2 flex items-center justify-center hover:bg-[#003d30] transition">
                        <span class="material-icons">search</span>
                    </button>
                </form>

                <!-- Tombol + Transaksi Baru -->
                <button type="button" onclick="openModal()" class="border-2 border-[#004d40] text-[#004d40] font-bold px-4 py-2 rounded bg-white hover:bg-emerald-50 transition text-sm shadow-sm flex items-center gap-1.5 whitespace-nowrap">
                    <span class="material-icons text-lg">add</span>
                    <span>Transaksi</span>
                </button>
            </div>

            <!-- Form Hapusan Massal / Bulk Delete -->
            <form id="form-delete-bulk" action="{{ route('transaction.destroy.bulk') }}" method="POST">
                @csrf
                @method('DELETE')

                <!-- Box Tabel -->
                <div class="bg-[#b0bec5] p-6 rounded shadow-[0_4px_12px_rgba(0,0,0,0.15)] border border-gray-300/30">
                    
                    <!-- Header Atas Tabel -->
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-white tracking-wide uppercase">Tabel Daftar Transaksi</h2>
                        
                        <div class="flex items-center gap-3">
                            <label id="label-check-all" class="flex items-center gap-2 text-white font-bold cursor-pointer select-none hidden ml-2">
                                <span>Pilih Semua</span>
                                <input type="checkbox" id="check-all" class="w-5 h-5 accent-[#004d40] rounded border-white/60 cursor-pointer">
                            </label>
                        
                            <!-- 1. Tombol Konfirmasi Hapus (Awalnya Tersembunyi, Muncul saat Mode Hapus) -->
                            <button type="button" id="btn-submit-delete" onclick="submitDeleteForm()" class="bg-red-700 text-white font-bold px-4 py-1.5 rounded hover:bg-red-800 transition text-sm shadow hidden">
                                Konfirmasi Hapus
                            </button>

                            <!-- 2. Tombol Trigger Utama (Hijau "Hapus Data" -> Merah "Hapus Transaksi" untuk Cancel) -->
                            <button type="button" id="btn-toggle-delete" onclick="toggleDeleteMode()" class="bg-[#004d40] text-white font-bold px-4 py-1.5 rounded hover:bg-[#003d30] transition text-sm shadow flex items-center gap-1.5">
                                <span class="material-icons text-base">delete</span>
                                <span id="text-btn-toggle">Hapus Data</span>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded">
                        <table class="min-w-full text-left border-collapse border border-white/40">
                            <thead>
                                <tr class="bg-[#004d40] text-white divide-x divide-white/40">
                                    <th class="p-3 text-sm font-bold tracking-wider">No</th>
                                    <th class="p-3 text-sm font-bold tracking-wider">Nama</th>
                                    <th class="p-3 text-sm font-bold tracking-wider">Jenis</th>
                                    <th class="p-3 text-sm font-bold tracking-wider">Nominal</th>
                                    <th class="p-3 text-sm font-bold tracking-wider">Tanggal</th>
                                    <th class="p-3 text-sm font-bold tracking-wider">Keterangan</th>
                                    <th class="p-3 text-sm font-bold tracking-wider text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-white divide-y divide-white/40">
                                @forelse($transactions as $key => $transaction)
                                    <tr class="divide-x divide-white/40 hover:bg-white/10 transition-colors">
                                        <td class="p-3 text-sm font-medium text-white/90">
                                            {{ sprintf('%02d', $transactions->firstItem() + $key) }}
                                        </td>
                                        <td class="p-3 text-sm font-medium text-white/90">
                                            {{ $transaction->user->name ?? 'Non-Anggota' }}
                                        </td>
                                        <td class="p-3 text-sm font-medium text-white/90">
                                            {{ $transaction->jenis_label }}
                                        </td>
                                        <td class="p-3 text-sm font-medium text-white/90">
                                            Rp.{{ number_format($transaction->nominal ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="p-3 text-sm font-medium text-white/90">
                                            {{ \Carbon\Carbon::parse($transaction->tanggal)->format('d/m/Y') }}
                                        </td>
                                        <td class="p-3 text-sm font-medium text-white/90 italic">
                                            {{ $transaction->keterangan ?? '...' }}
                                        </td>
                                        <td class="p-3 text-sm font-medium text-center">
                                            <div class="flex items-center justify-center gap-3">
                                                <a href="{{ route('transaction.edit', $transaction->id) }}" class="edit-mode-action bg-[#004d40] text-white text-xs font-bold px-3 py-1.5 rounded hover:bg-[#003d30] transition border border-white/20">
                                                    Edit Data
                                                </a>
                                                <!-- Checkbox per Baris (Tersembunyi Awal) -->
                                                <input type="checkbox" name="ids[]" value="{{ $transaction->id }}" class="item-checkbox delete-mode-action w-5 h-5 accent-[#004d40] rounded border-white/60 cursor-pointer hidden">
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-5 text-center text-sm font-semibold text-white/80">Belum ada data transaksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Navigasi Paginasi Custom Tailwind Hijau (Tanpa Style CSS) -->
                    @if ($transactions->hasPages())
                        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-white">
                            <!-- Informasi Jumlah Data -->
                            <div class="text-sm font-semibold">
                                Menampilkan <span class="font-bold">{{ $transactions->firstItem() }}</span> hingga <span class="font-bold">{{ $transactions->lastItem() }}</span> dari <span class="font-bold">{{ $transactions->total() }}</span> data
                            </div>

                            <!-- Tombol Halaman -->
                            <div class="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                {{-- Tombol Sebelumnya (Previous) --}}
                                @if ($transactions->onFirstPage())
                                    <span class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-[#004d40] bg-[#004d40]/40 text-white/50 cursor-not-allowed text-sm font-medium">
                                        &lsaquo;
                                    </span>
                                @else
                                    <a href="{{ $transactions->previousPageUrl() }}" class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-[#004d40] bg-white text-[#004d40] hover:bg-[#004d40] hover:text-white transition-colors text-sm font-medium">
                                        &lsaquo;
                                    </a>
                                @endif

                                {{-- Angka-angka Halaman --}}
                                @foreach ($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                                    @if ($page == $transactions->currentPage())
                                        <span aria-current="page" class="relative z-10 inline-flex items-center px-4 py-2 border border-[#004d40] bg-[#004d40] text-white font-bold text-sm">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-[#004d40] bg-white text-[#004d40] hover:bg-[#004d40] hover:text-white transition-colors text-sm font-medium">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach

                                {{-- Tombol Selanjutnya (Next) --}}
                                @if ($transactions->hasMorePages())
                                    <a href="{{ $transactions->nextPageUrl() }}" class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-[#004d40] bg-white text-[#004d40] hover:bg-[#004d40] hover:text-white transition-colors text-sm font-medium">
                                        &rsaquo;
                                    </a>
                                @else
                                    <span class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-[#004d40] bg-[#004d40]/40 text-white/50 cursor-not-allowed text-sm font-medium">
                                        &rsaquo;
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>
            </form>

        </div>
    </main>
</div>

<!-- ================= MODAL POP-UP TAMBAH TRANSAKSI ================= -->
<div id="modal-transaction" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm {{ $errors->any() ? '' : 'hidden' }}">
    <div class="bg-[#004d40] text-white rounded-lg p-6 w-full max-w-2xl shadow-2xl relative border border-emerald-600">
        
        <!-- Header Modal & Tombol Close (X) -->
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-2xl font-bold tracking-wide">Transaksi</h3>
            <button type="button" onclick="closeModal()" class="text-white hover:text-gray-300 font-bold text-2xl leading-none">
                &times;
            </button>
        </div>

        <!-- Form Tambah Transaksi -->
        <form action="{{ route('transaction.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-left">
                <!-- Kolom Kiri 1: No Identitas -->
                <div>
                    <label class="block text-sm font-medium mb-1">No Identitas</label>
                    <input type="text" name="no_identitas" value="{{ old('no_identitas') }}" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 placeholder-gray-500 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white">
                </div>

                <!-- Kolom Kanan 1: Nominal -->
                <div>
                    <label class="block text-sm font-medium mb-1">Nominal</label>
                    <input type="number" name="nominal" value="{{ old('nominal') }}" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 placeholder-gray-500 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white">
                </div>

                <!-- Kolom Kiri 2: Nama (Opsional) -->
                <div>
                    <label class="block text-sm font-medium mb-1">Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 placeholder-gray-500 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white">
                </div>

                <!-- Kolom Kanan 2: Keterangan -->
                <div>
                    <label class="block text-sm font-medium mb-1">Keterangan</label>
                    <input type="text" name="keterangan" value="{{ old('keterangan') }}" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 placeholder-gray-500 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white">
                </div>

                <!-- Kolom Kiri 3: Jenis Transaksi -->
                <div>
                    <label class="block text-sm font-medium mb-1">Jenis Transaksi</label>
                    <select name="jenis" class="w-full bg-[#b0bec5] text-gray-800 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white cursor-pointer">
                        <option value="" disabled selected>...</option>
                        <option value="pembuatan_kartu" {{ old('jenis') == 'pembuatan_kartu' ? 'selected' : '' }}>Pembuatan Kartu</option>
                        <option value="kehilangan_kartu" {{ old('jenis') == 'kehilangan_kartu' ? 'selected' : '' }}>Kehilangan Kartu</option>
                        <option value="denda_keterlambatan" {{ old('jenis') == 'denda_keterlambatan' ? 'selected' : '' }}>Denda Keterlambatan</option>
                    </select>
                </div>

                <!-- Kolom Kanan 3: Tanggal Transaksi -->
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Transaksi</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="w-full bg-[#b0bec5] text-gray-800 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white">
                </div>
            </div>

            <!-- Tombol Konfirmasi Modal -->
            <div class="mt-8 flex justify-center">
                <button type="submit" class="bg-white text-[#004d40] font-bold px-8 py-2 rounded shadow hover:bg-gray-100 transition">
                    Konfirmasi
                </button>
            </div>
        </form>

    </div>
</div>

<!-- Script Modal & Toggle Delete Mode -->
<script>
    let isDeleteActive = false;

    function toggleDeleteMode() {
        const btnToggle = document.getElementById('btn-toggle-delete');
        const textBtn = document.getElementById('text-btn-toggle');
        const btnSubmit = document.getElementById('btn-submit-delete');
        const editActions = document.querySelectorAll('.edit-mode-action');
        const deleteActions = document.querySelectorAll('.delete-mode-action');
        const labelCheckAll = document.getElementById('label-check-all');
        const checkAll = document.getElementById('check-all');

        isDeleteActive = !isDeleteActive;

        if (isDeleteActive) {
            btnToggle.classList.remove('bg-[#004d40]', 'hover:bg-[#003d30]');
            btnToggle.classList.add('bg-red-600', 'hover:bg-red-700');
            textBtn.innerText = 'Hapus Transaksi'; // Atau 'Batal'

            btnSubmit.classList.remove('hidden');
            labelCheckAll.classList.remove('hidden');
            deleteActions.forEach(el => el.classList.remove('hidden'));

            editActions.forEach(el => el.classList.add('hidden'));

        } else {
            btnToggle.classList.remove('bg-red-600', 'hover:bg-red-700');
            btnToggle.classList.add('bg-[#004d40]', 'hover:bg-[#003d30]');
            textBtn.innerText = 'Hapus Data';

            btnSubmit.classList.add('hidden');
            labelCheckAll.classList.add('hidden');
            deleteActions.forEach(el => {
                el.classList.add('hidden');
                if (el.type === 'checkbox') el.checked = false;
            });

            editActions.forEach(el => el.classList.remove('hidden'));

            if (checkAll) checkAll.checked = false;
        }
    }

    function submitDeleteForm() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        const form = document.getElementById('form-delete-bulk');

        if (checkedBoxes.length === 0) {
            alert('Silakan pilih minimal satu data transaksi yang ingin dihapus.');
            return;
        }

        if (confirm(`Apakah Anda yakin ingin menghapus ${checkedBoxes.length} data transaksi yang dipilih?`)) {
            form.submit();
        }
    }

    function openModal() { document.getElementById('modal-transaction').classList.remove('hidden'); }
    function closeModal() { document.getElementById('modal-transaction').classList.add('hidden'); }

    document.addEventListener('DOMContentLoaded', () => {
        const checkAll = document.getElementById('check-all');
        if (checkAll) {
            checkAll.addEventListener('change', function () {
                document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
            });
        }
    });
</script>
@endsection