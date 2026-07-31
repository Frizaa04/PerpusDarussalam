@extends('layouts.pages.users.provider.app')

@section('content')
<div class="bg-[#004d40] min-h-[calc(100vh-140px)] py-12 px-4 flex flex-col items-center">
    
    <!-- Judul Halaman -->
    <h1 class="text-white text-2xl md:text-3xl font-bold tracking-wider uppercase mb-8 text-center">
        AREA ANGGOTA PERPUSTAKAAN
    </h1>

    <div class="w-full max-w-4xl space-y-8">
        
        <!-- Card Profile / Profil Anggota -->
        <div class="bg-[#b2c8c6] rounded-2xl p-6 md:p-8 shadow-xl flex items-center gap-6 md:gap-8">
            <!-- Avatar Placeholder / Foto -->
<!-- Avatar Placeholder / Foto -->
            <div class="w-24 h-24 md:w-28 md:h-28 rounded-full bg-gray-300 flex-shrink-0 border-2 border-white/50 overflow-hidden">
                @php
                    $fotoPath = $user->foto ?? $user->avatar ?? $user->photo ?? null;
                    
                    // Cek file ada, tidak kosong, dan BUKAN PDF / dokumen
                    $isValidPhoto = !empty($fotoPath) && !\Illuminate\Support\Str::endsWith(strtolower($fotoPath), ['.pdf', '.doc', '.docx']);
                @endphp

                @if ($isValidPhoto)
                    @php
                        $url = \Illuminate\Support\Str::startsWith($fotoPath, 'storage/') 
                            ? asset($fotoPath) 
                            : asset('storage/' . $fotoPath);
                    @endphp
                    
                    <img src="{{ $url }}" 
                        alt="Foto Profile" 
                        class="w-full h-full object-cover" 
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                    
                    <div class="w-full h-full bg-gray-400 hidden items-center justify-center text-xl font-bold text-white">
                        {{ strtoupper(substr($user->name ?? $user->nama ?? 'U', 0, 2)) }}
                    </div>
                @else
                    <!-- Jika nilai di DB adalah NULL, default.pdf, atau file non-gambar -->
                    <div class="w-full h-full bg-gray-400 flex items-center justify-center text-xl font-bold text-white">
                        {{ strtoupper(substr($user->name ?? $user->nama ?? 'U', 0, 2)) }}
                    </div>
                @endif
            </div>

            <!-- Detail Info User -->
            <div class="text-[#003d30] space-y-1">
                <h2 class="text-2xl md:text-3xl font-bold mb-2">Selamat Datang!</h2>
                <div class="text-sm md:text-base font-semibold grid grid-cols-[80px_10px_1fr] md:grid-cols-[100px_10px_1fr] gap-1">
                    <span>Nama</span>
                    <span>:</span>
                    <span class="font-bold">{{ $user->name ?? $user->nama ?? '-' }}</span>

                    <span>NIS</span>
                    <span>:</span>
                    <span class="font-bold">{{ $user->nis ?? $user->nik ?? '-' }}</span>
                </div>
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
                            <tr class="hover:bg-[#003d30]/10 transition text-emerald-950 font-medium border-b border-emerald-800/30">
                                <!-- Judul Buku (diambil dari relasi bookItem -> book) -->
                                <td class="py-2.5 px-4 text-left border-r border-emerald-800/30">
                                    {{ $pinjam->bookItem->book->title ?? $pinjam->bookItem->book->judul ?? $pinjam->bookItem->code ?? '-' }}
                                </td>

                                <!-- Tanggal Pinjam -->
                                <td class="py-2.5 px-4 border-r border-emerald-800/30">
                                    {{ $pinjam->tanggal_pinjam ? \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d/m/Y') : '-' }}
                                </td>

                                <!-- Batas Pengembalian / Tanggal Jatuh Tempo -->
                                <td class="py-2.5 px-4 border-r border-emerald-800/30">
                                    {{ $pinjam->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($pinjam->tanggal_jatuh_tempo)->format('d/m/Y') : '-' }}
                                </td>

                                <!-- Status Badge -->
                                <td class="py-2.5 px-4 border-r border-emerald-800/30">
                                    @php
                                        $status = strtolower($pinjam->status);
                                    @endphp

                                    @if(in_array($status, ['selesai', 'returned', 'kembali', 'dikembalikan']))
                                        <span class="bg-blue-600 text-white text-[10px] font-bold px-2.5 py-0.5 rounded">Selesai</span>
                                    @elseif(in_array($status, ['borrowed', 'pinjam', 'dipinjam', 'peminjaman']))
                                        <span class="bg-yellow-500 text-white text-[10px] font-bold px-2.5 py-0.5 rounded">Pinjaman</span>
                                    @else
                                        <span class="bg-red-600 text-white text-[10px] font-bold px-2.5 py-0.5 rounded">Terlambat</span>
                                    @endif
                                </td>

                                <!-- Tanggal Kembali Realisasi -->
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
        </div>

    </div>
</div>
@endsection