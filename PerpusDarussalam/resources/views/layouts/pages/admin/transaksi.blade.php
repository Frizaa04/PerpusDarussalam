@extends('layouts.pages.admin.provider.app')

@section('content')
    <div class="flex min-h-screen bg-[#f4f7f6]">

        <!-- Pemanggilan Sidebar -->
        @include('layouts.pages.admin.provider.sidebar')

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
                    <form action="{{ route('transaction.index') }}" method="GET"
                        class="flex items-center border-2 border-[#004d40] rounded overflow-hidden bg-white w-full sm:w-80 shadow-sm">

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari Data Transaksi"
                            class="w-full px-4 py-2 text-gray-700 outline-none font-medium placeholder-gray-400">

                        @if (request('jenis'))
                            <input type="hidden" name="jenis" value="{{ request('jenis') }}">
                        @endif

                        <button type="submit"
                            class="bg-[#004d40] text-white px-4 py-2 flex items-center justify-center hover:bg-[#003d30] transition">
                            <span class="material-icons">search</span>
                        </button>
                    </form>

                    <!-- Tombol + Transaksi Baru -->
                    <button type="button" onclick="openModal()"
                        class="border-2 border-[#004d40] text-[#004d40] font-bold px-4 py-2 rounded bg-white hover:bg-emerald-50 transition text-sm shadow-sm flex items-center gap-1.5 whitespace-nowrap">
                        <span class="material-icons text-lg">add</span>
                        <span>Transaksi</span>
                    </button>
                </div>

                <!-- Form Hapusan Massal / Bulk Delete -->
                <div id="deleteFormContainer">
                    <!-- Box Tabel -->
                    <div class="bg-[#b0bec5] p-6 rounded shadow-[0_4px_12px_rgba(0,0,0,0.15)] border border-gray-300/30">

                        <!-- Header Atas Tabel -->
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold text-white tracking-wide uppercase">Tabel Daftar Transaksi</h2>

                            <div class="flex items-center gap-3">
                                <label id="selectAllContainer"
                                    class="flex items-center gap-2 text-white font-bold cursor-pointer select-none hidden ml-2">
                                    <span>Pilih Semua</span>
                                    <input type="checkbox" id="selectAllCheckboxMain" onchange="toggleSelectAll(this)"
                                        class="w-5 h-5 accent-red-600 cursor-pointer rounded border-2 border-white">
                                </label>

                                <!-- 1. Tombol Konfirmasi Hapus (Awalnya Tersembunyi, Muncul saat Mode Hapus) -->
                                <button type="button" id="btnConfirmDelete" onclick="submitDeleteForm()"
                                    class="bg-red-700 text-white font-bold px-4 py-1.5 rounded hover:bg-red-800 transition text-sm shadow hidden">
                                    Konfirmasi Hapus (<span id="jumlahTerpilih">0</span>)
                                </button>

                                <!-- Tombol Trigger Utama Kelola Mode Hapus Transaksi -->
                                <button type="button" id="btnToggleDelete" onclick="toggleDeleteMode()"
                                    class="bg-[#004d40] text-white font-bold px-4 py-1.5 rounded hover:bg-[#003d30] transition text-sm shadow flex items-center gap-1.5 select-none">

                                    <!-- Ikon SVG tempat sampah / close -->
                                    <svg id="trashIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>

                                    <span id="btnText">Hapus Transaksi</span>
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
                                        <th class="p-3 text-sm font-bold tracking-wider">Status Bayar</th>
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
                                                @if ($transaction->status_bayar === 'sudah_bayar')
                                                    <span
                                                        class="bg-green-600 text-white text-xs font-bold px-2 py-1 rounded">Sudah
                                                        Bayar</span>
                                                @else
                                                    <span
                                                        class="bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">Belum
                                                        Bayar</span>
                                                @endif
                                            </td>
                                            <td class="p-3 text-sm font-medium text-center">
                                                <div class="flex items-center justify-center gap-3">
                                                    <button type="button" onclick="openEditModal({{ $transaction->id }})"
                                                        class="edit-mode-action bg-[#004d40] text-white text-xs font-bold px-3 py-1.5 rounded hover:bg-[#003d30] transition border border-white/20">
                                                        Edit Data
                                                    </button>
                                                    <!-- Checkbox per Baris (Tersembunyi Awal) -->
                                                    <input type="checkbox" value="{{ $transaction->id }}"
                                                        class="transaction-checkbox delete-mode-action w-5 h-5 accent-red-600 cursor-pointer rounded border-2 border-white hidden">
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="p-5 text-center text-sm font-semibold text-white/80">
                                                Belum ada data transaksi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $transactions->links('vendor.pagination.custom') }}
                        </div>

                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- ================= MODAL POP-UP TAMBAH TRANSAKSI ================= -->
    <div id="modal-transaction"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm {{ $errors->any() ? '' : 'hidden' }}">
        <div class="bg-[#004d40] text-white rounded-lg p-6 w-full max-w-2xl shadow-2xl relative border border-emerald-600">

            <!-- Header Modal & Tombol Close (X) -->
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-2xl font-bold tracking-wide">Transaksi</h3>
                <button type="button" onclick="closeModal()"
                    class="text-white hover:text-gray-300 font-bold text-2xl leading-none">
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
                        <input type="text" id="input-no-identitas" name="no_identitas" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 placeholder-gray-500 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white">
                    </div>


                    <!-- Kolom Kanan 1: Nominal -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Nominal</label>
                        <input type="number" name="nominal" value="{{ old('nominal') }}" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 placeholder-gray-500 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white">
                    </div>

                    <!-- Kolom Kiri 2: Nama (Opsional) -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama</label>
                        <input type="text" id="input-nama-user" name="name" placeholder="..." readonly
                            class="w-full bg-[#90a4ae] text-gray-800 placeholder-gray-500 rounded px-3 py-2 outline-none font-medium cursor-not-allowed">
                    </div>

                    <!-- Kolom Kanan 2: Keterangan -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Keterangan</label>
                        <input type="text" name="keterangan" value="{{ old('keterangan') }}" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 placeholder-gray-500 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white">
                    </div>

                    <!-- Kolom Kiri 3: Jenis Transaksi -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Jenis Transaksi</label>
                        <select name="jenis"
                            class="w-full bg-[#b0bec5] text-gray-800 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white cursor-pointer">
                            <option value="" disabled selected>...</option>
                            <option value="pembuatan_kartu" {{ old('jenis') == 'pembuatan_kartu' ? 'selected' : '' }}>
                                Pembuatan Kartu</option>
                            <option value="kehilangan_kartu" {{ old('jenis') == 'kehilangan_kartu' ? 'selected' : '' }}>
                                Kehilangan Kartu</option>
                            <option value="denda_keterlambatan"
                                {{ old('jenis') == 'denda_keterlambatan' ? 'selected' : '' }}>Denda Keterlambatan</option>
                            <option value="kehilangan_buku" {{ old('jenis') == 'kehilangan_buku' ? 'selected' : '' }}>
                                Kehilangan Buku</option>
                            <option value="perpanjang_kartu" {{ old('jenis') == 'perpanjang_kartu' ? 'selected' : '' }}>
                                Perpanjang Kartu</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Status Bayar</label>
                        <select name="status_bayar"
                            class="w-full bg-[#b0bec5] text-gray-800 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white cursor-pointer">
                            <option value="belum_bayar" selected>Belum Bayar</option>
                            <option value="sudah_bayar">Sudah Bayar</option>
                        </select>
                    </div>

                    <!-- Kolom Kanan 3: Tanggal Transaksi -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Tanggal Transaksi</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}"
                            class="w-full bg-[#b0bec5] text-gray-800 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white">
                    </div>
                </div>

                <!-- Tombol Konfirmasi Modal -->
                <div class="mt-8 flex justify-center">
                    <button type="submit"
                        class="bg-white text-[#004d40] font-bold px-8 py-2 rounded shadow hover:bg-gray-100 transition">
                        Konfirmasi
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ================= MODAL POP-UP EDIT TRANSAKSI ================= -->
    <div id="modal-edit-transaction"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden">
        <div class="bg-[#004d40] text-white rounded-lg p-6 w-full max-w-2xl shadow-2xl relative border border-emerald-600">

            <!-- Header Modal & Tombol Close (X) -->
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-2xl font-bold tracking-wide">Edit Transaksi</h3>
                <button type="button" onclick="closeEditModal()"
                    class="text-white hover:text-gray-300 font-bold text-2xl leading-none">
                    &times;
                </button>
            </div>

            <!-- Form Edit Transaksi -->
            <form id="form-edit-transaction" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-left">

                    <!-- Kolom Kanan 1: Nominal -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Nominal</label>
                        <input type="number" id="edit-nominal" name="nominal" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 placeholder-gray-500 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white">
                    </div>

                    <!-- Kolom Kanan 2: Keterangan -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Keterangan</label>
                        <input type="text" id="edit-keterangan" name="keterangan" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 placeholder-gray-500 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white">
                    </div>

                    <!-- Kolom Kiri 3: Jenis Transaksi -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Jenis Transaksi</label>
                        <select id="edit-jenis" name="jenis"
                            class="w-full bg-[#b0bec5] text-gray-800 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white cursor-pointer">
                            <option value="" disabled>...</option>
                            <option value="pembuatan_kartu">Pembuatan Kartu</option>
                            <option value="kehilangan_kartu">Kehilangan Kartu</option>
                            <option value="denda_keterlambatan">Denda Keterlambatan</option>
                            <option value="kehilangan_buku">Kehilangan Buku</option>
                            <option value="perpanjang_kartu">Perpanjang Kartu</option>
                        </select>
                    </div>

                    <!-- Kolom Kanan 3: Tanggal Transaksi -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Tanggal Transaksi</label>
                        <input type="date" id="edit-tanggal" name="tanggal"
                            class="w-full bg-[#b0bec5] text-gray-800 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Status Bayar</label>
                    <select id="edit-status-bayar" name="status_bayar"
                        class="w-full bg-[#b0bec5] text-gray-800 rounded px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-white cursor-pointer">
                        <option value="belum_bayar">Belum Bayar</option>
                        <option value="sudah_bayar">Sudah Bayar</option>
                    </select>
                </div>

                <!-- Tombol Konfirmasi Modal -->
                <div class="mt-8 flex justify-center">
                    <button type="submit"
                        class="bg-white text-[#004d40] font-bold px-8 py-2 rounded shadow hover:bg-gray-100 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Script Modal & Toggle Delete Mode -->
    <script>
        let isDeleteModeActive = false;

        function openModal() {
            $('#modal-transaction').removeClass('hidden');
        }

        function closeModal() {
            $('#modal-transaction').addClass('hidden');
        }

        function openEditModal(id) {
            $.ajax({
                url: `/admin/transaksi/${id}/edit`,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        let data = res.data;

                        $('#form-edit-transaction').attr('action', `/admin/transaksi/${id}`);
                        $('#edit-nominal').val(data.nominal || '');
                        $('#edit-keterangan').val(data.keterangan || '');
                        $('#edit-jenis').val(data.jenis || '');
                        $('#edit-tanggal').val(data.tanggal || '');
                        $('#edit-status-bayar').val(data.status_bayar || 'belum_bayar');

                        $('#modal-edit-transaction').removeClass('hidden');
                    }
                },
                error: function(err) {
                    console.error('Error:', err);
                }
            });
        }

        function closeEditModal() {
            $('#modal-edit-transaction').addClass('hidden');
        }

        // ==========================================
        // FITUR: MODE HAPUS MASSAL LINTAS HALAMAN (sama seperti Katalog Buku)
        // ==========================================
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
                $trashIcon.html(
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />');

                $selectAllContainer.removeClass('hidden');
                sessionStorage.setItem('transaction_delete_mode_active', 'true');

                $('.edit-mode-action').addClass('hidden');
                $('.delete-mode-action').removeClass('hidden');
            } else {
                $btnToggle.removeClass('bg-gray-700 hover:bg-gray-800').addClass('bg-[#004d40] hover:bg-[#003d30]');
                $btnText.text('Hapus Transaksi');
                $trashIcon.html(
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />'
                    );

                $btnConfirm.addClass('hidden');
                $selectAllContainer.addClass('hidden');
                sessionStorage.setItem('transaction_delete_mode_active', 'false');

                $('.edit-mode-action').removeClass('hidden');
                $('.delete-mode-action').addClass('hidden');

                sessionStorage.removeItem('selected_transaction_ids');
                $('#selectAllCheckboxMain').prop('checked', false);
                $('.transaction-checkbox').prop('checked', false);
            }
            updateConfirmDeleteButtonState();
        }

        function toggleSelectAll(master, targetClass = 'transaction-checkbox') {
            let checkboxes = $('.' + targetClass);
            checkboxes.prop('checked', master.checked);

            let selectedTransactionIds = JSON.parse(sessionStorage.getItem('selected_transaction_ids')) || [];

            checkboxes.each(function() {
                let id = $(this).val();
                if (master.checked) {
                    if (!selectedTransactionIds.includes(id)) selectedTransactionIds.push(id);
                } else {
                    selectedTransactionIds = selectedTransactionIds.filter(item => item !== id);
                }
            });

            sessionStorage.setItem('selected_transaction_ids', JSON.stringify(selectedTransactionIds));
            updateConfirmDeleteButtonState();
        }

        function updateConfirmDeleteButtonState() {
            let selectedTransactionIds = JSON.parse(sessionStorage.getItem('selected_transaction_ids')) || [];
            let totalCount = selectedTransactionIds.length;

            $('#jumlahTerpilih').text(totalCount);

            if (isDeleteModeActive && totalCount > 0) {
                $('#btnConfirmDelete').removeClass('hidden');
            } else {
                $('#btnConfirmDelete').addClass('hidden');
            }
        }

        // 4. Event Listener Utama (Document Ready)
        $(document).ready(function() {

            // ==========================================
            // INISIALISASI MEMORI HAPUS TRANSAKSI LINTAS HALAMAN
            // ==========================================
            let selectedTransactionIds = JSON.parse(sessionStorage.getItem('selected_transaction_ids')) || [];

            // Pertahankan mode hapus saat pindah halaman paginasi
            if (sessionStorage.getItem('transaction_delete_mode_active') === 'true') {
                setTimeout(function() {
                    isDeleteModeActive = false;
                    toggleDeleteMode();
                }, 150);
            }

            // Otomatis centang ulang transaksi yang ID-nya ada di memori browser
            $('.transaction-checkbox').each(function() {
                if (selectedTransactionIds.includes($(this).val())) {
                    $(this).prop('checked', true);
                }
            });

            // Pantau klik checkbox transaksi secara individual
            $(document).on('change', '.transaction-checkbox', function() {
                let id = $(this).val();
                let ids = JSON.parse(sessionStorage.getItem('selected_transaction_ids')) || [];

                if ($(this).is(':checked')) {
                    if (!ids.includes(id)) ids.push(id);
                } else {
                    ids = ids.filter(item => item !== id);
                }
                sessionStorage.setItem('selected_transaction_ids', JSON.stringify(ids));
                updateConfirmDeleteButtonState();
            });

            // Event listener klik tombol Konfirmasi Hapus Transaksi Lintas Halaman
            $('#btnConfirmDelete').on('click', function(e) {
                e.preventDefault();

                let finalIds = JSON.parse(sessionStorage.getItem('selected_transaction_ids')) || [];

                if (finalIds.length === 0) {
                    alert('Silakan pilih minimal satu transaksi untuk dihapus!');
                    return;
                }

                if (confirm(
                        `Yakin ingin menghapus ${finalIds.length} transaksi yang dipilih dari berbagai halaman?`
                        )) {
                    $.ajax({
                        url: "{{ route('transaction.destroy.bulk') }}",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE',
                            ids: finalIds
                        },
                        success: function(response) {
                            sessionStorage.removeItem('selected_transaction_ids');
                            sessionStorage.removeItem('transaction_delete_mode_active');
                            location.reload();
                        },
                        error: function(xhr) {
                            alert('Gagal menghapus data transaksi lintas halaman. Status: ' +
                                xhr.status);
                        }
                    });
                }
            });

            updateConfirmDeleteButtonState();

            // Live Search User berdasarkan No Identitas (dengan Debounce)
            let timeout = null;
            let $inputNoIdentitas = $('#input-no-identitas');
            let $inputNamaUser = $('#input-nama-user');

            $inputNoIdentitas.on('keydown', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                }
            });

            $inputNoIdentitas.on('keyup', function() {
                clearTimeout(timeout);
                let identitas = $(this).val().trim();

                if (identitas === '') {
                    $inputNamaUser.val('');
                    return;
                }

                timeout = setTimeout(() => {
                    $.ajax({
                        url: `/admin/transaksi/cari-user/${encodeURIComponent(identitas)}`,
                        type: 'GET',
                        dataType: 'json',
                        success: function(res) {
                            if (res.success) {
                                $inputNamaUser.val(res.name);
                            } else {
                                $inputNamaUser.val('Tidak ditemukan');
                            }
                        },
                        error: function(err) {
                            console.error('Error:', err);
                        }
                    });
                }, 300);
            });

            // Auto-isi nominal berdasarkan jenis (Modal Tambah Transaksi)
            $('#modal-transaction select[name="jenis"]').on('change', function() {
                let jenis = $(this).val();
                $.getJSON(`/admin/transaksi/tarif/${jenis}`, function(data) {
                    if (data.success) {
                        $('#modal-transaction input[name="nominal"]').val(data.nominal);
                    }
                }).fail(function(err) {
                    console.error('Error:', err);
                });
            });

            // Auto-isi nominal berdasarkan jenis (Modal Edit Transaksi)
            $('#edit-jenis').on('change', function() {
                let jenis = $(this).val();
                $.getJSON(`/admin/transaksi/tarif/${jenis}`, function(data) {
                    if (data.success) {
                        $('#edit-nominal').val(data.nominal);
                    }
                }).fail(function(err) {
                    console.error('Error:', err);
                });
            });

        });
    </script>
@endsection
