@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Pemanggilan Sidebar -->
    @include('layouts.sidebar')

    <main class="flex-1 flex flex-col">
        <!-- Header Atas -->
        <header class="bg-white border-b border-gray-200 px-8 flex justify-end items-center shadow-sm h-20">
            <div class="flex items-center h-full gap-4">
                <!-- Notifikasi -->
                <button class="text-[#004d40] hover:text-[#003d30] p-1">
                    <span class="material-icons text-2xl">notifications_none</span>
                </button>
                
                <!-- Tombol LogOut -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-[#004d40] text-white px-4 py-1.5 rounded text-sm font-semibold hover:bg-[#003d30] transition">
                        LogOut
                    </button>
                </form>

                <!-- Logo Darussalam -->
                <img src="{{ asset('image/covers/darussalam.png') }}" alt="Logo Darussalam" class="h-12 object-contain py-1">
            </div>
        </header>

        <!-- Isi Data Transaksi -->
        <div class="p-8 space-y-6">
            
            <!-- Baris Pencarian & Tombol Tambah -->
            <div class="flex items-center gap-3">
                <!-- Form Cari Data -->
                <form action="{{ route('transaction.index') }}" method="GET" class="flex items-center border-2 border-[#004d40] rounded overflow-hidden bg-white w-full sm:w-80 shadow-sm">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari Data Transaksi" class="w-full px-4 py-2 text-gray-700 outline-none font-medium placeholder-gray-400">
                    <button type="submit" class="bg-[#004d40] text-white px-4 py-2 flex items-center justify-center hover:bg-[#003d30] transition">
                        <span class="material-icons">search</span>
                    </button>
                </form>

                <!-- Tombol + Transaksi Baru (Memicu Pop-up Modal) -->
                <button type="button" onclick="openModal()" class="border-2 border-[#004d40] text-[#004d40] font-bold px-4 py-2 rounded bg-white hover:bg-emerald-50 transition text-sm shadow-sm flex items-center gap-1.5 whitespace-nowrap">
                    <span class="material-icons text-lg">add</span>
                    <span>Transaksi</span>
                </button>
            </div>

            <!-- Form Hapusan Massal / Bulk Delete -->
            <form id="form-delete-bulk" action="#" method="POST">
                @csrf
                @method('DELETE')

                <!-- Box Tabel (Latar belakang abu-abu) -->
                <div class="bg-[#b0bec5] p-6 rounded shadow-[0_4px_12px_rgba(0,0,0,0.15)] border border-gray-300/30">
                    
                    <!-- Header Atas Tabel -->
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-white tracking-wide uppercase">Tabel Daftar Transaksi</h2>
                        
                        <!-- Pilihan Hapus Transaksi + Checkbox Utama -->
                        <label class="flex items-center gap-2 text-white font-bold cursor-pointer select-none">
                            <span>Hapus Transaksi</span>
                            <input type="checkbox" id="check-all" class="w-5 h-5 accent-[#004d40] rounded border-white/60 cursor-pointer">
                        </label>
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
                                            Rp.{{ number_format($transaction->nominal, 0, ',', '.') }}
                                        </td>
                                        <td class="p-3 text-sm font-medium text-white/90">
                                            {{ \Carbon\Carbon::parse($transaction->tanggal)->format('d/m/Y') }}
                                        </td>
                                        <td class="p-3 text-sm font-medium text-white/90 italic">
                                            {{ $transaction->keterangan ?? '...' }}
                                        </td>
                                        <td class="p-3 text-sm font-medium text-center">
                                            <div class="flex items-center justify-center gap-3">
                                                <a href="{{ route('transaction.edit', $transaction->id) }}" class="inline-block bg-[#004d40] text-white text-xs font-bold px-3 py-1.5 rounded hover:bg-[#003d30] transition border border-white/20">
                                                    Edit Data
                                                </a>
                                                <input type="checkbox" name="ids[]" value="{{ $transaction->id }}" class="item-checkbox w-5 h-5 accent-[#004d40] rounded border-white/60 cursor-pointer">
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

                    <!-- Pagination Bawah -->
                    <div class="mt-6 tx-pagination">
                        {{ $transactions->appends(['search' => $search])->links() }}
                    </div>

                </div>
            </form>

        </div>
    </main>
</div>

<!-- ================= MODAL POP-UP TAMBAH TRANSAKSI ================= -->
<div id="modal-transaction" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden">
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
                    <input type="text" name="user_id" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 placeholder-gray-500 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white">
                </div>

                <!-- Kolom Kanan 1: Nominal -->
                <div>
                    <label class="block text-sm font-medium mb-1">Nominal</label>
                    <input type="number" name="nominal" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 placeholder-gray-500 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white">
                </div>

                <!-- Kolom Kiri 2: Nama -->
                <div>
                    <label class="block text-sm font-medium mb-1">Nama</label>
                    <input type="text" name="name" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 placeholder-gray-500 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white">
                </div>

                <!-- Kolom Kanan 2: Keterangan -->
                <div>
                    <label class="block text-sm font-medium mb-1">Keterangan</label>
                    <input type="text" name="keterangan" placeholder="..." class="w-full bg-[#b0bec5] text-gray-800 placeholder-gray-500 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white">
                </div>

                <!-- Kolom Kiri 3: Jenis Transaksi -->
                <div>
                    <label class="block text-sm font-medium mb-1">Jenis Transaksi</label>
                    <select name="jenis" class="w-full bg-[#b0bec5] text-gray-800 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white cursor-pointer">
                        <option value="" disabled selected>...</option>
                        <option value="pembuatan_kartu">Pembuatan Kartu</option>
                        <option value="kehilangan_kartu">Kehilangan Kartu</option>
                        <option value="denda_keterlambatan">Denda Keterlambatan</option>
                    </select>
                </div>

                <!-- Kolom Kanan 3: Tanggal Transaksi -->
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Transaksi</label>
                    <input type="date" name="tanggal" class="w-full bg-[#b0bec5] text-gray-800 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white">
                </div>
            </div>

            <!-- Tombol Konfirmasi (Tengah Bawah) -->
            <div class="mt-8 flex justify-center">
                <button type="submit" class="bg-white text-[#004d40] font-bold px-8 py-2 rounded shadow hover:bg-gray-100 transition">
                    Konfirmasi
                </button>
            </div>
        </form>

    </div>
</div>

<!-- Script Modal & Toggle Checkbox -->
<script>
    function openModal() {
        document.getElementById('modal-transaction').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modal-transaction').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('check-all');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');

        if (checkAll) {
            checkAll.addEventListener('change', function () {
                itemCheckboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
            });
        }
    });
</script>

<style>
    .tx-pagination nav p {
        color: rgba(255, 255, 255, 0.8) !important;
    }
    .tx-pagination nav div:last-child span span, 
    .tx-pagination nav div:last-child a {
        background-color: white !important;
        color: #004d40 !important;
        border-color: #e5e7eb !important;
    }
    .tx-pagination nav div:last-child span[aria-current="page"] span {
        background-color: #004d40 !important;
        color: white !important;
        border-color: #004d40 !important;
    }
</style>
@endsection