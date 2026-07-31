<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Perpustakaan Madrasah Darussalam</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
</head>
<body class="bg-[#f4f7f6] text-gray-800 transition-colors duration-300 min-h-screen flex flex-col">

    <!-- Memanggil Header + Navigation Menu User -->
    @include('layouts.pages.users.provider.navbar')

    <!-- Tempat konten home.blade disisipkan -->
    <main class="flex-1">
        @yield('content')
    </main>

</body>
</html>