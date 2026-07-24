<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Perpustakaan Madrasah Darussalam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-[#1e293b] min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-xl shadow-2xl overflow-hidden border border-slate-700">
        
        <!-- Header Admin Card -->
        <div class="bg-[#0f172a] px-8 py-6 text-center text-white border-b border-slate-700">
            <div class="flex justify-center mb-3">
                <div class="p-3 bg-emerald-600/20 rounded-full text-emerald-400">
                    <span class="material-icons text-4xl">admin_panel_settings</span>
                </div>
            </div>
            <h2 class="text-2xl font-bold tracking-wider uppercase">Portal Administrator</h2>
            <p class="text-xs text-slate-400 tracking-widest uppercase mt-1">Perpustakaan Madrasah Darussalam</p>
        </div>

        <!-- Form Login Admin -->
        <div class="p-8 space-y-6 bg-slate-50">

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded text-sm shadow-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded text-sm shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-700 p-4 rounded text-sm shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Input Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Email Admin</label>
                    <div class="relative">
                        <span class="material-icons absolute left-3 top-2.5 text-slate-400 text-xl">admin_panel_settings</span>
                        <input type="email" name="email" id="email" required value="{{ old('email') }}"
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-[#00695c] focus:border-[#00695c] outline-none text-sm transition bg-white"
                            placeholder="admin@gmail.com">
                    </div>
                </div>

                <!-- Input Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Kata Sandi</label>
                    <div class="relative">
                        <span class="material-icons absolute left-3 top-2.5 text-slate-400 text-xl">lock</span>
                        <input type="password" name="password" id="password" required
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-[#00695c] focus:border-[#00695c] outline-none text-sm transition bg-white"
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" 
                    class="w-full bg-[#00695c] hover:bg-[#004d40] text-white font-bold py-3 rounded-lg transition duration-200 shadow-lg flex items-center justify-center gap-2">
                    <span class="material-icons text-lg">verified_user</span>
                    <span>Masuk Administrator</span>
                </button>
            </form>

            <div class="text-center pt-2 border-t border-slate-200">
                <a href="{{ route('welcome') }}" class="text-xs text-slate-500 hover:text-slate-800 font-semibold flex items-center justify-center gap-1">
                    <span class="material-icons text-sm">arrow_back</span> Kembali ke Beranda Utama
                </a>
            </div>

        </div>
    </div>

</body>
</html>