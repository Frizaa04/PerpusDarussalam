@extends('layouts.pages.users.provider.app')

@section('content')
    <div class="bg-[#004d40] min-h-[calc(100vh-140px)] py-12 px-4 flex flex-col items-center">

        <!-- Judul Halaman -->
        <h1 class="text-white text-2xl md:text-3xl font-bold tracking-wider uppercase mb-8 text-center">
            AREA ANGGOTA PERPUSTAKAAN
        </h1>

        <div class="w-full max-w-4xl space-y-8">

            <!-- Card Profile / Profil Anggota -->
            <div class="bg-[#b2c8c6] rounded-2xl p-6 md:p-8 shadow-xl flex items-center gap-6 md:gap-8 overflow-hidden">
                <!-- Avatar / Foto -->
                <div class="w-24 h-24 md:w-28 md:h-28 rounded-full bg-gray-300 flex-shrink-0 border-2 border-white/50 overflow-hidden">
                    @php
                        $fotoPath = $user->foto ?? ($user->avatar ?? ($user->photo ?? null));
                        $isValidPhoto = !empty($fotoPath) && !\Illuminate\Support\Str::endsWith(strtolower($fotoPath), ['.pdf', '.doc', '.docx']);
                    @endphp

                    @if ($isValidPhoto)
                        @php
                            $url = \Illuminate\Support\Str::startsWith($fotoPath, 'storage/') ? asset($fotoPath) : asset('storage/' . $fotoPath);
                        @endphp
                        <img src="{{ $url }}" alt="Foto Profile" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                        <div class="w-full h-full bg-gray-400 hidden items-center justify-center text-xl font-bold text-white">
                            {{ strtoupper(substr($user->name ?? ($user->nama ?? 'U'), 0, 2)) }}
                        </div>
                    @else
                        <div class="w-full h-full bg-gray-400 flex items-center justify-center text-xl font-bold text-white">
                            {{ strtoupper(substr($user->name ?? ($user->nama ?? 'U'), 0, 2)) }}
                        </div>
                    @endif
                </div>

                <!-- Detail Info User -->
                <div class="text-[#003d30] space-y-1 flex-1 relative pr-0 md:pr-44">
                    <h2 class="text-2xl md:text-3xl font-bold mb-2">Selamat Datang!</h2>
                    
                    <div class="text-sm md:text-base font-semibold grid grid-cols-[80px_10px_1fr] md:grid-cols-[100px_10px_1fr] gap-1">
                        <span>Nama</span>
                        <span>:</span>
                        <span class="font-bold">{{ $user->name ?? ($user->nama ?? '-') }}</span>

                        <span>
                            @if (isset($user->nisn) && $user->nisn)
                                NISN
                            @elseif(isset($user->nik) && $user->nik)
                                NIK
                            @else
                                Nomor Identitas
                            @endif
                        </span>
                        <span>:</span>
                        <span class="font-bold">{{ $user->nisn ?? ($user->nik ?? '-') }}</span>
                    </div>

                    <!-- Tombol Ubah Password -->
                    <div class="mt-4 md:mt-0 md:absolute md:top-1/2 md:-translate-y-1/2 md:right-0">
                        <button onclick="document.getElementById('modalResetPassword').classList.remove('hidden')" 
                                class="px-4 py-2 bg-[#003d30] hover:bg-[#002920] text-white text-sm font-bold rounded-lg shadow transition whitespace-nowrap flex items-center gap-1.5 border border-white/10">
                            Ubah Password
                        </button>
                    </div>
                </div>
            </div> 

            <!-- 3. MODAL RESET PASSWORD (Taruh di paling bawah terpisah dari element lain) -->
            <div id="modalResetPassword" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 @if(!$errors->has('current_password') && !$errors->has('password')) hidden @endif p-4">
                <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 text-slate-800 relative">
                    <h3 class="text-lg font-bold border-b pb-2 mb-4 text-[#003d30]">Ubah Password</h3>
                    
                    <form action="{{ route('password.update.custom') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="block text-xs font-bold mb-1 text-slate-600">Password Lama</label>
                            <input type="password" name="current_password" required 
                                class="w-full border @error('current_password') border-red-500 @else border-gray-300 @enderror rounded p-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003d30]">
                            @error('current_password')
                                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="block text-xs font-bold mb-1 text-slate-600">Password Baru</label>
                            <input type="password" name="password" required 
                                class="w-full border @error('password') border-red-500 @else border-gray-300 @enderror rounded p-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003d30]">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-bold mb-1 text-slate-600">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" required 
                                class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003d30]">
                        </div>

                        <div class="flex justify-end gap-2 text-sm font-bold">
                            <button type="button" onclick="document.getElementById('modalResetPassword').classList.add('hidden')" 
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-500 hover:bg-gray-100 transition">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-[#003d30] text-white rounded-lg hover:bg-[#002920] transition">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Card Data Peminjaman -->
            <div class="bg-[#b2c8c6] rounded-2xl p-6 md:p-8 shadow-xl">
                <h3 class="text-[#003d30] text-xl font-bold mb-4">Data Peminjaman</h3>

                <div class="overflow-x-auto rounded-lg border border-[#004d40]">
                    <table class="w-full text-left text-xs md:text-sm text-white border-collapse">
                        <!-- Header Tabel -->
                        <thead>
                            <tr class="bg-[#003d30] text-center border-b border-[#004d40]">
                                <th class="py-2.5 px-4 font-semibold border-r border-emerald-800/50">Judul Buku</th>
                                <th class="py-2.5 px-4 font-semibold border-r border-emerald-800/50">Tanggal Pinjam</th>
                                <th class="py-2.5 px-4 font-semibold border-r border-emerald-800/50">Batas Pengembalian</th>
                                <th class="py-2.5 px-4 font-semibold border-r border-emerald-800/50">Status</th>
                                <th class="py-2.5 px-4 font-semibold">Tanggal Kembali</th>
                            </tr>
                        </thead>

                        <!-- Body Tabel Dynamic Data -->
                        <tbody class="text-center divide-y divide-emerald-800/30">
                            @forelse($peminjamans as $pinjam)
                                <tr
                                    class="hover:bg-[#003d30]/10 transition text-emerald-950 font-medium border-b border-emerald-800/30">
                                    <!-- Judul Buku -->
                                    <td class="py-2.5 px-4 text-left border-r border-emerald-800/30">
                                        {{ $pinjam->bookItem->book->title ?? ($pinjam->bookItem->book->judul ?? ($pinjam->bookItem->code ?? '-')) }}
                                    </td>

                                    <!-- Tanggal Pinjam -->
                                    <td class="py-2.5 px-4 border-r border-emerald-800/30">
                                        {{ $pinjam->tanggal_pinjam ? \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d/m/Y') : '-' }}
                                    </td>

                                    <!-- Batas Pengembalian -->
                                    <td class="py-2.5 px-4 border-r border-emerald-800/30">
                                        {{ $pinjam->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($pinjam->tanggal_jatuh_tempo)->format('d/m/Y') : '-' }}
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="py-2.5 px-4 border-r border-emerald-800/30">
                                        @php
                                            $status = strtolower($pinjam->status);
                                        @endphp

                                        @if (in_array($status, ['selesai', 'returned', 'kembali', 'dikembalikan']))
                                            <span
                                                class="bg-blue-600 text-white text-[10px] font-bold px-2.5 py-0.5 rounded">Selesai</span>
                                        @elseif(in_array($status, ['borrowed', 'pinjam', 'dipinjam', 'peminjaman']))
                                            <span
                                                class="bg-yellow-500 text-white text-[10px] font-bold px-2.5 py-0.5 rounded">Pinjaman</span>
                                        @else
                                            <span
                                                class="bg-red-600 text-white text-[10px] font-bold px-2.5 py-0.5 rounded">Terlambat</span>
                                        @endif
                                    </td>

                                    <!-- Tanggal Kembali -->
                                    <td class="py-2.5 px-4">
                                        {{ $pinjam->tanggal_kembali ? \Carbon\Carbon::parse($pinjam->tanggal_kembali)->format('d/m/Y') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <!-- Pesan jika tidak ada data peminjaman -->
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-emerald-950 font-semibold italic">
                                        Belum ada data peminjaman buku.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $peminjamans->links('vendor.pagination.custom') }}
                </div>

            </div>

        </div>
    </div>

<script>
    window.addEventListener('click', function(event) {
        let modal = document.getElementById('modalResetPassword');
        if (event.target == modal) {
            modal.classList.add('hidden');
        }
    });
</script>
@endsection
