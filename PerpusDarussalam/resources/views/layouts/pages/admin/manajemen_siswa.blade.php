@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6]">
    
    <!-- Pemanggilan Sidebar -->
    @include('layouts.sidebar')

    <main class="flex-1 flex flex-col">
        <!-- Header Atas -->
        <header class="bg-white border-b border-gray-200 px-8 flex justify-end items-center shadow-sm h-20">
            <div class="flex items-center h-full">
                <img src="{{ asset('image/covers/darussalam.png') }}" alt="Logo Darussalam" class="h-full py-1 object-contain">
            </div>
        </header>

        <!-- Isi Data -->
        <div class="p-8 space-y-6">
            
            <!-- Baris Pencarian, Tombol User Baru, dan Tombol Print -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <!-- Form Cari Data Siswa -->
                    <form action="{{ route('member.index') }}" method="GET" class="flex items-center border-2 border-[#004d40] rounded overflow-hidden bg-white w-full sm:w-80">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari Data Siswa" class="w-full px-4 py-2 text-gray-700 outline-none font-medium placeholder-gray-400">
                        <button type="submit" class="bg-[#004d40] text-white px-4 py-2 flex items-center justify-center hover:bg-[#003d30] transition">
                            <span class="material-icons">search</span>
                        </button>
                    </form>

                    <!-- Tombol + User Baru -->
                    <button type="button" class="border-2 border-[#004d40] text-[#004d40] font-bold px-4 py-2 rounded bg-white hover:bg-emerald-50 transition text-sm">
                        + User Baru
                    </button>
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
                                <th class="p-3 text-sm font-bold tracking-wider">No Identitas</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Nama</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Kelamin</th>
                                <th class="p-3 text-sm font-bold tracking-wider">Peran</th>
                                <th class="p-3 text-sm font-bold tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-white divide-y divide-white/40">
                            @forelse($students as $student)
                                <tr class="divide-x divide-white/40 hover:bg-white/10 transition-colors">
                                    <td class="p-3 text-sm font-bold text-white/90">
                                        {{ $student->nis ?? $student->nip ?? $student->nik ?? '-' }}
                                    </td>
                                    <td class="p-3 text-sm font-bold text-white/90">{{ $student->name }}</td>
                                    <td class="p-3 text-sm font-bold text-white/90">{{ $student->jenis_kelamin ?? '-' }}</td>
                                    <td class="p-3 text-sm font-bold text-white/90">{{ ucfirst($student->role ?? 'Siswa') }}</td>
                                    <td class="p-3 text-sm text-center">
                                        <!-- Tombol Pemicu Modal Pop-up Edit -->
                                        <button type="button" 
                                                onclick="openEditModal('{{ $student->id }}', '{{ $student->nis ?? $student->nip ?? $student->nik ?? '' }}', '{{ $student->name }}', '{{ $student->role ?? 'Siswa' }}')"
                                                class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold uppercase tracking-wider hover:bg-[#003d30] transition shadow-sm inline-block">
                                                Edit Data
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-5 text-center text-sm font-semibold text-white/80">Data siswa tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Bawah (1 2 3 >) -->
                <div class="flex justify-center items-center gap-3 mt-6 text-white font-bold text-sm">
                    <button class="bg-white/30 text-white w-7 h-7 rounded flex items-center justify-center hover:bg-white/50 transition">1</button>
                    <button class="hover:text-emerald-900 transition">2</button>
                    <button class="hover:text-emerald-900 transition">3</button>
                    <button class="hover:text-emerald-900 transition flex items-center">
                        <span class="material-icons text-base">chevron_right</span>
                    </button>
                </div>

            </div>
        </div>
    </main>
</div>

<!-- POP-UP MODAL EDIT DATA USER -->
<div id="editModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
    <div class="bg-[#00695c] text-white rounded-lg shadow-2xl w-full max-w-sm p-6 relative border border-emerald-400/30">
        <!-- Tombol Close -->
        <button type="button" onclick="closeEditModal()" class="absolute top-3 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
            &#10005;
        </button>

        <!-- Judul Modal -->
        <h3 class="text-xl font-bold mb-5 tracking-wide">Edit Data User</h3>

        <!-- Form Edit -->
        <form action="{{ route('member.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- ID Tersembunyi untuk Acuan Update -->
            <input type="hidden" id="modalId" name="id">

            <div>
                <label class="block text-sm font-semibold mb-1 text-emerald-100">NIS</label>
                <input type="text" id="modalNis" name="nis" required class="w-full bg-[#b0bec5] text-gray-800 font-medium px-3 py-2 rounded outline-none focus:ring-2 focus:ring-white border border-white/20">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1 text-emerald-100">Nama</label>
                <input type="text" id="modalName" name="name" required class="w-full bg-[#b0bec5] text-gray-800 font-medium px-3 py-2 rounded outline-none focus:ring-2 focus:ring-white border border-white/20">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1 text-emerald-100">Peran</label>
                <select id="modalRole" name="role" class="w-full bg-[#b0bec5] text-gray-800 font-medium px-3 py-2 rounded outline-none focus:ring-2 focus:ring-white border border-white/20">
                    <option value="Siswa">Siswa</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>

            <!-- Tombol Konfirmasi -->
            <div class="pt-3 text-center">
                <button type="submit" class="bg-white text-[#004d40] hover:bg-emerald-50 px-6 py-2 rounded font-bold transition shadow-md w-full">
                    Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- POP-UP MODAL CETAK KARTU ANGGOTA (Sesuai Foto 3) -->
<div id="cetakModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
    <div class="bg-[#004d40] text-white rounded-lg shadow-2xl w-full max-w-4xl p-6 relative border border-emerald-400/30">
        <!-- Tombol Close -->
        <button type="button" onclick="closeCetakModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 text-xl font-bold transition">
            &#10005;
        </button>

        <!-- Judul Modal -->
        <h3 class="text-xl font-bold mb-4 tracking-wide">Cetak Kartu Anggota</h3>

        <!-- Baris Pencarian & Akses Download/Cetak -->
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

        <!-- Tabel Pratinjau Kartu -->
        <div class="overflow-x-auto rounded border border-white/40 max-h-96">
            <table class="min-w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#003d30] border-b border-white/40 divide-x divide-white/40">
                        <th class="p-2.5 text-sm font-bold">NIS</th>
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
    // Functions untuk Edit Modal
    function openEditModal(id, nis, name, role) {
        document.getElementById('modalId').value = id;
        document.getElementById('modalNis').value = nis;
        document.getElementById('modalName').value = name;
        
        const roleSelect = document.getElementById('modalRole');
        for (let i = 0; i < roleSelect.options.length; i++) {
            if (roleSelect.options[i].value.toLowerCase() === role.toLowerCase()) {
                roleSelect.selectedIndex = i;
                break;
            }
        }

        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Functions untuk Cetak Modal
    function openCetakModal() {
        document.getElementById('cetakModal').classList.remove('hidden');
    }

    function closeCetakModal() {
        document.getElementById('cetakModal').classList.add('hidden');
    }

    // Close modal ketika klik area background luar
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