@extends('layouts.pages.admin.provider.app')

@section('content')
    <div class="flex min-h-screen bg-[#f4f7f6]">

        <!-- Pemanggilan Sidebar -->
        @include('layouts.pages.admin.provider.sidebar')

        <main class="flex-1 flex flex-col">
            <!-- Isi Data -->
            <div class="p-8 space-y-6">

                <!-- Baris Pencarian, Tombol Filter, dan Tombol Print -->
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                        <!-- Form Cari Data User -->
                        <form action="{{ route('member.index') }}" method="GET"
                            class="flex items-center border-2 border-[#004d40] rounded overflow-hidden bg-white w-full sm:w-80">
                            <input type="text" name="search" value="{{ $search ?? '' }}"
                                placeholder="Cari Nama / No Induk"
                                class="w-full px-4 py-2 text-gray-700 outline-none font-medium placeholder-gray-400 text-sm">
                            @if (request('role'))
                                <input type="hidden" name="role" value="{{ request('role') }}">
                            @endif
                            <button type="submit"
                                class="bg-[#004d40] text-white px-4 py-2 flex items-center justify-center hover:bg-[#003d30] transition">
                                <span class="material-icons text-sm">search</span>
                            </button>
                        </form>

                        <!-- Tombol Filter Peran (Siswa, Guru, Umum, Semua) -->
                        <div class="flex items-center gap-1 bg-white p-1 rounded border-2 border-[#004d40]">
                            <a href="{{ route('member.index', ['search' => request('search')]) }}"
                                class="px-3 py-1 rounded text-xs font-bold transition {{ !request('role') ? 'bg-[#004d40] text-white' : 'text-[#004d40] hover:bg-emerald-50' }}">
                                Semua
                            </a>
                            <a href="{{ route('member.index', ['role' => 'siswa', 'search' => request('search')]) }}"
                                class="px-3 py-1 rounded text-xs font-bold transition {{ request('role') == 'siswa' ? 'bg-[#004d40] text-white' : 'text-[#004d40] hover:bg-emerald-50' }}">
                                Siswa
                            </a>
                            <a href="{{ route('member.index', ['role' => 'guru', 'search' => request('search')]) }}"
                                class="px-3 py-1 rounded text-xs font-bold transition {{ request('role') == 'guru' ? 'bg-[#004d40] text-white' : 'text-[#004d40] hover:bg-emerald-50' }}">
                                Guru
                            </a>
                            <a href="{{ route('member.index', ['role' => 'umum', 'search' => request('search')]) }}"
                                class="px-3 py-1 rounded text-xs font-bold transition {{ request('role') == 'umum' ? 'bg-[#004d40] text-white' : 'text-[#004d40] hover:bg-emerald-50' }}">
                                Umum
                            </a>
                        </div>
                        <button type="button" onclick="openAddUserModal()"
                            class="bg-white text-[#004d40] hover:bg-emerald-50 px-4 py-2 rounded-md font-bold text-sm shadow transition flex items-center gap-1.5 border border-[#004d40] whitespace-nowrap">
                            <span>+ User Baru</span>
                        </button>
                    </div>

                    <!-- Tombol Print (Membuka Modal Cetak Kartu Anggota) -->
                    <button type="button" onclick="openCetakModal()"
                        class="bg-[#004d40] text-white p-2.5 rounded hover:bg-[#003d30] transition shadow-md flex items-center justify-center">
                        <span class="material-icons">print</span>
                    </button>
                </div>

                <!-- Box Tabel -->
                <div class="bg-[#b0bec5] p-6 rounded shadow-[0_4px_12px_rgba(0,0,0,0.15)] border border-gray-300/30 table-manage">
                    <h2 class="text-xl font-bold text-white mb-4 tracking-wide">Tabel Daftar User</h2>

                    <div class="overflow-x-auto rounded">
                        <table class="min-w-full text-left border-collapse border border-white/40">
                            <thead>
                                <tr class="bg-[#004d40] text-white divide-x divide-white/40">
                                    <th class="p-3 text-xs font-bold tracking-wider">Foto</th>
                                    <th class="p-3 text-xs font-bold tracking-wider">No. Induk (NIS/NIK)</th>
                                    <th class="p-3 text-xs font-bold tracking-wider">Nama</th>
                                    <th class="p-3 text-xs font-bold tracking-wider">Kelamin</th>
                                    <th class="p-3 text-xs font-bold tracking-wider">Peran</th>
                                    <th class="p-3 text-xs font-bold tracking-wider">Email</th>
                                    <th class="p-3 text-xs font-bold tracking-wider">Alamat</th>
                                    <th class="p-3 text-xs font-bold tracking-wider text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-white divide-y divide-white/40">
                                @forelse($students as $student)
                                    <tr class="divide-x divide-white/40 hover:bg-white/10 transition-colors">
                                        <!-- Kolom Foto -->
                                        <td class="p-3 text-sm text-center">
                                            @if ($student->foto)
                                                <img src="{{ asset('storage/' . $student->foto) }}" alt="Foto"
                                                    class="w-10 h-10 rounded-full object-cover mx-auto border border-white">
                                            @else
                                                <div
                                                    class="w-10 h-10 rounded-full bg-gray-400 flex items-center justify-center text-xs font-bold mx-auto text-white">
                                                    {{ strtoupper(substr($student->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </td>
                                        <!-- Kolom No Induk -->
                                        <td class="p-3 text-sm font-bold text-white/90">
                                            {{ $student->nis ?? ($student->nik ?? '-') }}
                                        </td>
                                        <!-- Kolom Nama -->
                                        <td class="p-3 text-sm font-bold text-white/90">{{ $student->name }}</td>
                                        <!-- Kolom Jenis Kelamin -->
                                        <td class="p-3 text-sm font-bold text-white/90">
                                            {{ $student->jenis_kelamin == 'L' ? 'Laki-laki' : ($student->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                                        </td>
                                        <!-- Kolom Peran -->
                                        <td class="p-3 text-sm font-bold text-white/90">
                                            <span class="px-2 py-1 rounded text-xs bg-[#003d30] uppercase">
                                                {{ $student->role ?? 'Siswa' }}
                                            </span>
                                        </td>
                                        <!-- Kolom Email -->
                                        <td class="p-3 text-sm font-medium text-white/90">{{ $student->email }}</td>
                                        <!-- Kolom Alamat -->
                                        <td class="p-3 text-sm font-medium text-white/90 truncate max-w-xs">
                                            {{ $student->alamat ?? '-' }}</td>

                                        <!-- Tombol Aksi Edit -->
                                        <td class="p-3 text-sm text-center">
                                            <button type="button"
                                                onclick="openEditModal(
                                                    '{{ $student->id }}', 
                                                    '{{ $student->nis ?? '' }}', 
                                                    '{{ $student->nik ?? '' }}', 
                                                    '{{ addslashes($student->name) }}', 
                                                    '{{ $student->email }}', 
                                                    '{{ $student->role }}', 
                                                    '{{ $student->jenis_kelamin }}', 
                                                    '{{ addslashes($student->alamat) }}'
                                                )"
                                                class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold uppercase tracking-wider hover:bg-[#003d30] transition shadow-sm inline-block">
                                                Edit Data
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="p-5 text-center text-sm font-semibold text-white/80">Data
                                            user tidak ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Laravel -->
                    <div class="mt-4">
                        {{ $students->links() }}
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- ====== POP-UP MODAL TAMBAH USER ====== -->
    <div id="addUserModal"
        class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
        <div
            class="bg-[#005a4e] text-white rounded-md shadow-2xl w-full max-w-xl p-6 relative border border-emerald-400/30">

            <!-- Tombol Close -->
            <button type="button" onclick="closeAddUserModal()"
                class="absolute top-3 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
                &#10005;
            </button>

            <h3 class="text-xl font-bold mb-5 tracking-wide">Tambah User Baru</h3>

            <!-- Tempat Error Validasi -->
            <div id="addUserErrorContainer"
                class="mb-4 p-3 bg-red-600 text-white rounded text-xs {{ $errors->addUserForm->any() ? '' : 'hidden' }}">
                @if ($errors->addUserForm->any())
                    @foreach ($errors->addUserForm->all() as $error)
                        <div>- {{ $error }}</div>
                    @endforeach
                @endif
            </div>

            <form action="{{ route('member.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <!-- Form Grid 2 Kolom -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Foto</label>
                        <input type="file" name="foto"
                            class="w-full bg-[#b0bec5] text-gray-800 text-xs font-medium px-2 py-1.5 rounded outline-none file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-600 file:text-white">
                    </div>
                    <!-- Dropdown Peran -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">Peran</label>
                        <select name="role" id="peranAdd" required
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                            <option value="">Pilih Peran...</option>
                            <option value="siswa">Siswa</option>
                            <option value="guru">Guru</option>
                            <option value="umum">Umum</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">Nama Lengkap</label>
                        <input type="text" name="name" placeholder="Nama lengkap..." required
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Email</label>
                        <input type="email" name="email" placeholder="email@example.com" required
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>

                    <div>
                        <label id="labelNomorAdd" class="block text-sm font-semibold mb-1">No. Induk (NIS / NIK)</label>
                        <input type="text" name="nomor_induk" id="inputNomorAdd" placeholder="..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Password</label>
                        <input type="password" name="password" placeholder="Minimal 6 karakter" required
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">Jenis Kelamin</label>
                        <select name="jenis_kelamin"
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                            <option value="">Pilih...</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Alamat</label>
                        <input type="text" name="alamat" placeholder="Alamat lengkap..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                    </div>

                    <!-- Tambahan: Jenjang -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">Jenjang</label>
                        <select name="jenjang"
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                            <option value="">Pilih Jenjang...</option>
                            <option value="MTs">MTs</option>
                            <option value="MA">MA</option>
                        </select>
                    </div>

                    <!-- Tambahan: Kelas -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">Kelas</label>
                        <input type="text" name="kelas" placeholder="Contoh: 10A, 12 IPA 1..."
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

    <!-- POP-UP MODAL EDIT DATA USER -->
    <div id="editModal"
        class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
        <div
            class="bg-[#00695c] text-white rounded-lg shadow-2xl w-full max-w-lg p-6 relative border border-emerald-400/30 max-h-[90vh] overflow-y-auto">
            <!-- Tombol Close -->
            <button type="button" onclick="closeEditModal()"
                class="absolute top-3 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
                &#10005;
            </button>

            <!-- Judul Modal -->
            <h3 class="text-xl font-bold mb-5 tracking-wide">Edit Data User</h3>

            <!-- Form Edit -->
            <form action="{{ route('member.update') }}" method="POST" class="space-y-3">
                @csrf
                @method('PUT')

                <!-- ID Tersembunyi -->
                <input type="hidden" id="modalId" name="id">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Input Nama Lengkap -->
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-emerald-100">Nama Lengkap</label>
                        <input type="text" id="modalName" name="name" required
                            class="w-full bg-[#b0bec5] text-gray-800 font-medium px-3 py-1.5 rounded outline-none text-sm"
                            placeholder="Masukkan Nama...">
                    </div>

                    <!-- Label Dinamis untuk Nomor Induk di Modal Edit -->
                    <div>
                        <label id="modalLabelNomor" class="block text-xs font-semibold mb-1 text-emerald-100">No. Induk
                            (NIS / NIK)</label>
                        <input type="text" id="modalInputNomor" name="nomor_induk"
                            class="w-full bg-[#b0bec5] text-gray-800 font-medium px-3 py-1.5 rounded outline-none text-sm"
                            placeholder="Masukkan Nomor...">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-emerald-100">Email</label>
                        <input type="email" id="modalEmail" name="email" required
                            class="w-full bg-[#b0bec5] text-gray-800 font-medium px-3 py-1.5 rounded outline-none text-sm">
                    </div>
                    <!-- Dropdown Peran Edit -->
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-emerald-100">Peran (Role)</label>
                        <select id="modalRole" name="role"
                            class="w-full bg-[#b0bec5] text-gray-800 font-medium px-3 py-1.5 rounded outline-none text-sm">
                            <option value="siswa">Siswa</option>
                            <option value="guru">Guru</option>
                            <option value="umum">Umum</option>
                        </select>
                    </div>
                </div>

                <!-- Tambahan: Jenjang & Kelas di Edit -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-emerald-100">Jenjang</label>
                        <select id="modalJenjang" name="jenjang"
                            class="w-full bg-[#b0bec5] text-gray-800 font-medium px-3 py-1.5 rounded outline-none text-sm">
                            <option value="">Pilih Jenjang...</option>
                            <option value="MTs">MTs</option>
                            <option value="MA">MA</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-emerald-100">Kelas</label>
                        <input type="text" id="modalKelas" name="kelas" placeholder="Contoh: 10A, 12 IPA 1..."
                            class="w-full bg-[#b0bec5] text-gray-800 font-medium px-3 py-1.5 rounded outline-none text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-emerald-100">Jenis Kelamin</label>
                        <select id="modalJenisKelamin" name="jenis_kelamin"
                            class="w-full bg-[#b0bec5] text-gray-800 font-medium px-3 py-1.5 rounded outline-none text-sm">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-emerald-100">Alamat</label>
                        <input type="text" id="modalAlamat" name="alamat" placeholder="Alamat lengkap..."
                            class="w-full bg-[#b0bec5] text-gray-800 font-medium px-3 py-1.5 rounded outline-none text-sm">
                    </div>
                </div>

                <!-- Tombol Konfirmasi -->
                <div class="pt-3 text-center">
                    <button type="submit"
                        class="bg-white text-[#004d40] hover:bg-emerald-50 px-6 py-2 rounded font-bold transition shadow-md w-full">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- POP-UP MODAL CETAK KARTU ANGGOTA -->
    <div id="cetakModal"
        class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
        <div
            class="bg-[#004d40] text-white rounded-lg shadow-2xl w-full max-w-4xl p-6 relative border border-emerald-400/30">
            <!-- Tombol Close -->
            <button type="button" onclick="closeCetakModal()"
                class="absolute top-4 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
                &#10005;
            </button>

            <h3 class="text-xl font-bold mb-4 tracking-wide">Cetak Kartu Anggota</h3>

            <!-- Form untuk mengirim data user yang dicentang -->
            <form action="{{ route('member.printCards') }}" method="POST" target="_blank">
                @csrf

                <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
                    <div class="w-full sm:w-72">
                        <div class="flex items-center border border-white rounded overflow-hidden bg-white">
                            <!-- Input Search dengan ID agar terhubung ke fungsi JS filter -->
                            <input type="text" id="searchCetakInput" placeholder="Cari Data Siswa..."
                                class="w-full px-3 py-1.5 text-gray-700 outline-none text-sm placeholder-gray-400">
                            <button type="button"
                                class="bg-white text-[#004d40] px-3 py-1.5 flex items-center justify-center border-l border-gray-200">
                                <span class="material-icons text-lg">search</span>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-sm font-semibold">
                        <!-- Tombol Submit Cetak -->
                        <button type="submit"
                            class="flex items-center gap-1.5 hover:text-emerald-200 transition bg-[#003d30] px-3 py-1.5 rounded border border-white/40 shadow">
                            <span>[ Cetak Kartu Terpilih ]</span>
                            <span class="material-icons text-sm">print</span>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto rounded border border-white/40 max-h-96">
                    <table class="min-w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#003d30] border-b border-white/40 divide-x divide-white/40">
                                <th class="p-2.5 text-sm font-bold">No. Induk</th>
                                <th class="p-2.5 text-sm font-bold">Nama</th>
                                <th class="p-2.5 text-sm font-bold">Peran</th>
                                <th class="p-2.5 text-sm font-bold text-center w-12">
                                    <!-- Checkbox Pilih Semua -->
                                    <input type="checkbox" id="selectAll" class="rounded cursor-pointer"
                                        onclick="toggleSelectAll(this)">
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/40">
                            @forelse($students as $student)
                                <tr class="divide-x divide-white/40 hover:bg-white/10 transition-colors">
                                    <td class="p-2.5 text-sm font-medium">
                                        {{ $student->nis ?? ($student->nik ?? '-') }}
                                    </td>
                                    <td class="p-2.5 text-sm font-medium">{{ $student->name }}</td>
                                    <td class="p-2.5 text-sm font-medium">{{ ucfirst($student->role ?? 'Siswa') }}</td>
                                    <td class="p-2.5 text-center">
                                        <!-- Checkbox per Baris Data -->
                                        <input type="checkbox" name="selected_users[]" value="{{ $student->id }}"
                                            class="user-checkbox rounded cursor-pointer">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-sm font-medium text-white/80">Data
                                        tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT UNTUK CONTROL MODAL -->
    <script>
        // --- Fungsi Bantuan untuk Mengubah Label & Placeholder Nomor Induk ---
        function updateNomorField(roleValue, labelElementId, inputElement) {
            const labelNomor = document.getElementById(labelElementId);
            if (!labelNomor || !inputElement) return;

            let peran = roleValue ? roleValue.toLowerCase() : '';

            if (peran === 'siswa') {
                labelNomor.innerText = 'NIS (Nomor Induk Siswa)';
                inputElement.placeholder = 'Masukkan NIS...';
            } else if (peran === 'guru' || peran === 'umum') {
                labelNomor.innerText = 'NIK (Nomor Induk Kependudukan)';
                inputElement.placeholder = 'Masukkan NIK...';
            } else {
                labelNomor.innerText = 'No. Induk (NIS / NIK)';
                inputElement.placeholder = '...';
            }
        }

        // Variabel global sementara untuk menyimpan data asli user yang sedang diedit 
        let currentEditData = {};

        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. OTOMATIS BUKA MODAL TAMBAH USER JIKA ADA ERROR DARI SERVER ---
            @if ($errors->addUserForm->any())
                openAddUserModal();
            @endif

            // --- 2. Event Listener Saat Dropdown Peran Berubah ---
            const addRoleSelect = document.getElementById('peranAdd');
            const addNomorInput = document.getElementById('inputNomorAdd');

            if (addRoleSelect && addNomorInput) {
                addRoleSelect.addEventListener('change', function() {
                    updateNomorField(this.value, 'labelNomorAdd', addNomorInput);
                });
            }

            // --- 3. Event Listener Saat Dropdown Peran Berubah ---
            const modalRole = document.getElementById('modalRole');
            const modalInputNomor = document.getElementById('modalInputNomor');

            if (modalRole && modalInputNomor) {
                modalRole.addEventListener('change', function() {
                    let selectedRole = this.value.toLowerCase();

                    updateNomorField(selectedRole, 'modalLabelNomor', modalInputNomor);

                    // Kembalikan nilai jika kembali ke role aslinya, atau kosongkan jika role baru
                    if (selectedRole === 'siswa') {
                        modalInputNomor.value = currentEditData.nis || '';
                    } else if (selectedRole === 'guru' || selectedRole === 'umum') {
                        modalInputNomor.value = currentEditData.nik || '';
                    } else {
                        modalInputNomor.value = '';
                    }
                });
            }

            // --- 4. Validasi Form Cetak Kartu (Mencegah submit jika tidak ada yang dipilih) ---
            const cetakForm = document.querySelector('#cetakModal form');
            if (cetakForm) {
                cetakForm.addEventListener('submit', function(e) {
                    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');

                    if (checkedBoxes.length === 0) {
                        alert('Pilih minimal satu kartu anggota yang ingin dicetak terlebih dahulu!');
                        e.preventDefault(); // Membatalkan aksi submit & pembukaan tab baru
                    }
                });
            }
        });

        // --- Fungsi Modal Tambah User Baru ---
        function openAddUserModal() {
            const modal = document.getElementById('addUserModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function closeAddUserModal() {
            const modal = document.getElementById('addUserModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        // --- Fungsi Modal Edit User  ---
        function openEditModal(id, nis, nik, name, email, role, jenis_kelamin, alamat, jenjang, kelas) {
            // 1. Simpan data mentah ke objek global
            currentEditData = {
                nis: (nis && nis !== 'null' && nis !== 'undefined') ? nis : '',
                nik: (nik && nik !== 'null' && nik !== 'undefined') ? nik : ''
            };

            // 2. Isi field dasar
            document.getElementById('modalId').value = id;
            document.getElementById('modalName').value = (name && name !== 'null') ? name : '';
            document.getElementById('modalEmail').value = (email && email !== 'null') ? email : '';
            document.getElementById('modalAlamat').value = (alamat && alamat !== 'null') ? alamat : '';

            // 3. Atur Dropdown Role
            const roleSelect = document.getElementById('modalRole');
            let activeRole = role ? role.toLowerCase().trim() : '';
            if (roleSelect) {
                roleSelect.value = activeRole;
            }

            // 4. Masukkan nomor induk yang sesuai ke input form edit
            const modalInputNomor = document.getElementById('modalInputNomor');
            const modalLabelNomor = document.getElementById('modalLabelNomor');

            if (modalInputNomor) {
                updateNomorField(activeRole, 'modalLabelNomor', modalInputNomor);

                if (activeRole === 'siswa') {
                    modalInputNomor.value = currentEditData.nis;
                } else if (activeRole === 'guru' || activeRole === 'umum') {
                    modalInputNomor.value = currentEditData.nik;
                } else {
                    modalInputNomor.value = '';
                }
            }

            // 5. Atur Dropdown Jenis Kelamin
            const jkSelect = document.getElementById('modalJenisKelamin');
            if (jkSelect) {
                jkSelect.value = (jenis_kelamin && jenis_kelamin !== 'null') ? jenis_kelamin : '';
            }

            // 6. Atur Dropdown Jenjang & Input Kelas (Baru)
            const jenjangSelect = document.getElementById('modalJenjang');
            if (jenjangSelect) {
                jenjangSelect.value = (jenjang && jenjang !== 'null' && jenjang !== 'undefined') ? jenjang : '';
            }

            const kelasInput = document.getElementById('modalKelas');
            if (kelasInput) {
                kelasInput.value = (kelas && kelas !== 'null' && kelas !== 'undefined') ? kelas : '';
            }

            // 7. Tampilkan Modal Edit
            const editModal = document.getElementById('editModal');
            if (editModal) {
                editModal.classList.remove('hidden');
            }
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        // --- Fungsi Modal Cetak ---
        function openCetakModal() {
            const modal = document.getElementById('cetakModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        // --- Filter Pencarian di Modal Cetak Kartu ---
        const searchCetakInput = document.getElementById('searchCetakInput');
        if (searchCetakInput) {
            searchCetakInput.addEventListener('keyup', function() {
                let keyword = this.value.toLowerCase();
                let rows = document.querySelectorAll('#cetakModal tbody tr');

                rows.forEach(row => {
                    // Lewatkan baris "Data tidak ditemukan" jika sedang kosong
                    if (row.querySelector('td').getAttribute('colspan')) return;

                    let textRow = row.innerText.toLowerCase();
                    if (textRow.includes(keyword)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        function closeCetakModal() {
            const modal = document.getElementById('cetakModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        // --- Tutup Modal jika Klik di Luar Kotak ---
        window.onclick = function(event) {
            const addUserModal = document.getElementById('addUserModal');
            const editModal = document.getElementById('editModal');
            const cetakModal = document.getElementById('cetakModal');

            if (event.target === addUserModal) {
                closeAddUserModal();
            }
            if (event.target === editModal) {
                closeEditModal();
            }
            if (event.target === cetakModal) {
                closeCetakModal();
            }
        }

        function toggleSelectAll(source) {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = source.checked;
            });
        }
    </script>

    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            let tableManage = $('.table-manage').hide();
        });
    </script> -->
@endsection
