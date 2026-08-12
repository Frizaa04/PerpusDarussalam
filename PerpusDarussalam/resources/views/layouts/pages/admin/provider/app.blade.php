<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Perpustakaan Madrasah Darussalam</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- 1. CDN JQUERY DITAMBAHKAN DI SINI -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>

    @include('layouts.pages.admin.provider.header')
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 transition-colors duration-300">

    <!-- isi dari Dashboard -->
    @yield('content')

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');

            // Geser sidebar masuk atau keluar layar
            sidebar.classList.toggle('-translate-x-full');
            
            // Tampilkan atau sembunyikan backdrop gelap
            backdrop.classList.toggle('hidden');
        }
    </script>

    <!-- 2. STACK SCRIPTS DITAMBAHKAN DI SINI -->
    @stack('scripts')

</body>
</html>