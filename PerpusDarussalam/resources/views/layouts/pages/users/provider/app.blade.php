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

    <!-- Memanggil Footer User -->
    @include('layouts.pages.users.provider.footer')

    <!-- Modal Pop-up Izin Notifikasi -->
    <div id="pushNotificationModal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden">
        <div
            class="bg-[#003d30] border border-emerald-600/60 rounded-2xl p-6 max-w-sm w-full text-white shadow-2xl space-y-4 text-center">
            <!-- Icon / Ilustrasi Kustom -->
            <div
                class="w-16 h-16 bg-emerald-500/20 text-emerald-300 rounded-full flex items-center justify-center mx-auto text-2xl">
                🔔
            </div>

            <div class="space-y-1">
                <h3 class="font-bold text-lg text-white">Jangan Lewatkan Informasi Penting!</h3>
                <p class="text-xs text-emerald-200/80 leading-relaxed">
                    Aktifkan notifikasi agar kamu langsung tahu saat ada pengingat masa
                    peminjaman.
                </p>
            </div>

            <div class="flex gap-2 pt-2">
                <button onclick="closeNotificationModal()"
                    class="flex-1 bg-transparent hover:bg-white/10 text-emerald-200 font-semibold text-xs py-2.5 rounded-xl border border-emerald-600/40 transition">
                    Nanti Saja
                </button>
                <button onclick="requestPushPermissionFromModal()"
                    class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-[#002820] font-bold text-xs py-2.5 rounded-xl shadow transition">
                    Aktifkan
                </button>
            </div>
        </div>
    </div>

</body>

<script>
    // 1. Pendaftaran Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('Service Worker berhasil didaftar:', registration.scope);
                    return registration.pushManager.getSubscription();
                })
                .then(function(subscription) {
                    if (subscription) {
                        console.log('Pengguna sudah berlangganan push notification:', subscription);
                    } else {
                        console.log('Pengguna belum berlangganan push notification.');
                    }
                })
                .catch(function(err) {
                    console.error('Gagal mendaftarkan Service Worker:', err);
                });
        });
    }

    // 2. Logika Pop-up Modal Notifikasi Otomatis
    document.addEventListener("DOMContentLoaded", function() {
        // Cek apakah browser mendukung notifikasi DAN user belum pernah memberikan izin/menolaknya
        if ('Notification' in window && Notification.permission === 'default') {
            // Munculkan pop-up setelah 2 detik halaman dibuka
            setTimeout(function() {
                const modal = document.getElementById('pushNotificationModal');
                if (modal) {
                    modal.classList.remove('hidden');
                }
            }, 2000);
        }
    });

    // Fungsi saat tombol "Nanti Saja" diklik
    function closeNotificationModal() {
        const modal = document.getElementById('pushNotificationModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    // Fungsi saat tombol "Aktifkan" di dalam modal diklik
    function requestPushPermissionFromModal() {
        Notification.requestPermission().then(function(permission) {
            // Sembunyikan modal
            const modal = document.getElementById('pushNotificationModal');
            if (modal) {
                modal.classList.add('hidden');
            }

            if (permission === 'granted') {
                // Ambil service worker ready lalu kirim status aktif ke backend
                navigator.serviceWorker.ready.then(function(registration) {
                        // Kita gunakan ID unik sederhana atau endpoint dummy untuk testing lokal
                        const fakeEndpoint = "local-device-" + Date.now();

                        return fetch("{{ route('push.subscribe') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                endpoint: fakeEndpoint,
                                publicKey: null,
                                authToken: null
                            })
                        });
                    }).then(response => response.json())
                    .then(data => {
                        alert('Terima kasih! Notifikasi berhasil diaktifkan.');
                        location.reload();
                    }).catch(err => {
                        console.error('Gagal menyimpan:', err);
                    });

            } else {
                console.log('Izin notifikasi ditolak.');
            }
        });
    }
</script>

</html>
