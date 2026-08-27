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
                    <span class="text-xs text-white/80 font-semibold mt-1 inline-block">Yang Terpinjam Hari Ini</span>
                </div>

                <!-- Card 3 -->
                <div class="bg-[#b0bec5] text-white p-5 rounded-xl shadow-[0_4px_10px_rgba(0,0,0,0.1)] border border-white/20">
                    <h3 class="text-xs font-bold text-white/90 tracking-wider">Pengembalian Buku</h3>
                    <p class="text-3xl font-extrabold mt-2">{{ $todayReturns ?? 32 }}</p>
                    <span class="text-xs text-white/80 font-semibold mt-1 inline-block">Pengembalian Buku Hari Ini</span>
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
                            <p class="text-xs text-white/80 mt-0.5">Tren harian (7 hari terakhir).</p>
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
                        <div class="relative h-44 w-full py-2 px-4">
                            <div 
                                id="chartTooltip"
                                class="hidden absolute z-50 bg-white text-gray-800 rounded-lg shadow-xl border border-gray-200 p-3 text-xs w-64 pointer-events-auto"
                                style="transform: translateX(0);"
                            >
                            </div>
                            <!-- Garis Panduan Sumbu Y -->
                            <div class="absolute inset-x-4 inset-y-2 flex flex-col justify-between pointer-events-none opacity-20">
                                <div class="border-b border-white w-full"></div>
                                <div class="border-b border-white w-full"></div>
                                <div class="border-b border-white w-full"></div>
                            </div>

                            @php
                                // Sesuaikan skala tinggi SVG (viewBox 0 sampai 140) agar titik 0 tepat berada di garis paling bawah (Y=140)
                                $getY = fn($val) => 140 - ($val / $maxVal) * 140;
                                
                                $pointsPeminjaman = [];
                                $totalPoints = count($peminjaman);
                                
                                $svgWidth = 500;
                                $paddingX = 30;
                                $usableWidth = $svgWidth - ($paddingX * 2);
                                
                                $stepX = $totalPoints > 1 ? $usableWidth / ($totalPoints - 1) : 0; 
                                
                                foreach($peminjaman as $index => $val) {
                                    $x = $totalPoints > 1 ? $paddingX + ($index * $stepX) : $svgWidth / 2;
                                    $y = $getY($val);
                                    $pointsPeminjaman[] = "$x,$y";
                                }
                                
                                $pathPeminjaman = count($pointsPeminjaman) > 1 ? "M " . implode(" L ", $pointsPeminjaman) : "M 0,140";
                                $pathAreaPeminjaman = $pathPeminjaman . " L " . $svgWidth . ",140 L 0,140 Z";
                            @endphp

                            <!-- SVG Grafik Line Chart -->
                            <svg viewBox="0 0 500 140" preserveAspectRatio="none" class="w-full h-full overflow-visible">
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
                                    @php
                                        $cx = $totalPoints > 1
                                            ? $paddingX + ($index * $stepX)
                                            : $svgWidth / 2;

                                        $date = $chartDates[$index] ?? null;
                                        $details = $date ? ($chartDetails[$date] ?? []) : [];
                                    @endphp

                                    <circle 
                                        cx="{{ $cx }}" 
                                        cy="{{ $getY($val) }}" 
                                        r="4.5" 
                                        fill="#ffffff" 
                                        stroke="#004d40" 
                                        stroke-width="2.5"
                                        class="chart-point cursor-pointer"
                                        data-date="{{ $date }}"
                                        data-count="{{ $val }}"
                                        data-details='@json($details)'
                                    />
                                @endforeach
                            </svg>
                        </div>
                    </div>

                    <!-- Footer Sumbu X (Label Hari) -->
                    <div class="flex justify-between text-[11px] text-white/80 pt-3 pl-7 pr-3 border-t border-white/20 font-medium">
                        @if(!empty($chartLabels))
                            @foreach($chartLabels as $label)
                                <span class="text-center flex-1">{{ $label }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Kolom Kanan: Ringkasan Cepat / Status (1 Span) -->
                <div class="bg-[#b0bec5] p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.1)] border border-white/20 text-white flex flex-col justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-white mb-1 tracking-wide">Kelola Teks Berjalan</h2>
                        <p class="text-xs text-white/80 mb-4">Buat pengumuman baru atau pilih dari riwayat teks yang pernah dibuat.</p>
                    </div>

                    <!-- Form Input Pengumuman Baru -->
                    <form action="{{ route('admin.announcement.store') }}" method="POST" class="space-y-3 mb-6">
                        @csrf
                        <div>
                            <textarea name="content" rows="2" placeholder="Tulis pengumuman baru di sini..." class="w-full p-2.5 text-xs text-gray-800 rounded-lg border border-white/15 focus:outline-none focus:ring-2 focus:ring-[#004d40]" required></textarea>
                        </div>
                        <button type="submit" class="bg-[#003d30] hover:bg-[#004d40] text-white text-xs font-semibold px-4 py-2 rounded-lg transition shadow">
                            Simpan & Aktifkan
                        </button>
                    </form>

                    <!-- Daftar Riwayat Pengumuman -->
                    <div class="border-t border-white/20 pt-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider mb-2 text-white/90">Riwayat Pilihan Pengumuman</h3>
                        <div class="max-h-40 overflow-y-auto space-y-2 pr-1">
                            @php
                                $announcements = \App\Models\Announcement::latest()->get();
                            @endphp

                            @forelse($announcements as $item)
                                <div class="bg-white/10 p-2.5 rounded-lg border border-white/15 flex items-center justify-between gap-3 text-xs">
                                    <div class="truncate flex items-center gap-2">
                                        @if($item->is_active)
                                            <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0" title="Sedang Aktif"></span>
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-gray-400 shrink-0" title="Arsip"></span>
                                        @endif
                                        <span class="truncate {{ $item->is_active ? 'font-bold text-white' : 'text-white/80' }}">{{ $item->content }}</span>
                                    </div>

                                    <div class="flex items-center gap-1.5 shrink-0">
                                        @if(!$item->is_active)
                                            <form action="{{ route('admin.announcement.activate', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-2.5 py-1 rounded text-[10px] font-semibold transition">
                                                    Gunakan
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[10px] bg-emerald-800 text-emerald-100 px-2 py-0.5 rounded font-medium">Aktif</span>
                                        @endif

                                        <!-- Tombol Hapus -->
                                        <form action="{{ route('admin.announcement.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengumuman ini secara permanen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-2 py-1 rounded text-[10px] font-semibold transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-[11px] text-white/70 italic">Belum ada riwayat pengumuman.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

                <!-- 1. TABEL AKTIVITAS TERBARU (Real-time log) -->
                <div class="bg-[#b0bec5] p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.1)] border border-white/20 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-bold text-white tracking-wide">Aktivitas Peminjaman terbaru</h2>
                            <span class="text-xs bg-[#004d40] text-white px-3 py-1 rounded-full font-semibold">Real-time log</span>
                        </div>
                        
                        <div class="overflow-x-auto rounded-lg">
                            <table class="min-w-full text-left border-collapse border border-white/30">
                                <thead>
                                    <tr class="bg-[#004d40] text-white divide-x divide-white/30">
                                        <th class="p-3 text-xs font-bold tracking-wider">Tanggal & Waktu</th>
                                        <th class="p-3 text-xs font-bold tracking-wider">Tindakan</th>
                                        <th class="p-3 text-xs font-bold tracking-wider">Detail Buku</th>
                                        <th class="p-3 text-xs font-bold tracking-wider">User</th>
                                    </tr>
                                </thead>
                                <tbody class="text-white divide-y divide-white/30">
                                    @forelse($recentActivities ?? [] as $activity)
                                        <tr class="divide-x divide-white/30 hover:bg-white/10 transition-colors">
                                            <td class="p-3 text-xs font-bold text-white/90">
                                                {{ $activity['tanggal'] }} <span class="text-white/90 font-normal">({{ $activity['waktu'] }})</span>
                                            </td>
                                            <td class="p-3 text-xs text-white/90">{{ $activity['tindakan'] }}</td>
                                            <td class="p-3 text-xs text-white/90">{{ $activity['detail_buku'] }}</td>
                                            <td class="p-3 text-xs font-bold text-white/90">{{ $activity['user'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-4 text-center text-xs text-white/80 italic">Belum ada aktivitas terbaru.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Render Navigasi Paginasi Pintar -->
                    <div class="mt-4">
                        {{ $recentActivities->links('vendor.pagination.custom') }}
                    </div>
                </div>

                <!-- 2. TABEL RINGKASAN TRANSAKSI -->
                <div class="bg-[#b0bec5] p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.1)] border border-white/20 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <h2 class="text-lg font-bold text-white tracking-wide">Transaksi Keuangan</h2>
                            <span class="text-xs bg-[#004d40] text-white px-3 py-1 rounded-full font-semibold">
                                Total: Rp {{ number_format($totalNominalTransaksi ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                        <p class="text-xs text-white/80 mb-4">Daftar transaksi denda & administrasi perpustakaan.</p>
                        
                        <div class="overflow-x-auto rounded-lg">
                            <table class="min-w-full text-left border-collapse border border-white/30">
                                <thead>
                                    <tr class="bg-[#004d40] text-white divide-x divide-white/30">
                                        <th class="p-3 text-xs font-bold tracking-wider">Jenis Transaksi</th>
                                        <th class="p-3 text-xs font-bold tracking-wider">Peminjam / User</th>
                                        <th class="p-3 text-xs font-bold tracking-wider">Nominal</th>
                                        <th class="p-3 text-xs font-bold tracking-wider">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody class="text-white divide-y divide-white/30">
                                    @forelse($recentTransactions ?? [] as $trx)
                                        <tr class="divide-x divide-white/30 hover:bg-white/10 transition-colors">
                                            <td class="p-3 text-xs font-bold text-white/90">
                                                {{ ucwords(str_replace('_', ' ', $trx->jenis)) }}
                                            </td>
                                            <td class="p-3 text-xs text-white/90">
                                                {{ $trx->user->name ?? 'Umum / Tanpa Akun' }}
                                            </td>
                                            <td class="p-3 text-xs font-semibold text-white/90">
                                                Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                            </td>
                                            <td class="p-3 text-xs text-white/90">
                                                {{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-4 text-center text-xs text-white/80 italic">Belum ada data transaksi keuangan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mt-4">
                        {{ $recentTransactions->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>

            <div class="bg-[#b0bec5] p-6 rounded-xl shadow-[0_4px_12px_rgba(0,0,0,0.1)] border border-white/20 text-white flex flex-col justify-between">
                <div>
                    <h2 class="text-lg font-bold text-white mb-1 tracking-wide">Kelola Banner Slider</h2>
                    <p class="text-xs text-white/80 mb-4">Upload gambar baru atau aktifkan/arsip banner yang tampil di beranda user.</p>
                </div>

                <!-- Form Upload Gambar Banner Baru -->
                <form action="{{ route('admin.banner.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3 mb-6">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-semibold text-white/90 mb-1">Pilih Gambar Banner (JPG/PNG)</label>
                        <input type="file" name="image" accept="image/*" class="w-full text-xs text-gray-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#003d30] file:text-white hover:file:bg-[#004d40] cursor-pointer bg-white/10 rounded-lg border border-white/15 p-1" required>
                    </div>
                    <button type="submit" class="bg-[#003d30] hover:bg-[#004d40] text-white text-xs font-semibold px-4 py-2 rounded-lg transition shadow">
                        Upload & Simpan Banner
                    </button>
                </form>

                <!-- Daftar Riwayat Banner -->
                <div class="border-t border-white/20 pt-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider mb-2 text-white/90">Riwayat Pilihan Banner</h3>
                    <div class="max-h-48 overflow-y-auto space-y-2.5 pr-1">
                        @php
                            $banners = \App\Models\Banner::latest()->get();
                        @endphp

                        @forelse($banners as $banner)
                            <div class="bg-white/10 p-2.5 rounded-lg border border-white/15 flex items-center justify-between gap-3 text-xs">
                                <!-- Preview Gambar & Status -->
                                <div class="flex items-center gap-3 truncate">
                                    <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Banner Preview" class="w-12 h-8 object-cover rounded shadow-sm border border-white/20 shrink-0">
                                    <div class="truncate">
                                        <div class="flex items-center gap-2">
                                            @if($banner->is_active)
                                                <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0" title="Sedang Aktif di User"></span>
                                                <span class="font-bold text-white text-[11px]">Aktif Ditampilkan</span>
                                            @else
                                                <span class="w-2 h-2 rounded-full bg-gray-400 shrink-0" title="Arsip"></span>
                                                <span class="text-white/70 text-[11px]">Arsip</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Aksi (Gunakan / Arsip & Hapus) -->
                                <div class="flex items-center gap-1.5 shrink-0">
                                    @if(!$banner->is_active)
                                        <form action="{{ route('admin.banner.activate', $banner->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-2.5 py-1 rounded text-[10px] font-semibold transition">
                                                Gunakan
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.banner.deactivate', $banner->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-2.5 py-1 rounded text-[10px] font-semibold transition" title="Sembunyikan dari beranda">
                                                Nonaktifkan
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('admin.banner.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus banner ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-2 py-1 rounded text-[10px] font-semibold transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-[11px] text-white/70 italic">Belum ada riwayat banner.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const points = document.querySelectorAll('.chart-point');
    const tooltip = document.getElementById('chartTooltip');

    let hideTimeout = setTimeout(() => {
        tooltip.classList.add('hidden');
    }, 200);

    /*
    |--------------------------------------------------------------------------
    | Fungsi menampilkan tooltip
    |--------------------------------------------------------------------------
    */
    function showTooltip(point) {

        // Batalkan timer hide jika ada
        clearTimeout(hideTimeout);

        const count = parseInt(point.dataset.count || 0);
        const details = JSON.parse(point.dataset.details || '[]');

        let html = `
            <div class="font-bold text-[#004d40] mb-1">
                ${formatDate(point.dataset.date)}
            </div>

            <div class="font-semibold mb-2">
                ${count} Peminjaman
            </div>
        `;

        /*
        |--------------------------------------------------------------------------
        | Jika ada peminjaman
        |--------------------------------------------------------------------------
        */
        if (details.length > 0) {

            html += `
                <div class="border-t border-gray-200 pt-2 space-y-2 max-h-48 overflow-y-auto">
            `;

            details.forEach((item, index) => {

                html += `
                    <div>
                        <div class="font-semibold text-gray-700">
                            ${index + 1}. ${escapeHtml(item.peminjam)}
                        </div>

                        <div class="text-gray-500 text-[11px] mt-0.5">
                            ${escapeHtml(item.buku)}
                        </div>
                    </div>
                `;

            });

            html += `
                </div>
            `;

        } else {

            html += `
                <div class="text-gray-400 italic">
                    Tidak ada peminjaman.
                </div>
            `;
        }

        tooltip.innerHTML = html;

        /*
        |--------------------------------------------------------------------------
        | Tampilkan tooltip terlebih dahulu agar ukuran bisa dihitung
        |--------------------------------------------------------------------------
        */
        tooltip.classList.remove('hidden');

        const pointRect = point.getBoundingClientRect();
        const container = point.closest('.relative');
        const containerRect = container.getBoundingClientRect();

        const tooltipWidth = tooltip.offsetWidth;
        const tooltipHeight = tooltip.offsetHeight;

        /*
        |--------------------------------------------------------------------------
        | Posisi horizontal
        |
        | Bubble berada tepat di tengah titik.
        |--------------------------------------------------------------------------
        */
        let left =
            (pointRect.left - containerRect.left)
            + (pointRect.width / 2)
            - (tooltipWidth / 2);

        /*
        |--------------------------------------------------------------------------
        | Posisi vertikal
        |
        | Bubble berada DI ATAS titik.
        |--------------------------------------------------------------------------
        */
        let top =
            (pointRect.top - containerRect.top)
            - tooltipHeight
            - 12;

        /*
        |--------------------------------------------------------------------------
        | Jangan sampai bubble keluar dari sisi kiri
        |--------------------------------------------------------------------------
        */
        const padding = 8;

        if (left < padding) {
            left = padding;
        }

        /*
        |--------------------------------------------------------------------------
        | Jangan sampai bubble keluar dari sisi kanan
        |--------------------------------------------------------------------------
        */
        if (left + tooltipWidth > containerRect.width - padding) {
            left = containerRect.width - tooltipWidth - padding;
        }

        /*
        |--------------------------------------------------------------------------
        | Terapkan posisi
        |--------------------------------------------------------------------------
        */
        tooltip.style.left = `${left}px`;
        tooltip.style.top = `${top}px`;
    }


    /*
    |--------------------------------------------------------------------------
    | Fungsi menyembunyikan tooltip dengan delay
    |--------------------------------------------------------------------------
    */
    function hideTooltip() {

        clearTimeout(hideTimeout);

        hideTimeout = setTimeout(() => {
            tooltip.classList.add('hidden');
        }, 200);

    }


    /*
    |--------------------------------------------------------------------------
    | Mouse masuk ke titik
    |--------------------------------------------------------------------------
    */
    points.forEach(point => {

        point.addEventListener('mouseenter', function () {

            showTooltip(this);

        });

        point.addEventListener('mouseleave', function () {

            hideTooltip();

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Mouse masuk ke tooltip
    |
    | Tooltip TIDAK akan hilang ketika cursor masuk ke bubble.
    |--------------------------------------------------------------------------
    */
    tooltip.addEventListener('mouseenter', function () {

        clearTimeout(hideTimeout);

    });


    /*
    |--------------------------------------------------------------------------
    | Mouse keluar dari tooltip
    |--------------------------------------------------------------------------
    */
    tooltip.addEventListener('mouseleave', function () {

        hideTooltip();

    });


    /*
    |--------------------------------------------------------------------------
    | Format tanggal
    |--------------------------------------------------------------------------
    */
    function formatDate(dateString) {

        if (!dateString) {
            return '';
        }

        const date = new Date(dateString + 'T00:00:00');

        return date.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        });

    }


    /*
    |--------------------------------------------------------------------------
    | Mencegah HTML injection dari nama/buku
    |--------------------------------------------------------------------------
    */
    function escapeHtml(text) {

        if (!text) {
            return '';

        }

        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

    }

});
</script>
@endsection
