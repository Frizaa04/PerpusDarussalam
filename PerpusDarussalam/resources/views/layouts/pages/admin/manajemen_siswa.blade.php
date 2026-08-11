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

                <!-- Form Pembungkus untuk Fitur Hapus Massal -->
                <form id="deleteUserForm" action="{{ route('member.destroyMultiple') }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <!-- Box Tabel -->
                    <div
                        class="bg-[#b0bec5] p-6 rounded shadow-[0_4px_12px_rgba(0,0,0,0.15)] border border-gray-300/30 table-manage">

                        <!-- Header Tabel & Tombol Kontrol -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
                            <h2 class="text-xl font-bold text-white tracking-wide">Tabel Daftar User</h2>

                            <!-- Area Tombol Kontrol -->
                            <div class="flex items-center gap-2 flex-wrap">

                                <!-- Tombol Hapus Semua Expired-->
                                <button type="button" id="btnHapusExpired" form="hapusExpiredForm"
                                    onclick="confirmDeleteExpired()"
                                    class="hidden bg-red-900 hover:bg-red-950 text-white font-bold px-3 py-1.5 rounded text-sm transition shadow-md">
                                    Hapus Semua Expired
                                </button>

                                <!-- Tombol Konfirmasi Hapus Massal (Pilihan Checkbox) -->
                                <button type="submit" form="deleteUserForm" id="btnConfirmDeleteUser"
                                    onclick="return confirm('Yakin ingin menghapus user yang dipilih?')"
                                    class="hidden bg-red-700 hover:bg-red-800 text-white font-bold px-3 py-1.5 rounded text-sm transition shadow-md">
                                    Konfirmasi Hapus
                                </button>

                                <!-- Tombol Trigger Mode Hapus / Batal -->
                                <button type="button" id="btnToggleDeleteUser" onclick="toggleUserDeleteMode()"
                                    class="bg-[#004d40] hover:bg-[#003d30] text-white font-bold px-3 py-1.5 rounded text-sm transition shadow flex items-center gap-1.5 select-none">
                                    <svg id="trashIconUser" xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 text-white transition-colors duration-200" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span id="btnTextUser">Hapus User</span>
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded">
                            <table class="min-w-full text-left border-collapse border border-white/40">
                                <thead>
                                    <tr class="bg-[#004d40] text-white divide-x divide-white/40">
                                        <th class="p-3 text-xs font-bold tracking-wider">Foto</th>
                                        <th class="p-3 text-xs font-bold tracking-wider">No. Induk (NISN/NIK)</th>
                                        <th class="p-3 text-xs font-bold tracking-wider">Nama</th>
                                        <th class="p-3 text-xs font-bold tracking-wider">Kelamin</th>
                                        <th class="p-3 text-xs font-bold tracking-wider">Peran</th>
                                        <th class="p-3 text-xs font-bold tracking-wider">Email</th>
                                        <th class="p-3 text-xs font-bold tracking-wider">Masa Berlaku</th>
                                        <th class="p-3 text-xs font-bold tracking-wider">Status Kartu</th>
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
                                                {{ $student->nisn ?? ($student->nik ?? '-') }}
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

                                            <!-- Kolom Masa Berlaku -->
                                            <td class="p-3 text-xs font-medium text-white/90">
                                                {{ $student->masa_berlaku_sampai ? \Carbon\Carbon::parse($student->masa_berlaku_sampai)->format('d M Y') : '-' }}
                                            </td>

                                            <!-- Kolom Status Kartu -->
                                            <td class="p-3 text-sm text-center">
                                                @if ($student->masa_berlaku_sampai && \Carbon\Carbon::parse($student->masa_berlaku_sampai)->isFuture())
                                                    <span
                                                        class="bg-emerald-600 text-white px-2 py-1 rounded text-xs font-semibold">Aktif</span>
                                                @else
                                                    <span
                                                        class="bg-red-600 text-white px-2 py-1 rounded text-xs font-semibold">Expired</span>
                                                @endif
                                            </td>

                                            <!-- Tombol Aksi (Edit, Perpanjang, & Checkbox Mode Hapus) -->
                                            <td class="p-3 text-sm text-center">
                                                <!-- Mode Normal: Tombol Edit & Perpanjang -->
                                                <div class="edit-mode-action space-y-1">
                                                    <button type="button"
                                                        onclick="openEditModal(
                                            '{{ $student->id }}', 
                                            '{{ $student->nisn ?? '' }}', 
                                            '{{ $student->nik ?? '' }}', 
                                            '{{ addslashes($student->name) }}', 
                                            '{{ $student->email }}', 
                                            '{{ $student->status ?? '' }}', 
                                            '{{ $student->jenis_kelamin }}', 
                                            '{{ addslashes($student->alamat) }}',
                                            '{{ $student->jenjang }}', 
                                            '{{ $student->kelas }}'
                                        )"
                                                        class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold uppercase tracking-wider hover:bg-[#003d30] transition shadow-sm inline-block w-full">
                                                        Edit Data
                                                    </button>

                                                    <!-- Tombol Perpanjang HANYA MUNCUL jika statusnya EXPIRED -->
                                                    @if (!$student->masa_berlaku_sampai || \Carbon\Carbon::parse($student->masa_berlaku_sampai)->isPast())
                                                        <button type="button"
                                                            onclick="perpanjangKartu('{{ $student->id }}', '{{ addslashes($student->name) }}')"
                                                            class="bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-bold uppercase tracking-wider hover:bg-amber-700 transition shadow-sm inline-block w-full">
                                                            Perpanjang
                                                        </button>
                                                    @endif
                                                </div>

                                                <!-- Mode Hapus: Checkbox (Tersembunyi secara default) -->
                                                <div
                                                    class="delete-mode-action hidden flex items-center justify-center gap-2 py-1">
                                                    <input type="checkbox" name="ids[]" value="{{ $student->id }}"
                                                        class="user-checkbox w-5 h-5 accent-red-600 cursor-pointer rounded border-2 border-white">
                                                    <span class="text-xs font-semibold text-red-200 italic">Pilih</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9"
                                                class="p-5 text-center text-sm font-semibold text-white/80">Data
                                                user tidak ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- Paginasi Selalu Tampil (Meskipun Hanya 1 Halaman) -->
                        <div class="flex justify-center items-center gap-2 mt-6 text-gray-700 font-bold">

                            {{-- Tombol Previous (<) --}}
                            @if ($students->onFirstPage())
                                <span class="px-2.5 py-1 rounded text-sm opacity-50 cursor-not-allowed">&lt;</span>
                            @else
                                <a href="{{ $students->previousPageUrl() }}"
                                    class="px-2.5 py-1 hover:bg-[#004d40]/10 rounded text-sm transition">&lt;</a>
                            @endif

                            {{-- Nomor Halaman --}}
                            @foreach ($students->getUrlRange(1, max($students->lastPage(), 1)) as $page => $url)
                                @if ($page == $students->currentPage())
                                    {{-- Halaman Aktif (Kotak Hijau Tua, Teks Putih) --}}
                                    <span
                                        class="px-2.5 py-1 bg-[#004d40] text-white rounded text-sm shadow">{{ $page }}</span>
                                @else
                                    {{-- Halaman Lain --}}
                                    <a href="{{ $url }}"
                                        class="px-2.5 py-1 hover:bg-[#004d40]/10 rounded text-sm transition">{{ $page }}</a>
                                @endif
                            @endforeach

                            {{-- Tombol Next (>) --}}
                            @if ($students->hasMorePages())
                                <a href="{{ $students->nextPageUrl() }}"
                                    class="px-2.5 py-1 hover:bg-[#004d40]/10 rounded text-sm transition">&gt;</a>
                            @else
                                <span class="px-2.5 py-1 rounded text-sm opacity-50 cursor-not-allowed">&gt;</span>
                            @endif

                        </div>
                    </div>
                </form>

                <form id="hapusExpiredForm" action="{{ route('member.destroyExpired') }}" method="POST"
                    class="hidden">
                    @csrf
                    @method('DELETE')
                </form>

                <div class="mt-4">
                    {{ $students->links('vendor.pagination.custom') }}
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

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Foto</label>
                        <input type="file" name="foto"
                            class="w-full bg-[#b0bec5] text-gray-800 text-xs font-medium px-2 py-1.5 rounded outline-none file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-600 file:text-white">
                    </div>

                    <!-- Dropdown Status (Siswa/Guru/Umum) -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">Status / Kategori</label>
                        <select name="role" id="peranAdd" required
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                            <option value="">Pilih Status...</option>
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
                        <label id="labelNomorAdd" class="block text-sm font-semibold mb-1">No. Induk (NISN / NIK)</label>
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

                    <!-- Jenjang -->
                    <div>
                        <label class="block text-sm font-semibold mb-1">Jenjang</label>
                        <select name="jenjang"
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                            <option value="">Pilih Jenjang...</option>
                            <option value="MTS">MTS</option>
                            <option value="MA">MA</option>
                        </select>
                    </div>

                    <!-- Kelas -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-sm font-semibold">Kelas</label>
                            <div class="space-x-1.5">
                                <button type="button" onclick="hapusKelasAktif()" id="btnHapusKelas"
                                    class="text-xs underline text-red-300 hover:text-white hidden">
                                    Hapus
                                </button>
                                <button type="button" onclick="editKelasAktif()" id="btnEditKelas"
                                    class="text-xs underline text-yellow-200 hover:text-white hidden">
                                    Edit
                                </button>
                                <button type="button" onclick="toggleInputKelas()" id="btnToggleKelas"
                                    class="text-xs underline text-emerald-200 hover:text-white">
                                    + Kelas Baru
                                </button>
                            </div>
                        </div>

                        <!-- Dropdown Kelas Eksisting (Ambil dari data kelas yang sudah ada, misal $allKelas) -->
                        <select name="kelas" id="selectKelas" onchange="cekPilihKelas(this)"
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white">
                            <option value="">Pilih Kelas...</option>
                            @foreach ($allKelas as $kls)
                                <option value="{{ $kls }}" data-nama="{{ $kls }}">{{ $kls }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Input Text untuk Kelas Baru / Edit Kelas -->
                        <input type="text" name="kelas_baru" id="inputKelasBaru"
                            placeholder="Contoh: 10A, 12 IPA 1..."
                            class="w-full bg-[#b0bec5] text-gray-800 text-sm font-medium px-3 py-1.5 rounded outline-none focus:ring-2 focus:ring-white hidden">

                        <!-- Input hidden untuk penanda mode edit/hapus kelas -->
                        <input type="hidden" name="edit_kelas_lama" id="editKelasLama">
                        <input type="hidden" name="delete_kelas" id="deleteKelasVal">
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
            <form id="editForm" onsubmit="event.preventDefault(); submitEditForm();" class="space-y-3">
                @csrf
                @method('PUT')

                <!-- Input hidden untuk ID siswa -->
                <input type="hidden" id="editUserId" name="id">

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
                            (NISN / NIK)</label>
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
                        <select id="modalRole" name="status"
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
                            <option value="MTS">MTs</option>
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
                    <button type="button" onclick="submitEditForm()"
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
                                        {{ $student->nisn ?? ($student->nik ?? '-') }}
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

    <script>
        // --- Fungsi Bantuan untuk Mengubah Label & Placeholder Nomor Induk ---
        function updateNomorField(roleValue, labelElementId, inputElement) {
            const labelNomor = document.getElementById(labelElementId);
            if (!labelNomor || !inputElement) return;

            let peran = roleValue ? roleValue.toLowerCase() : '';

            if (peran === 'siswa') {
                labelNomor.innerText = 'NISN (Nomor Induk Siswa)';
                inputElement.placeholder = 'Masukkan NISN...';
            } else if (peran === 'guru' || peran === 'umum') {
                labelNomor.innerText = 'NIK (Nomor Induk Kependudukan)';
                inputElement.placeholder = 'Masukkan NIK...';
            } else {
                labelNomor.innerText = 'No. Induk (NISN / NIK)';
                inputElement.placeholder = '...';
            }
        }

        // Variabel global aman dari duplikasi
        if (typeof window.isOpeningModal === 'undefined') {
            window.isOpeningModal = false;
        }
        if (typeof window.currentEditData === 'undefined') {
            window.currentEditData = {};
        }

        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. OTOMATIS BUKA MODAL TAMBAH USER JIKA ADA ERROR DARI SERVER ---
            @if ($errors->addUserForm->any())
                openAddUserModal();
            @endif

            // --- 2. Event Listener Saat Dropdown Peran (Add) Berubah ---
            const addRoleSelect = document.getElementById('peranAdd');
            const addNomorInput = document.getElementById('inputNomorAdd');

            if (addRoleSelect && addNomorInput) {
                addRoleSelect.addEventListener('change', function() {
                    updateNomorField(this.value, 'labelNomorAdd', addNomorInput);
                });
            }

            // --- 3. Event Listener Saat Dropdown Peran (Edit) Berubah ---
            const modalRole = document.getElementById('modalRole');
            const modalInputNomor = document.getElementById('modalInputNomor');

            if (modalRole && modalInputNomor) {
                modalRole.addEventListener('change', function() {
                    if (window.isOpeningModal) return;

                    let selectedRole = this.value.toLowerCase();
                    updateNomorField(selectedRole, 'modalLabelNomor', modalInputNomor);

                    if (selectedRole === 'siswa') {
                        modalInputNomor.value = window.currentEditData.nisn || '';
                    } else if (selectedRole === 'guru' || selectedRole === 'umum') {
                        modalInputNomor.value = window.currentEditData.nik || '';
                    } else {
                        modalInputNomor.value = '';
                    }
                });
            }

            // --- 4. Validasi Form Cetak Kartu ---
            const cetakForm = document.querySelector('#cetakModal form');
            if (cetakForm) {
                cetakForm.addEventListener('submit', function(e) {
                    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');

                    if (checkedBoxes.length === 0) {
                        alert('Pilih minimal satu kartu anggota yang ingin dicetak terlebih dahulu!');
                        e.preventDefault();
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

        // Munculkan tombol "Edit" dan "Hapus" hanya jika kelas di dropdown dipilih
        function cekPilihKelas(select) {
            const btnEdit = document.getElementById('btnEditKelas');
            const btnHapus = document.getElementById('btnHapusKelas');
            if (select.value) {
                btnEdit.classList.remove('hidden');
                btnHapus.classList.remove('hidden');
            } else {
                btnEdit.classList.add('hidden');
                btnHapus.classList.add('hidden');
            }
        }

        // Toggle untuk mode Tambah Kelas Baru
        function toggleInputKelas() {
            const selectBox = document.getElementById('selectKelas');
            const inputBox = document.getElementById('inputKelasBaru');
            const editKelasLama = document.getElementById('editKelasLama');
            const deleteKelasVal = document.getElementById('deleteKelasVal');
            const btnToggle = document.getElementById('btnToggleKelas');
            const btnEdit = document.getElementById('btnEditKelas');
            const btnHapus = document.getElementById('btnHapusKelas');

            editKelasLama.value = "";
            deleteKelasVal.value = "";

            if (inputBox.classList.contains('hidden')) {
                inputBox.classList.remove('hidden');
                selectBox.classList.add('hidden');
                selectBox.value = "";
                btnEdit.classList.add('hidden');
                btnHapus.classList.add('hidden');
                btnToggle.textContent = "Pilih Kelas Eksisting";
                inputBox.placeholder = "Ketik kelas baru...";
            } else {
                inputBox.classList.add('hidden');
                selectBox.classList.remove('hidden');
                inputBox.value = "";
                btnToggle.textContent = "+ Kelas Baru";
            }
        }

        // Toggle untuk mode Edit Kelas yang sedang dipilih
        function editKelasAktif() {
            const selectBox = document.getElementById('selectKelas');
            const selectedOption = selectBox.options[selectBox.selectedIndex];
            const inputBox = document.getElementById('inputKelasBaru');
            const editKelasLama = document.getElementById('editKelasLama');
            const btnToggle = document.getElementById('btnToggleKelas');
            const btnEdit = document.getElementById('btnEditKelas');
            const btnHapus = document.getElementById('btnHapusKelas');

            if (selectBox.value) {
                editKelasLama.value = selectBox.value;
                inputBox.value = selectedOption.getAttribute('data-nama');

                selectBox.classList.add('hidden');
                inputBox.classList.remove('hidden');
                btnEdit.classList.add('hidden');
                btnHapus.classList.add('hidden');
                btnToggle.textContent = "Batal Edit";
                inputBox.placeholder = "Edit nama kelas...";
            }
        }

        // Fungsi untuk menghapus kelas terpilih
        function hapusKelasAktif() {
            const selectBox = document.getElementById('selectKelas');
            const selectedOption = selectBox.options[selectBox.selectedIndex];
            const deleteKelasVal = document.getElementById('deleteKelasVal');

            if (selectBox.value) {
                const namaKelas = selectedOption.text;
                if (confirm(`Apakah Anda yakin ingin menghapus kelas "${namaKelas}" dari daftar?`)) {
                    deleteKelasVal.value = selectBox.value;
                    selectBox.form.submit();
                }
            }
        }

        // --- Fungsi Modal Edit User ---
        function openEditModal(id, nisn, nik, name, email, role, jenis_kelamin, alamat, jenjang, kelas) {
            window.isOpeningModal = true;

            // 1. Simpan data mentah ke objek global
            window.currentEditData = {
                nisn: (nisn && nisn !== 'null' && nisn !== 'undefined') ? nisn : '',
                nik: (nik && nik !== 'null' && nik !== 'undefined') ? nik : ''
            };

            // 2. Isi field dasar
            document.getElementById('editUserId').value = id;
            document.getElementById('modalName').value = (name && name !== 'null') ? name : '';
            document.getElementById('modalEmail').value = (email && email !== 'null') ? email : '';
            document.getElementById('modalAlamat').value = (alamat && alamat !== 'null') ? alamat : '';

            // Mengatur URL action form secara dinamis membawa ID user
            const editForm = document.getElementById('editForm');
            if (editForm) {
                editForm.action = "/manajemen-siswa/update/" + id;
            }

            // 3. Atur Dropdown Role secara case-insensitive
            const roleSelect = document.getElementById('modalRole');
            let activeRole = role ? role.toLowerCase().trim() : '';
            if (roleSelect) {
                let foundMatch = false;
                for (let option of roleSelect.options) {
                    if (option.value.toLowerCase() === activeRole) {
                        roleSelect.value = option.value;
                        foundMatch = true;
                        break;
                    }
                }
                if (!foundMatch) roleSelect.value = '';
            }

            // 4. Masukkan nomor induk yang sesuai ke input form edit
            const modalInputNomor = document.getElementById('modalInputNomor');
            if (modalInputNomor) {
                updateNomorField(activeRole, 'modalLabelNomor', modalInputNomor);

                if (activeRole === 'siswa') {
                    modalInputNomor.value = window.currentEditData.nisn;
                } else if (activeRole === 'guru' || activeRole === 'umum') {
                    modalInputNomor.value = window.currentEditData.nik;
                } else {
                    modalInputNomor.value = '';
                }
            }

            // 5. Atur Dropdown Jenis Kelamin
            const jkSelect = document.getElementById('modalJenisKelamin');
            if (jkSelect) {
                let activeJk = jenis_kelamin ? jenis_kelamin.toLowerCase().trim() : '';
                let jkFound = false;
                for (let option of jkSelect.options) {
                    if (option.value.toLowerCase() === activeJk) {
                        jkSelect.value = option.value;
                        jkFound = true;
                        break;
                    }
                }
                if (!jkFound) jkSelect.value = '';
            }

            // 6. Atur Dropdown Jenjang & Input Kelas
            const jenjangSelect = document.getElementById('modalJenjang');
            if (jenjangSelect) {
                let activeJenjang = (jenjang && jenjang !== 'null' && jenjang !== 'undefined') ? jenjang.toLowerCase()
                    .trim() : '';
                let jenjangFound = false;
                for (let option of jenjangSelect.options) {
                    if (option.value.toLowerCase() === activeJenjang) {
                        jenjangSelect.value = option.value;
                        jenjangFound = true;
                        break;
                    }
                }
                if (!jenjangFound) jenjangSelect.value = '';
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

            setTimeout(() => {
                window.isOpeningModal = false;
            }, 100);
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        function submitEditForm() {
            const form = document.getElementById('editForm');
            const id = document.getElementById('editUserId').value;
            const formData = new FormData(form);

            fetch("/manajemen-siswa/update/" + id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                            '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        // Tutup modal edit jika ada fungsi penutupnya
                        closeEditModal();
                        // Otomatis muat ulang halaman untuk melihat perubahan data terbaru
                        location.reload();
                    } else {
                        return response.text().then(text => {
                            console.error('Respon Error Server:', text);
                            alert('Gagal memperbarui data. Periksa kembali inputan Anda.');
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan pada jaringan atau server.');
                });
        }

        function perpanjangKartu(id, name) {
            if (!confirm(`Perpanjang masa berlaku kartu untuk ${name} hingga 30 Juni tahun depan?`)) {
                return;
            }

            fetch(`/manajemen-siswa/perpanjang/${id}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                            '',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async response => {
                    const text = await response.text();
                    console.log('STATUS:', response.status, 'BODY:', text);
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Gagal memperpanjang kartu. Status: ' + response.status);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan pada jaringan atau server.');
                });
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

        function toggleUserDeleteMode() {
            const btnConfirmDelete = document.getElementById('btnConfirmDeleteUser');
            const btnHapusExpired = document.getElementById('btnHapusExpired');
            const btnToggleDelete = document.getElementById('btnToggleDeleteUser');
            const btnText = document.getElementById('btnTextUser');
            const trashIcon = document.getElementById('trashIconUser');

            const editActions = document.querySelectorAll('.edit-mode-action');
            const deleteActions = document.querySelectorAll('.delete-mode-action');

            const isHidden = btnConfirmDelete.classList.contains('hidden');

            if (isHidden) {
                btnConfirmDelete.classList.remove('hidden');
                btnHapusExpired.classList.remove('hidden');

                btnToggleDelete.classList.remove('bg-[#004d40]', 'hover:bg-[#003d30]');
                btnToggleDelete.classList.add('bg-gray-600', 'hover:bg-gray-700');
                btnText.textContent = 'Batal';
                trashIcon.classList.add('rotate-45');

                editActions.forEach(el => el.classList.add('hidden'));
                deleteActions.forEach(el => el.classList.remove('hidden'));
            } else {
                btnConfirmDelete.classList.add('hidden');
                btnHapusExpired.classList.add('hidden');

                btnToggleDelete.classList.remove('bg-gray-600', 'hover:bg-gray-700');
                btnToggleDelete.classList.add('bg-[#004d40]', 'hover:bg-[#003d30]');
                btnText.textContent = 'Hapus User';
                trashIcon.classList.remove('rotate-45');

                editActions.forEach(el => el.classList.remove('hidden'));
                deleteActions.forEach(el => el.classList.add('hidden'));
            }
        }

        function confirmDeleteExpired() {
            if (confirm('Yakin ingin menghapus SEMUA user yang berstatus expired (kecuali admin)?')) {
                document.getElementById('hapusExpiredForm').submit();
            }
        }
    </script>
@endsection
