@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Pemanggilan Sidebar -->
    @include('layouts.sidebar')

    <main class="flex-1 flex flex-col">
        <!-- Isi Data -->
        <div class="p-8 space-y-6">
            
            <!-- Baris Pencarian, Tombol Filter, dan Tombol Print -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    <!-- Form Cari Data User -->
                    <form action="{{ route('member.index') }}" method="GET" class="flex items-center border-2 border-[#004d40] rounded overflow-hidden bg-white w-full sm:w-80">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari Nama / No Induk" class="w-full px-4 py-2 text-gray-700 outline-none font-medium placeholder-gray-400 text-sm">
                        @if(request('role'))
                            <input type="hidden" name="role" value="{{ request('role') }}">
                        @endif
                        <button type="submit" class="bg-[#004d40] text-white px-4 py-2 flex items-center justify-center hover:bg-[#003d30] transition">
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
                </div>

                <!-- Tombol Print (Membuka Modal Cetak Kartu Anggota) -->
                <button type="button" onclick="openCetakModal()" class="bg-[#004d40] text-white p-2.5 rounded hover:bg-[#003d30] transition shadow-md flex items-center justify-center">
                    <span class="material-icons">print</span>
                </button>
            </div>

            <!-- Box Tabel -->
            <div class="bg-[#b0bec5] p-6 rounded shadow-[0_4px_12px_rgba(0,0,0,0.15)] border border-gray-300/30">
                <h2 class="text-xl font-bold text-white mb-4 tracking-wide">Tabel Daftar User</h2>
                
                <div class="overflow-x-auto rounded">
                    <table class="min-w-full text-left border-collapse border border-white/40">
                        <thead>
                            <tr class="bg-[#004d40] text-white divide-x divide-white/40">
                                <th class="p-3 text-xs font-bold tracking-wider">Foto</th>
                                <th class="p-3 text-xs font-bold tracking-wider">No. Induk (NIS/NIK/NIP)</th>
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
                                        @if($student->foto)
                                            <img src="{{ asset('storage/' . $student->foto) }}" alt="Foto" class="w-10 h-10 rounded-full object-cover mx-auto border border-white">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gray-400 flex items-center justify-center text-xs font-bold mx-auto text-white">
                                                {{ strtoupper(substr($student->name, 0, 2)) }}
                                            </div>
                                        @endif
                                    </td>
                                    <!-- Kolom No Induk -->
                                    <td class="p-3 text-sm font-bold text-white/90">
                                        {{ $student->nis ?? $student->nip ?? $student->nik ?? '-' }}
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
                                    <td class="p-3 text-sm font-medium text-white/90 truncate max-w-xs">{{ $student->alamat ?? '-' }}</td>
                                    
                                    <!-- Tombol Aksi Edit -->
                                    <td class="p-3 text-sm text-center">
                                        <button type="button" 
                                                onclick="openEditModal(
                                                    '{{ $student->id }}', 
                                                    '{{ $student->nis }}', 
                                                    '{{ $student->nik }}', 
                                                    '{{ $student->nip }}', 
                                                    '{{ $student->name }}', 
                                                    '{{ $student->email }}', 
                                                    '{{ $student->role }}', 
                                                    '{{ $student->jenis_kelamin }}', 
                                                    '{{ $student->alamat }}'
                                                )"
                                                class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold uppercase tracking-wider hover:bg-[#003d30] transition shadow-sm inline-block">
                                            Edit Data
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-5 text-center text-sm font-semibold text-white/80">Data user tidak ditemukan.</td>
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

<!-- POP-UP MODAL EDIT DATA USER (Disesuaikan dengan field lengkap) -->
<div id="editModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
    <div class="bg-[#00695c] text-white rounded-lg shadow-2xl w-full max-w-lg p-6 relative border border-emerald-400/30 max-h-[90vh] overflow-y-auto">
        <!-- Tombol Close -->
        <button type="button" onclick="closeEditModal()" class="absolute top-3 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
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
                <div>
                    <label class="block text-xs font-semibold mb-1 text-emerald-100">NIS</label>
                    <input type="text" id="modalNis" name="nis" class="w-full bg-[#b0bec5] text-gray-800 font-medium px-3 py-1.5 rounded outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-emerald-100">NIK</label>
                    <input type="text" id="modalNik" name="nik" class="w-full bg-[#b0bec5] text-gray-800 font-medium px-3 py-1.5 rounded outline-none text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold mb-1 text-emerald-100">NIP</label>
                    <input type="text" id="modalNip" name="nip" class="w-full bg-[#b0bec5] text-gray-800 font-medium px-3 py-1.5 rounded outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-emerald-100">Nama Lengkap</label>
                    <input type="text" id="modalName" name="name" required class="w-full bg-[#b0bec5] text-gray-800 font-medium px-3 py-1.5 rounded outline-none text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold mb-1 text-emerald-100">Email</label>
                    <input type="email" id="modalEmail" name="email" required class="w-full bg-[#b0bec5] text-gray-800 font-medium px-3 py-1.5 rounded outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-emerald-100">Peran (Role)</label>
                    <select id="modalRole" name="role" class="w-full bg-[#b0bec5] text-gray-800 font-medium px-3 py-1.5 rounded outline-none text-sm">
                        <option value="siswa">Siswa</option>
                        <option value="guru">Guru</option>
                        <option value="umum">Umum</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1 text-emerald-100">Jenis Kelamin</label>
                <select id="modalJenisKelamin" name="jenis_kelamin" class="w-full bg-[#b0bec5] text-gray-800 font-medium px-3 py-1.5 rounded outline-none text-sm">
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1 text-emerald-100">Alamat</label>
                <textarea id="modalAlamat" name="alamat" rows="2" class="w-full bg-[#b0bec5] text-gray-800 font-medium px-3 py-1.5 rounded outline-none text-sm"></textarea>
            </div>

            <!-- Tombol Konfirmasi -->
            <div class="pt-3 text-center">
                <button type="submit" class="bg-white text-[#004d40] hover:bg-emerald-50 px-6 py-2 rounded font-bold transition shadow-md w-full">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- POP-UP MODAL CETAK KARTU ANGGOTA -->
<div id="cetakModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
    <div class="bg-[#004d40] text-white rounded-lg shadow-2xl w-full max-w-4xl p-6 relative border border-emerald-400/30">
        <!-- Tombol Close -->
        <button type="button" onclick="closeCetakModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
            &#10005;
        </button>

        <h3 class="text-xl font-bold mb-4 tracking-wide">Cetak Kartu Anggota</h3>

        <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
            <div class="w-full sm:w-72">
                <div class="flex items-center border border-white rounded overflow-hidden bg-white">
                    <input type="text" placeholder="Cari Data Siswa" class="w-full px-3 py-1.5 text-gray-700 outline-none text-sm placeholder-gray-400">
                    <button type="button" class="bg-white text-[#004d40] px-3 py-1.5 flex items-center justify-center border-l border-gray-200">
                        <span class="material-icons text-lg">search</span>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-4 text-sm font-semibold">
                <button type="button" class="flex items-center gap-1.5 hover:text-emerald-200 transition">
                    <span>[ Download PDF ]</span>
                    <span class="material-icons border border-white p-0.5 rounded text-sm">download</span>
                </button>
                <button type="button" class="flex items-center gap-1.5 hover:text-emerald-200 transition">
                    <span>[ Cetak Kartu ]</span>
                    <span class="material-icons border border-white p-0.5 rounded text-sm">print</span>
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
                        <th class="p-2.5 text-sm font-bold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/40">
                    @forelse($students as $student)
                        <tr class="divide-x divide-white/40 hover:bg-white/10 transition-colors">
                            <td class="p-2.5 text-sm font-medium">{{ $student->nis ?? $student->nip ?? $student->nik ?? '-' }}</td>
                            <td class="p-2.5 text-sm font-medium">{{ $student->name }}</td>
                            <td class="p-2.5 text-sm font-medium">{{ ucfirst($student->role ?? 'Siswa') }}</td>
                            <td class="p-2.5 text-sm text-center"></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-sm font-medium text-white/80">Data tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SCRIPT UNTUK CONTROL MODAL -->
<script>
    // Fungsi JavaScript Diperbarui untuk Menangkap Parameter Lengkap Edit Modal
    function openEditModal(id, nis, nik, nip, name, email, role, jenis_kelamin, alamat) {
        document.getElementById('modalId').value = id;
        document.getElementById('modalNis').value = nis !== 'null' ? nis : '';
        document.getElementById('modalNik').value = nik !== 'null' ? nik : '';
        document.getElementById('modalNip').value = nip !== 'null' ? nip : '';
        document.getElementById('modalName').value = name !== 'null' ? name : '';
        document.getElementById('modalEmail').value = email !== 'null' ? email : '';
        document.getElementById('modalAlamat').value = alamat !== 'null' ? alamat : '';

        // Mengatur Dropdown Role
        const roleSelect = document.getElementById('modalRole');
        for (let i = 0; i < roleSelect.options.length; i++) {
            if (roleSelect.options[i].value.toLowerCase() === role.toLowerCase()) {
                roleSelect.selectedIndex = i;
                break;
            }
        }

        // Mengatur Dropdown Jenis Kelamin
        const jkSelect = document.getElementById('modalJenisKelamin');
        jkSelect.value = jenis_kelamin !== 'null' ? jenis_kelamin : '';

        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function openCetakModal() {
        document.getElementById('cetakModal').classList.remove('hidden');
    }

    function closeCetakModal() {
        document.getElementById('cetakModal').classList.add('hidden');
    }

    window.onclick = function(event) {
        const editModal = document.getElementById('editModal');
        const cetakModal = document.getElementById('cetakModal');
        
        if (event.target === editModal) {
            closeEditModal();
        }
        if (event.target === cetakModal) {
            closeCetakModal();
        }
    }
</script>
@endsection