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
            <div class="max-w-md">
                <form action="{{ route('member.index') }}" method="GET" class="flex items-center border-2 border-[#004d40] rounded overflow-hidden bg-white">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari Data Siswa" class="w-full px-4 py-2 text-gray-700 outline-none font-medium placeholder-gray-400">
                    <button type="submit" class="bg-[#004d40] text-white px-4 py-2 flex items-center justify-center hover:bg-[#003d30] transition">
                        <span class="material-icons">search</span>
                    </button>
                </form>
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
                                    <td class="p-3 text-sm font-bold text-white/90">{{ ucfirst($student->role ?? 'Siswa') }}</td>
                                    <td class="p-3 text-sm text-center">
                                        <!-- Tombol Pemicu Modal Pop-up -->
                                        <button type="button" 
                                                onclick="openEditModal('{{ $student->id }}', '{{ $student->nis ?? $student->nip ?? $student->nik ?? '' }}', '{{ $student->name }}', '{{ $student->role ?? 'Siswa' }}')"
                                                class="bg-[#004d40] text-white px-3 py-1.5 rounded text-xs font-bold uppercase tracking-wider hover:bg-[#003d30] transition shadow-sm inline-block">
                                                Edit Data
                                            </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-5 text-center text-sm font-semibold text-white/80">Data siswa tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- POP-UP MODAL EDIT DATA USER -->
<div id="editModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
    <div class="bg-[#00695c] text-white rounded-lg shadow-2xl w-full max-w-sm p-6 relative border border-emerald-400/30">
        <!-- Tombol Close (X) -->
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
                <!-- Atribut readonly sudah dihapus agar NIS bisa diubah -->
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

<!-- SCRIPT UNTUK CONTROL MODAL -->
<script>
    function openEditModal(id, nis, name, role) {
        document.getElementById('modalId').value = id;
        document.getElementById('modalNis').value = nis;
        document.getElementById('modalName').value = name;
        
        // Pilih opsi role secara case-insensitive
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

    window.onclick = function(event) {
        const modal = document.getElementById('editModal');
        if (event.target === modal) {
            closeEditModal();
        }
    }
</script>
@endsection