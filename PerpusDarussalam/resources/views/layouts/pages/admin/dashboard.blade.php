@extends('layouts.pages.admin.provider.app')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6] text-gray-800">
    
    <!-- Pemanggilan Sidebar -->
    @include('layouts.pages.admin.provider.sidebar')
    
    <main class="flex-1 flex flex-col overflow-x-hidden">

        <!-- Area Konten Utama -->
        <div class="p-8 space-y-6">

            <!-- STATISTIK CARD (Grid 5 Kolom ke Samping seperti Referensi) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                
                <!-- Card 1 -->
                <div class="bg-[#b0bec5] text-white p-5 rounded-xl shadow-[0_4px_10px_rgba(0,0,0,0.1)] border border-white/20">
                    <h3 class="text-xs font-bold text-white/90 tracking-wider">Pengunjung Hari Ini</h3>
                    <p class="text-3xl font-extrabold mt-2">{{ $todayVisitors ?? 128 }}</p>
                </div>
                
                <!-- Card 2 -->
                <div class="bg-[#b0bec5] text-white p-5 rounded-xl shadow-[0_4px_10px_rgba(0,0,0,0.1)] border border-white/20">
                    <h3 class="text-xs font-bold text-white/90 tracking-wider">Peminjaman Buku</h3>
                    <p class="text-3xl font-extrabold mt-2">{{ $todayBorrowings ?? 45 }}</p>
                </div>

                <!-- Card 3 -->
                <div class="bg-[#b0bec5] text-white p-5 rounded-xl shadow-[0_4px_10px_rgba(0,0,0,0.1)] border border-white/20">
                    <h3 class="text-xs font-bold text-white/90 tracking-wider">Pengembalian Buku</h3>
                    <p class="text-3xl font-extrabold mt-2">{{ $todayReturns ?? 32 }}</p>
                </div>

                <!-- Card 4  -->
                <div class="bg-[#b0bec5] text-white p-5 rounded-xl shadow-[0_4px_10px_rgba(0,0,0,0.1)] border border-white/20">
                    <h3 class="text-xs font-bold text-white/90 tracking-wider">Total Anggota</h3>
                    <p class="text-3xl font-extrabold mt-2">{{ $totalMembers }}</p>
                    <span class="text-xs text-white/80 font-semibold mt-1 inline-block">Terdaftar di sistem</span>
                </div>

                <!-- Card 5: Total Buku Per Item -->
                <div class="bg-[#b0bec5] text-white p-5 rounded-xl shadow-[0_4px_10px_rgba(0,0,0,0.1)] border border-white/20">
                    <h3 class="text-xs font-bold text-white/90 tracking-wider">Total Item Buku</h3>
                    <p class="text-3xl font-extrabold mt-2">{{ $totalBookItems }}</p>
                    <span class="text-xs text-white/80 font-semibold mt-1 inline-block">Seluruh eksemplar</span>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-[#b0bec5] p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.1)] border border-white/20 lg:col-span-2 flex flex-col justify-between text-white">
                    <!-- Header Grafik & Info Ringkas -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h2 class="text-lg font-bold text-white tracking-wide">Statistik Peminjaman Mingguan</h2>
                            <p class="text-xs text-white/80 mt-0.5">Tren harian (Senin - hari ini) pada minggu aktif.</p>
                        </div>
                        
                        <!-- Kotak Info Total Minggu Ini -->
                        <div class="bg-white text-gray-800 p-2.5 rounded-lg shadow-md text-xs border border-gray-100 flex flex-col gap-1 min-w-[140px]">
                            <span class="font-bold text-[10px] text-gray-400 uppercase tracking-wider">Total Periode Ini</span>
                            <div class="flex justify-between items-center">
                                <span class="flex items-center gap-1.5 font-semibold text-[#004d40]"><span class="w-2 h-2 rounded-full bg-[#004d40]"></span> Peminjaman</span>
                                <span class="font-extrabold">{{ array_sum($chartPeminjaman ?? []) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Area Grafik & Sumbu Y (Angka Penanda Kiri) -->
                    <div class="flex items-center gap-2">
                        <!-- Label Angka Sumbu Y di Kiri -->
                        <div class="flex flex-col justify-between text-[10px] text-white/70 h-44 py-2 text-right pr-1 select-none min-w-[20px]">
                            @php
                                $peminjaman = $chartPeminjaman ?? [0];
                                $maxVal = max(array_merge($peminjaman, [4])); // Minimal skala 4 agar rapi
                                $midVal = round($maxVal / 2);
                            @endphp
                            <span>{{ $maxVal }}</span>
                            <span>{{ $midVal }}</span>
                            <span>0</span>
                        </div>

                        <!-- Container SVG Grafik -->
                        <div class="relative h-44 w-full py-2">
                            <!-- Garis Panduan Sumbu Y -->
                            <div class="absolute inset-0 flex flex-col justify-between pointer-events-none opacity-20">
                                <div class="border-b border-white w-full"></div>
                                <div class="border-b border-white w-full"></div>
                                <div class="border-b border-white w-full"></div>
                            </div>

                            @php
                                $getY = fn($val) => 130 - ($val / $maxVal) * 100;
                                
                                $pointsPeminjaman = [];
                                $totalPoints = count($peminjaman);
                                // Mencegah pembagian dengan zero jika titik hanya 1
                                $stepX = $totalPoints > 1 ? 500 / ($totalPoints - 1) : 0; 
                                
                                foreach($peminjaman as $index => $val) {
                                    // Beri offset sedikit agar titik pertama & terakhir tidak menempel di dinding kiri/kanan SVG
                                    $x = $totalPoints > 1 ? $index * $stepX : 250;
                                    $y = $getY($val);
                                    $pointsPeminjaman[] = "$x,$y";
                                }
                                
                                $pathPeminjaman = count($pointsPeminjaman) > 1 ? "M " . implode(" L ", $pointsPeminjaman) : "M 0,130";
                                $pathAreaPeminjaman = $pathPeminjaman . " L 500,150 L 0,150 Z";
                            @endphp

                            <!-- SVG Grafik Line Chart -->
                            <svg viewBox="0 0 500 150" preserveAspectRatio="none" class="w-full h-full overflow-visible">
                                <defs>
                                    <linearGradient id="gradPeminjaman" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" stop-color="#004d40" stop-opacity="0.5" />
                                        <stop offset="100%" stop-color="#004d40" stop-opacity="0.0" />
                                    </linearGradient>
                                </defs>

                                <!-- Area Gradasi Peminjaman -->
                                <path d="{{ $pathAreaPeminjaman }}" fill="url(#gradPeminjaman)" />

                                <!-- Garis Tren Peminjaman -->
                                <path d="{{ $pathPeminjaman }}" fill="none" stroke="#004d40" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

                                <!-- Titik-titik Indikator Peminjaman -->
                                @foreach($peminjaman as $index => $val)
                                    @php $cx = $totalPoints > 1 ? $index * $stepX : 250; @endphp
                                    <circle cx="{{ $cx }}" cy="{{ $getY($val) }}" r="4.5" fill="#ffffff" stroke="#004d40" stroke-width="2.5" />
                                @endforeach
                            </svg>
                        </div>
                    </div>

                    <!-- Footer Sumbu X (Label Hari) -->
                    <div class="flex justify-between text-[11px] text-white/80 pt-3 pl-7 border-t border-white/20 font-medium">
                        @if(!empty($chartLabels))
                            @foreach($chartLabels as $label)
                                <span>{{ $label }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Kolom Kanan: Ringkasan Cepat / Status (1 Span) -->
                <div class="bg-[#b0bec5] p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.1)] border border-white/20 text-white flex flex-col justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-white mb-1 tracking-wide">Kelola Teks Berjalan (Marquee)</h2>
                        <p class="text-xs text-white/80 mb-4">Input informasi penting yang akan tampil bergerak di beranda user.</p>
                    </div>

                    <form action="{{ route('admin.announcement.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <textarea name="content" rows="3" placeholder="Contoh: Perpustakaan tutup lebih awal hari Jumat pukul 11.30..." class="w-full p-2.5 text-xs text-gray-800 rounded-lg border border-white/15 focus:outline-none focus:ring-2 focus:ring-[#004d40]">{{ \App\Models\Announcement::where('is_active', true)->latest()->value('content') }}</textarea>
                        </div>
                        <button type="submit" class="bg-[#003d30] hover:bg-[#004d40] text-white text-xs font-semibold px-4 py-2 rounded-lg transition shadow">
                            Perbarui Teks Berjalan
                        </button>
                    </form>
                </div>
            </div>

            <!-- BOX TABEL AKTIVITAS TERBARU (Sesuai kode asli Anda yang disempurnakan tampilannya) -->
            <div class="bg-[#b0bec5] p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.1)] border border-white/20">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-white tracking-wide">Aktivitas Terbaru</h2>
                    <span class="text-xs bg-[#004d40] text-white px-3 py-1 rounded-full font-semibold">Real-time log</span>
                </div>
                
                <div class="overflow-x-auto rounded-lg">
                    <table class="min-w-full text-left border-collapse border border-white/30">
                        <thead>
                            <tr class="bg-[#004d40] text-white divide-x divide-white/30">
                                <th class="p-3 text-xs font-bold tracking-wider">Waktu</th>
                                <th class="p-3 text-xs font-bold tracking-wider">Tindakan</th>
                                <th class="p-3 text-xs font-bold tracking-wider">Detail Buku</th>
                                <th class="p-3 text-xs font-bold tracking-wider">User</th>
                            </tr>
                        </thead>
                        <tbody class="text-white divide-y divide-white/30">
                            @forelse($recentActivities ?? [] as $activity)
                                <tr class="divide-x divide-white/30 hover:bg-white/10 transition-colors">
                                    <td class="p-3 text-xs font-bold text-white/90">{{ $activity['waktu'] }}</td>
                                    <td class="p-3 text-xs text-white/90">{{ $activity['tindakan'] }}</td>
                                    <td class="p-3 text-xs text-white/90">{{ $activity['detail_buku'] }}</td>
                                    <td class="p-3 text-xs font-bold text-white/90">{{ $activity['user'] }}</td>
                                </tr>
                            @empty
                                <!-- Data Dummy untuk preview jika controller belum mengirim data -->
                                <tr class="divide-x divide-white/30 hover:bg-white/10 transition-colors">
                                    <td class="p-3 text-xs font-bold text-white/90">10:42 AM</td>
                                    <td class="p-3 text-xs text-white/90">Peminjaman Buku</td>
                                    <td class="p-3 text-xs text-white/90">Laskar Pelangi (INV-001)</td>
                                    <td class="p-3 text-xs font-bold text-white/90">Ahmad (NIS: 1029)</td>
                                </tr>
                                <tr class="divide-x divide-white/30 hover:bg-white/10 transition-colors">
                                    <td class="p-3 text-xs font-bold text-white/90">09.15 AM</td>
                                    <td class="p-3 text-xs text-white/90">Pengembalian Buku</td>
                                    <td class="p-3 text-xs text-white/90">Bumi Manusia (INV-045)</td>
                                    <td class="p-3 text-xs font-bold text-white/90">Siti (NIS: 1055)</td>
                                </tr>
                                <tr class="divide-x divide-white/30 hover:bg-white/10 transition-colors">
                                    <td class="p-3 text-xs font-bold text-white/90">08:30 AM</td>
                                    <td class="p-3 text-xs text-white/90">Peminjaman Buku</td>
                                    <td class="p-3 text-xs text-white/90">Fisika Dasar (INV-089)</td>
                                    <td class="p-3 text-xs font-bold text-white/90">Budi (NIS: 1102)</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection