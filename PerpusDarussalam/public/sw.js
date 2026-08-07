// Mendengarkan event push dari server/backend
self.addEventListener('push', function (event) {
    let data = { title: 'Notifikasi Baru', body: 'Ada informasi penting untukmu.' };
    
    if (event.data) {
        data = event.data.json();
    }

    const options = {
        body: data.body,
        icon: '/image/logo.png', // Sesuaikan path ikon perpustakaanmu jika ada
        badge: '/image/badge.png'
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Event saat user mengklik notifikasi
self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow('/') // Halaman yang dibuka saat notifikasi diklik
    );
});