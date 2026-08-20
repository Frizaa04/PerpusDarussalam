<header class="bg-white border-b border-gray-200 px-8 flex justify-end items-center gap-4 shadow-sm h-20">
    <!-- Button Sidebar (Paling Kiri) -->
    <button type="button" onclick="toggleSidebar()" class="text-gray-600 hover:text-[#004d40] transition mr-auto">
        <span class="material-icons text-2xl">menu</span>
    </button>

    <!-- Notification Container -->
    <div class="relative">
        <!-- Notification Trigger Button -->
        <button type="button" id="notif-btn" onclick="toggleNotifDropdown()"
            class="relative text-[#005a4e] hover:text-[#004d40] transition p-2 focus:outline-none flex items-center justify-center rounded-full hover:bg-emerald-50">
            <span class="material-icons text-2xl">notifications</span>

            @php
                $unreadCount = \App\Models\Notification::where('status', 'unread')->count();
            @endphp
            @if ($unreadCount > 0)
                <!-- TAMBAHKAN id="notification-badge" DI SINI -->
                <span id="notification-badge" class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-600"></span>
                </span>
            @endif
        </button>

        <!-- Notification Dropdown Menu -->
        <div id="notif-dropdown"
            class="hidden absolute right-0 mt-3 w-80 sm:w-96 bg-white text-gray-800 rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden">

            <!-- Dropdown Header -->
            <div class="flex items-center justify-between px-4 py-3 bg-[#005a4e]/5 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="material-icons text-sm text-[#005a4e]">inbox</span>
                    <h3 class="text-sm font-bold text-[#005a4e] tracking-wide">Notifikasi</h3>
                </div>
                @if ($unreadCount > 0)
                    <button type="button" onclick="markAllAsReadFromDropdown()"
                        class="text-[11px] text-[#005a4e] hover:underline font-medium">
                        Tandai semua dibaca
                    </button>
                @endif
            </div>

            <!-- Notification Item List (Preview 5 Teratas) -->
            <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                @php
                    $notifications = \App\Models\Notification::with('borrowing.user')->latest()->take(5)->get();
                @endphp

                @forelse($notifications as $notif)
                    <div id="notif-item-{{ $notif->id }}"
                        class="p-3.5 hover:bg-emerald-50/40 transition flex items-start gap-3 {{ $notif->status === 'unread' ? 'bg-emerald-50/20' : '' }}">
                        <div class="mt-0.5 shrink-0">
                            @if (in_array($notif->type, ['keterlambatan', 'denda']))
                                <span class="material-icons text-red-500 text-lg">warning</span>
                            @else
                                <span class="material-icons text-[#005a4e] text-lg">info</span>
                            @endif
                        </div>

                        <div class="flex-1 text-xs">
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="font-semibold text-gray-800 leading-tight">{{ $notif->title }}</span>
                                <span
                                    class="text-[10px] text-gray-400 shrink-0 ml-2">{{ $notif->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-gray-600 leading-normal">{{ $notif->message }}</p>

                            <div class="flex items-center justify-between mt-2">
                                @if ($notif->borrowing && $notif->borrowing->user)
                                    <p class="text-[10px] text-[#005a4e] font-medium">
                                        Anggota: {{ $notif->borrowing->user->name ?? '-' }}
                                    </p>
                                @else
                                    <div></div>
                                @endif

                                <!-- TOMBOL TANDAI DIBACA VIA AJAX -->
                                <div id="action-container-{{ $notif->id }}">
                                    @if ($notif->status === 'unread')
                                        <button type="button" onclick="markAsReadAjax('{{ $notif->id }}')"
                                            class="text-[10px] bg-[#005a4e] hover:bg-[#004d40] text-white px-2.5 py-1 rounded font-semibold transition flex items-center gap-1 shadow-sm">
                                            <span class="material-icons text-[12px]">check</span> Tandai Dibaca
                                        </button>
                                    @else
                                        <span
                                            class="text-[10px] text-gray-400 font-medium italic flex items-center gap-0.5">
                                            <span class="material-icons text-[12px] text-green-600">done_all</span>
                                            Dibaca
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-gray-400">
                        Tidak ada notifikasi baru.
                    </div>
                @endforelse
            </div>

            <!-- Dropdown Footer: Tombol Trigger Modal Semua Notifikasi -->
            <div class="p-3 bg-gray-50/80 text-center border-t border-gray-100">
                <button type="button" onclick="openAllNotifModal()"
                    class="text-xs text-[#005a4e] hover:text-[#004d40] font-bold block w-full transition">
                    Lihat Semua Notifikasi &rarr;
                </button>
            </div>
        </div>
    </div>

    <!-- Form Logout -->
    <form action="{{ route('admin.logout') }}" method="POST">
        @csrf
        <button type="submit"
            class="bg-[#005a4e] hover:bg-[#004d40] text-white px-4 py-1.5 rounded font-bold text-sm tracking-wide transition shadow-sm">
            LogOut
        </button>
    </form>

    <!-- Logo Brand -->
    <div class="flex items-center h-full pl-2">
        <img src="{{ asset('image/covers/darussalam.png') }}" alt="Logo Darussalam" class="h-14 py-1 object-contain">
    </div>
</header>

<!-- ========================================== -->
<!-- MODAL POPUP: SEMUA NOTIFIKASI              -->
<!-- ========================================== -->
<div id="all-notif-modal"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4 hidden">
    <div class="bg-white w-full max-w-2xl rounded-xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh]">

        <!-- Header Modal -->
        <div class="px-6 py-4 bg-[#005a4e] text-white flex items-center justify-between">
            <h3 class="font-bold text-base flex items-center gap-2">
                <span class="material-icons">notifications</span> Daftar Semua Notifikasi
            </h3>

            <div class="flex items-center gap-4">
                @php
                    $unreadCount = \App\Models\Notification::where('status', 'unread')->count();
                @endphp

                @if ($unreadCount > 0)
                    <button type="button" onclick="markAllAsReadFromModal()"
                        class="text-xs text-emerald-200 hover:text-white font-medium underline transition">
                        Tandai semua dibaca
                    </button>
                @endif

                <button type="button" onclick="closeAllNotifModal()"
                    class="text-white hover:text-gray-200 focus:outline-none">
                    <span class="material-icons">close</span>
                </button>
            </div>
        </div>

        <!-- Body Modal (Menampilkan Seluruh Riwayat Notifikasi) -->
        <div class="p-6 overflow-y-auto space-y-3 bg-gray-50 flex-1 divide-y divide-gray-100">
            @php
                // Mengambil seluruh data notifikasi dari database
                $allNotifications = \App\Models\Notification::with('borrowing.user')->latest()->get();
            @endphp

            @forelse($allNotifications as $item)
                <div id="notif-item-modal-{{ $item->id }}"
                    class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex items-start gap-3 pt-4 first:pt-0 {{ $item->status === 'unread' ? 'bg-emerald-50/20 border-l-4 border-l-[#005a4e]' : '' }}">
                    <div class="mt-0.5 shrink-0">
                        @if (in_array($item->type, ['keterlambatan', 'denda']))
                            <span class="material-icons text-red-500 text-xl">warning</span>
                        @else
                            <span class="material-icons text-[#005a4e] text-xl">info</span>
                        @endif
                    </div>

                    <div class="flex-1 text-xs">
                        <div class="flex justify-between items-start mb-1">
                            <h4 class="font-bold text-gray-800 text-sm">{{ $item->title }}</h4>
                            <span class="text-[11px] text-gray-400">{{ $item->created_at->format('d M Y, H:i') }}
                                ({{ $item->created_at->diffForHumans() }})
                            </span>
                        </div>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $item->message }}</p>

                        <div class="flex items-center justify-between mt-3">
                            @if ($item->borrowing && $item->borrowing->user)
                                <div
                                    class="inline-flex items-center gap-1 bg-emerald-50 text-[#005a4e] text-xs px-2.5 py-1 rounded font-medium">
                                    <span class="material-icons text-sm">person</span> Anggota:
                                    {{ $item->borrowing->user->name ?? '-' }}
                                </div>
                            @else
                                <div></div>
                            @endif

                            <!-- Tombol Tandai Dibaca AJAX untuk Pop-up Besar -->
                            <div id="action-container-modal-{{ $item->id }}">
                                @if ($item->status === 'unread')
                                    <button type="button" onclick="markAsReadAjax('{{ $item->id }}')"
                                        class="text-[11px] bg-[#005a4e] hover:bg-[#004d40] text-white px-3 py-1 rounded-md font-semibold transition flex items-center gap-1 shadow-sm">
                                        <span class="material-icons text-xs">check</span> Tandai Dibaca
                                    </button>
                                @else
                                    <span class="text-[11px] text-gray-400 font-medium italic flex items-center gap-1">
                                        <span class="material-icons text-xs text-green-600">done_all</span> Dibaca
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-sm text-gray-400">
                    Belum ada riwayat notifikasi sama sekali.
                </div>
            @endforelse
        </div>

        <!-- Footer Modal -->
        <div class="px-6 py-3 bg-gray-100 flex justify-end border-t border-gray-200">
            <button type="button" onclick="closeAllNotifModal()"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-xs font-bold transition">
                Tutup
            </button>
        </div>

    </div>
</div>

<script>
    function toggleNotifDropdown() {
        const dropdown = document.getElementById('notif-dropdown');
        dropdown.classList.toggle('hidden');
    }

    function openAllNotifModal() {
        const dropdown = document.getElementById('notif-dropdown');
        if (dropdown) {
            dropdown.classList.add('hidden');
        }

        const modal = document.getElementById('all-notif-modal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeAllNotifModal() {
        const modal = document.getElementById('all-notif-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    // Menutup dropdown atau modal saat klik di luar area
    document.addEventListener('click', function(event) {
        const btn = document.getElementById('notif-btn');
        const dropdown = document.getElementById('notif-dropdown');
        const modal = document.getElementById('all-notif-modal');

        if (btn && dropdown && !btn.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }

        if (modal && event.target === modal) {
            closeAllNotifModal();
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        if (localStorage.getItem('keepLargeModalOpen') === 'true') {
            localStorage.removeItem('keepLargeModalOpen');
            openAllNotifModal();
        }
    });

    // Fungsi untuk Pop-up Kecil 
    function markAllAsReadFromDropdown() {
        fetch("{{ route('notifications.markAllAsRead') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => console.error("Error:", error));
    }

    // Fungsi untuk Pop-up Besar 
    function markAllAsReadFromModal() {
        fetch("{{ route('notifications.markAllAsRead') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            })
            .then(response => {
                if (response.ok) {
                    localStorage.setItem('keepLargeModalOpen', 'true');
                    location.reload();
                }
            })
            .catch(error => console.error("Error:", error));
    }

    // Fungsi tunggal AJAX untuk memperbarui status satuan
    function markAsReadAjax(id) {
        fetch(`/admin/notifikasi/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const readHtmlDropdown = `
                    <span class="text-[10px] text-gray-400 font-medium italic flex items-center gap-0.5">
                        <span class="material-icons text-[12px] text-green-600">done_all</span> Dibaca
                    </span>
                `;

                    const readHtmlModal = `
                    <span class="text-[11px] text-gray-400 font-medium italic flex items-center gap-1">
                        <span class="material-icons text-xs text-green-600">done_all</span> Dibaca
                    </span>
                `;

                    const actionContainerKecil = document.getElementById(`action-container-${id}`);
                    if (actionContainerKecil) {
                        actionContainerKecil.innerHTML = readHtmlDropdown;
                    }
                    const notifItemKecil = document.getElementById(`notif-item-${id}`);
                    if (notifItemKecil) {
                        notifItemKecil.classList.remove('bg-emerald-50/20');
                    }

                    const actionContainerModal = document.getElementById(`action-container-modal-${id}`);
                    if (actionContainerModal) {
                        actionContainerModal.innerHTML = readHtmlModal;
                    }
                    const notifItemModal = document.getElementById(`notif-item-modal-${id}`);
                    if (notifItemModal) {
                        notifItemModal.classList.remove('bg-emerald-50/20', 'border-l-4', 'border-l-[#005a4e]');
                    }

                    // --- TAMBAHKAN KODE INI UNTUK MENGHILANGKAN TITIK MERAH DI LONCENG ---
                    const notificationBadge = document.getElementById(
                    'notification-badge'); // Sesuaikan ID elemen titik merah di ikon lonceng Anda
                    if (notificationBadge) {
                        notificationBadge.style.display = 'none';
                        // Atau bisa pakai: notificationBadge.remove();
                    }
                    // -----------------------------------------------------------------
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>
