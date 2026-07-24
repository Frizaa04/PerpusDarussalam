<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pemustaka - Perpustakaan Madrasah Darussalam</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Tailwind CSS CDN Fallback jika belum di-build -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-[#f4f7f6] min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-xl shadow-xl overflow-hidden border border-emerald-100">
        
        <!-- Header Card Tema Hijau Darussalam -->
        <div class="bg-[#00695c] px-8 py-6 text-center text-white relative">
            <div class="flex justify-center mb-3">
                <img src="{{ asset('image/covers/darussalam.png') }}" alt="Madrasah Darussalam" class="h-16 object-contain bg-white/10 p-2 rounded-lg">
            </div>
            <h2 class="text-2xl font-bold tracking-wider uppercase">Masuk Pemustaka</h2>
            <p class="text-xs text-emerald-100 tracking-widest uppercase mt-1">Perpustakaan Madrasah Darussalam</p>
        </div>

        <!-- Form Login -->
        <div class="p-8 space-y-6">

            <!-- Notifikasi Error -->
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('user.login.post') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Input Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email / NIK / NIS</label>
                    <div class="relative">
                        <span class="material-icons absolute left-3 top-2.5 text-gray-400 text-xl">email</span>
                        <input type="email" name="email" id="email" required value="{{ old('email') }}"
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00695c] focus:border-[#00695c] outline-none text-sm transition"
                            placeholder="masukkan email anda...">
                    </div>
                </div>

                <!-- Input Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Kata Sandi</label>
                    <div class="relative">
                        <span class="material-icons absolute left-3 top-2.5 text-gray-400 text-xl">lock</span>
                        <input type="password" name="password" id="password" required
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#00695c] focus:border-[#00695c] outline-none text-sm transition"
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Tombol Submit Login -->
                <button type="submit" 
                    class="w-full bg-[#00695c] hover:bg-[#004d40] text-white font-bold py-3 rounded-lg transition duration-200 shadow-md flex items-center justify-center gap-2">
                    <span class="material-icons text-lg">login</span>
                    <span>Masuk Aplikasi</span>
                </button>
            </form>

            <div class="text-center pt-2">
                <a href="{{ route('welcome') }}" class="text-xs text-[#00695c] hover:underline font-semibold flex items-center justify-center gap-1">
                    <span class="material-icons text-sm">arrow_back</span> Kembali ke Beranda
                </a>
            </div>

        </div>
    </div>

</body>
</html>