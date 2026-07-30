@extends('layouts.pages.admin.provider.app')

@section('content')
<div class="flex min-h-screen bg-[#f4f7f6] text-gray-800">
    
    <!-- Pemanggilan Sidebar -->
    @include('layouts.pages.admin.provider.sidebar')
    
    <main class="flex-1 flex flex-col overflow-x-hidden">

        <!-- Area Konten Utama -->
        <div class="p-8 space-y-6">
            
            <!-- HEADER & FILTER DROPDOWN (Gaya mirip referensi) -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <h1 class="text-xl font-extrabold text-[#004d40] tracking-wide">Dashboard</h1>
                
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <select class="bg-gray-50 border border-gray-200 text-sm rounded-lg px-3 py-1.5 outline-none font-medium text-gray-600">
                            <option>Semua Kategori</option>
                            <option>Buku Umum</option>
                            <option>Novel & Fiksi</option>
                        </select>
                    </div>
                    <div class="relative">
                        <select class="bg-gray-50 border border-gray-200 text-sm rounded-lg px-3 py-1.5 outline-none font-medium text-gray-600">
                            <option>Bulan Ini</option>
                            <option>Tahun Ini</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- STATISTIK CARD (Grid 5 Kolom ke Samping seperti Referensi) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                
                <!-- Card 1 -->
                <div class="bg-[#b0bec5] text-white p-5 rounded-xl shadow-[0_4px_10px_rgba(0,0,0,0.1)] border border-white/20">
                    <h3 class="text-xs font-bold text-white/90 tracking-wider">Pengunjung Hari Ini</h3>
                    <p class="text-3xl font-extrabold mt-2">{{ $todayVisitors ?? 128 }}</p>
                    <span class="text-xs text-emerald-200 font-semibold mt-1 inline-block">▲ +4.2% Dari kemarin</span>
                </div>
                
                <!-- Card 2 -->
                <div class="bg-[#b0bec5] text-white p-5 rounded-xl shadow-[0_4px_10px_rgba(0,0,0,0.1)] border border-white/20">
                    <h3 class="text-xs font-bold text-white/90 tracking-wider">Peminjaman Buku</h3>
                    <p class="text-3xl font-extrabold mt-2">{{ $todayBorrowings ?? 45 }}</p>
                    <span class="text-xs text-emerald-200 font-semibold mt-1 inline-block">▲ +1.2% Minggu ini</span>
                </div>

                <!-- Card 3 -->
                <div class="bg-[#b0bec5] text-white p-5 rounded-xl shadow-[0_4px_10px_rgba(0,0,0,0.1)] border border-white/20">
                    <h3 class="text-xs font-bold text-white/90 tracking-wider">Pengembalian Buku</h3>
                    <p class="text-3xl font-extrabold mt-2">{{ $todayReturns ?? 32 }}</p>
                    <span class="text-xs text-emerald-200 font-semibold mt-1 inline-block">▲ +2.5% Hari ini</span>
                </div>

                <!-- Card 4 (Dummy Tambahan Penyeimbang Layout 5 Kolom) -->
                <div class="bg-[#b0bec5] text-white p-5 rounded-xl shadow-[0_4px_10px_rgba(0,0,0,0.1)] border border-white/20">
                    <h3 class="text-xs font-bold text-white/90 tracking-wider">Total Anggota</h3>
                    <p class="text-3xl font-extrabold mt-2">{{ $totalMembers }}</p>
                    <span class="text-xs text-emerald-200 font-semibold mt-1 inline-block">Terdaftar di sistem</span>
                </div>

                <!-- Card 5: Total Buku Per Item -->
                <div class="bg-[#b0bec5] text-white p-5 rounded-xl shadow-[0_4px_10px_rgba(0,0,0,0.1)] border border-white/20">
                    <h3 class="text-xs font-bold text-white/90 tracking-wider">Total Item Buku</h3>
                    <p class="text-3xl font-extrabold mt-2">{{ $totalBookItems }}</p>
                    <span class="text-xs text-white/80 font-semibold mt-1 inline-block">Seluruh eksemplar</span>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Kolom Kiri: Grafik Garis Tren Sirkulasi (2 Span) -->
                <div class="bg-[#b0bec5] p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.1)] border border-white/20 lg:col-span-2 flex flex-col justify-between text-white">
                    <!-- Header Grafik & Info Ringkas -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h2 class="text-lg font-bold text-white tracking-wide">Statistik Sirkulasi & Pengunjung Mingguan</h2>
                            <p class="text-xs text-white/80 mt-0.5">Tren harian (Senin - Minggu) pada minggu aktif.</p>
                        </div>
                        
                        <!-- Kotak Info Total Minggu Ini -->
                        <div class="bg-white text-gray-800 p-2.5 rounded-lg shadow-md text-xs border border-gray-100 flex flex-col gap-1 min-w-[150px]">
                            <span class="font-bold text-[10px] text-gray-400 uppercase tracking-wider">Total Minggu Ini</span>
                            <div class="flex justify-between items-center">
                                <span class="flex items-center gap-1.5 font-semibold text-[#004d40]"><span class="w-2 h-2 rounded-full bg-[#004d40]"></span> Peminjaman</span>
                                <span class="font-extrabold">{{ array_sum($chartPeminjaman ?? []) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="flex items-center gap-1.5 font-semibold text-emerald-600"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Pengunjung</span>
                                <span class="font-extrabold">{{ array_sum($chartPengunjung ?? []) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Area Grafik SVG Dinamis -->
                    <div class="relative h-44 w-full py-2">
                        <!-- Garis Panduan Sumbu Y -->
                        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none opacity-20">
                            <div class="border-b border-white w-full"></div>
                            <div class="border-b border-white w-full"></div>
                            <div class="border-b border-white w-full"></div>
                        </div>

                        @php
                            $peminjaman = $chartPeminjaman ?? [0,0,0,0,0,0,0];
                            $pengunjung = $chartPengunjung ?? [0,0,0,0,0,0,0];
                            
                            // Cari nilai maksimum untuk skala tinggi grafik (minimal 1 agar tidak division by zero)
                            $maxVal = max(array_merge($peminjaman, $pengunjung, [10]));
                            
                            // Fungsi helper untuk mengubah angka menjadi koordinat Y SVG (tinggi max 120px)
                            $getY = fn($val) => 130 - ($val / $maxVal) * 100;
                            
                            // Hitung titik koordinat X untuk 7 hari (lebar SVG 500px)
                            $pointsPeminjaman = [];
                            $pointsPengunjung = [];
                            $stepX = 500 / 6; // 6 interval untuk 7 titik (Senin - Minggu)
                            
                            foreach($peminjaman as $index => $val) {
                                $x = $index * $stepX;
                                $y = $getY($val);
                                $pointsPeminjaman[] = "$x,$y";
                            }
                            
                            foreach($pengunjung as $index => $val) {
                                $x = $index * $stepX;
                                $y = $getY($val);
                                $pointsPengunjung[] = "$x,$y";
                            }
                            
                            $pathPeminjaman = "M " . implode(" L ", $pointsPeminjaman);
                            $pathPengunjung = "M " . implode(" L ", $pointsPengunjung);
                            
                            // Path untuk area gradasi di bawah garis peminjaman
                            $pathAreaPeminjaman = $pathPeminjaman . " L 500,150 L 0,150 Z";
                        @endphp

                        <!-- SVG Grafik Line Chart -->
                        <svg viewBox="0 0 500 150" class="w-full h-full overflow-visible">
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

                            <!-- Garis Tren Pengunjung (Dashed Line) -->
                            <path d="{{ $pathPengunjung }}" fill="none" stroke="#34d399" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="4 4" />

                            <!-- Titik-titik Indikator Peminjaman -->
                            @foreach($peminjaman as $index => $val)
                                <circle cx="{{ $index * $stepX }}" cy="{{ $getY($val) }}" r="4" fill="#ffffff" stroke="#004d40" stroke-width="2" />
                            @endforeach
                        </svg>
                    </div>

                    <!-- Footer Sumbu X (Label Hari Senin - Minggu) -->
                    <div class="flex justify-between text-[11px] text-white/80 pt-3 border-t border-white/20 font-medium">
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
                        <h2 class="text-lg font-bold text-white mb-1 tracking-wide">Pemberitahuan Sistem</h2>
                        <p class="text-xs text-white/80 mb-4">Informasi penting operasional perpustakaan.</p>
                    </div>
                    <div class="space-y-3">
                        <div class="bg-white/10 p-3 rounded-lg border border-white/15">
                            <p class="text-xs font-bold">Pemeliharaan Sistem</p>
                            <p class="text-[11px] text-white/80 mt-0.5">Backup database otomatis dijadwalkan pukul 00:00.</p>
                        </div>
                        <div class="bg-white/10 p-3 rounded-lg border border-white/15">
                            <p class="text-xs font-bold">Buku Terlambat</p>
                            <p class="text-[11px] text-white/80 mt-0.5">Terdapat 3 anggota yang melewati batas tempo hari ini.</p>
                        </div>
                    </div>
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