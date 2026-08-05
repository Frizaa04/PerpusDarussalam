@extends('layouts.pages.users.provider.app')

@section('content')
<div class="bg-[#004d40] min-h-[calc(100vh-140px)] py-12 px-4 flex flex-col items-center">
    
    <!-- Judul Halaman -->
    <h1 class="text-white text-2xl md:text-3xl font-bold tracking-wider uppercase mb-8 text-center">
        TENTANG PERPUSTAKAAN
    </h1>

    <div class="w-full max-w-4xl space-y-8">
        
        <!-- Profile / Deskripsi Singkat -->
        <div class="bg-[#b2c8c6] rounded-2xl p-6 md:p-8 shadow-xl text-[#003d30]">
            <h2 class="text-xl md:text-2xl font-bold mb-3 border-b-2 border-[#004d40] pb-2">
                Selamat Datang di Perpustakaan Madrasah Darussalam
            </h2>
            <p class="leading-relaxed text-sm md:text-base font-medium mb-4">
                Perpustakaan Madrasah Darussalam merupakan pusat sumber belajar yang menyediakan berbagai koleksi buku cetak, literasi digital (E-Book), serta ruang baca yang nyaman untuk seluruh siswa, guru, dan staf madrasah.
            </p>
            <p class="leading-relaxed text-sm md:text-base font-medium">
                Kami berkomitmen untuk mendukung proses pembelajaran dan meningkatkan minat baca civitas akademika melalui layanan perpustakaan yang modern, mudah diakses, dan responsif.
            </p>
        </div>

        <!-- Grid Informasi Kontak & Operasional -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Card Kontak & Alamat -->
            <div class="bg-[#b2c8c6] rounded-2xl p-6 shadow-xl text-[#003d30] flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold mb-4 flex items-center gap-2 border-b border-[#004d40]/30 pb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Kontak & Lokasi
                    </h3>
                    
                    <ul class="space-y-3 text-sm font-medium">
                        <li class="flex items-start gap-3">
                            <span class="font-bold w-20 shrink-0">Alamat</span>
                            <span>: Jl. Kh. Ahmad Dahlan No.1, Kompleks Madrasah Darussalam</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="font-bold w-20 shrink-0">Telepon / WA</span>
                            <span>: +62 812-3456-7890</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="font-bold w-20 shrink-0">Email</span>
                            <span>: perpus@darussalam.sch.id</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Card Jam Operasional -->
            <div class="bg-[#b2c8c6] rounded-2xl p-6 shadow-xl text-[#003d30] flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold mb-4 flex items-center gap-2 border-b border-[#004d40]/30 pb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Jam Layanan Perpustakaan
                    </h3>
                    
                    <ul class="space-y-2 text-sm font-medium">
                        <li class="flex justify-between border-b border-[#004d40]/10 py-1">
                            <span>Senin - Kamis</span>
                            <span class="font-bold">08.00 - 15.00 WITA</span>
                        </li>
                        <li class="flex justify-between border-b border-[#004d40]/10 py-1">
                            <span>Jumat</span>
                            <span class="font-bold">08.00 - 15.30 WITA</span>
                        </li>
                        <li class="flex justify-between text-red-800 font-bold py-1">
                            <span>Sabtu, Minggu & Hari Libur</span>
                            <span>Tutup</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        <!-- Section Google Maps Embed Interaktif -->
        <div class="bg-[#b2c8c6] rounded-2xl p-4 md:p-6 shadow-xl text-[#003d30]">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2 border-b border-[#004d40]/30 pb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                Peta Lokasi Madrasah
            </h3>

            <!-- Container iFrame Peta -->
            <div class="w-full h-80 md:h-[380px] rounded-xl overflow-hidden shadow-inner border border-[#004d40]/20">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.6644262100803!2d117.1140738!3d-0.5544039!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df681725d5f004d%3A0xf100e2a208d049a5!2sMadrasah%20Darussalam%20IBS%20Samarinda!5e0!3m2!1sid!2sid!4v1710000000000!5m2!1sid!2sid" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>

    </div>
</div>
@endsection