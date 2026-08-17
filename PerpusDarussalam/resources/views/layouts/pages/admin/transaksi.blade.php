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
                <form id="form-delete-bulk" action="{{ route('transaction.destroy.bulk') }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <!-- Box Tabel -->
                    <div class="bg-[#b0bec5] p-6 rounded shadow-[0_4px_12px_rgba(0,0,0,0.15)] border border-gray-300/30">

                        <!-- Header Atas Tabel -->
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold text-white tracking-wide uppercase">Tabel Daftar Transaksi</h2>

                            <div class="flex items-center gap-3">
                                <label id="label-check-all"
                                    class="flex items-center gap-2 text-white font-bold cursor-pointer select-none hidden ml-2">
                                    <span>Pilih Semua</span>
                                    <input type="checkbox" id="check-all"
                                        class="w-5 h-5 accent-[#004d40] rounded border-white/60 cursor-pointer">
                                </label>

                                <!-- 1. Tombol Konfirmasi Hapus (Awalnya Tersembunyi, Muncul saat Mode Hapus) -->
                                <button type="button" id="btn-submit-delete" onclick="submitDeleteForm()"
                                    class="bg-red-700 text-white font-bold px-4 py-1.5 rounded hover:bg-red-800 transition text-sm shadow hidden">
                                    Konfirmasi Hapus
                                </button>

                                <!-- 2. Tombol Trigger Utama (Hijau "Hapus Data" -> Abu-abu/Gelap "Batal" untuk Cancel) -->
                                <button type="button" id="btn-toggle-delete" onclick="toggleDeleteMode()"
                                    class="bg-[#004d40] text-white font-bold px-4 py-1.5 rounded hover:bg-[#003d30] transition text-sm shadow flex items-center gap-1.5 select-none">
                                    <span id="btn-icon" class="material-icons text-base">delete</span>
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
                                                    <input type="checkbox" name="ids[]" value="{{ $transaction->id }}"
                                                        class="item-checkbox delete-mode-action w-5 h-5 accent-[#004d40] rounded border-white/60 cursor-pointer hidden">
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
                </form>

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
                            <option value="kehilangan_buku"
                                {{ old('jenis') == 'kehilangan_buku' ? 'selected' : '' }}>Kehilangan Buku</option>
                            <option value="perpanjang_kartu"
                                {{ old('jenis') == 'perpanjang_kartu' ? 'selected' : '' }}>Perpanjang Kartu</option>
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
    let isDeleteActive = false;

    function toggleDeleteMode() {
        isDeleteActive = !isDeleteActive;

        let $btnToggle = $('#btn-toggle-delete');
        let $textBtn = $('#text-btn-toggle');
        let $btnIcon = $('#btn-icon');
        let $btnSubmit = $('#btn-submit-delete');
        let $labelCheckAll = $('#label-check-all');
        let $editActions = $('.edit-mode-action');
        let $deleteActions = $('.delete-mode-action');

        if (isDeleteActive) {
            // Mode Hapus Aktif
            $btnToggle.removeClass('bg-[#004d40] hover:bg-[#003d30]').addClass('bg-gray-700 hover:bg-gray-800');
            $textBtn.text('Batal');
            $btnIcon.text('close');

            $btnSubmit.removeClass('hidden');
            $labelCheckAll.removeClass('hidden');
            $deleteActions.removeClass('hidden');
            $editActions.addClass('hidden');
        } else {
            // Mode Batal/Normal
            $btnToggle.removeClass('bg-gray-700 hover:bg-gray-800').addClass('bg-[#004d40] hover:bg-[#003d30]');
            $textBtn.text('Hapus Data');
            $btnIcon.text('delete');

            $btnSubmit.addClass('hidden');
            $labelCheckAll.addClass('hidden');

            $deleteActions.each(function() {
                $(this).addClass('hidden');
                if (this.type === 'checkbox') this.checked = false;
            });

            $editActions.removeClass('hidden');
            $('#check-all').prop('checked', false);
        }
    }

    // 2. Fungsi Modal Tambah & Edit Transaksi
    function openModal() {
        $('#modal-transaction').removeClass('hidden');
    }

    function closeModal() {
        $('#modal-transaction').addClass('hidden');
    }

    function openEditModal(id) {
        $.ajax({
            url: `/transaksi/${id}/edit`,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    let data = res.data;

                    $('#form-edit-transaction').attr('action', `/transaksi/${id}`);
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

    // 3. Submit Hapus Massal
    function submitDeleteForm() {
        let $checkedBoxes = $('.item-checkbox:checked');

        if ($checkedBoxes.length === 0) {
            alert('Silakan pilih minimal satu data transaksi yang ingin dihapus.');
            return;
        }

        if (confirm(`Apakah Anda yakin ingin menghapus ${$checkedBoxes.length} data transaksi yang dipilih?`)) {
            $('#form-delete-bulk').submit();
        }
    }

    // 4. Event Listener Utama (Document Ready)
    $(document).ready(function() {

        // Check All Checkbox
        $('#check-all').on('change', function() {
            $('.item-checkbox').prop('checked', this.checked);
        });

        // Live Search User berdasarkan No Identitas (dengan Debounce)
        let timeout = null;
        let $inputNoIdentitas = $('#input-no-identitas');
        let $inputNamaUser = $('#input-nama-user');

        $inputNoIdentitas.on('keydown', function(e) {
            if (e.which === 13) {
                e.preventDefault(); // Mencegah form tersubmit saat tekan Enter
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
                    url: `/transaksi/cari-user/${encodeURIComponent(identitas)}`,
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
            $.getJSON(`/transaksi/tarif/${jenis}`, function(data) {
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
            $.getJSON(`/transaksi/tarif/${jenis}`, function(data) {
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
