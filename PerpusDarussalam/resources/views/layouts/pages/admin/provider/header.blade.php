<header class="bg-white border-b border-gray-200 px-8 flex justify-end items-center gap-4 shadow-sm h-20">
    <!-- Button Sidebar (Paling Kiri) -->
    <button type="button" onclick="toggleSidebar()" class="text-gray-600 hover:text-[#004d40] transition mr-auto">
        <span class="material-icons text-2xl">menu</span>
    </button>

    <!-- Notification Container -->
    <div class="relative">
        <!-- Notification Trigger Button -->
        <button type="button" id="notif-btn" onclick="toggleNotifDropdown()" class="relative text-[#005a4e] hover:text-[#004d40] transition p-2 focus:outline-none flex items-center justify-center rounded-full hover:bg-emerald-50">
            <span class="material-icons text-2xl">notifications</span>
            
            @php
                $unreadCount = \App\Models\Notification::where('status', 'unread')->count();
            @endphp
            @if($unreadCount > 0)
                <span class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-600"></span>
                </span>
            @endif
        </button>

        <!-- Notification Dropdown Menu (Clean White Theme) -->
        <div id="notif-dropdown" class="hidden absolute right-0 mt-3 w-80 sm:w-96 bg-white text-gray-800 rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden">
            
            <!-- Dropdown Header (Accent Hijau Soft) -->
            <div class="flex items-center justify-between px-4 py-3 bg-[#005a4e]/5 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="material-icons text-sm text-[#005a4e]">inbox</span>
                    <h3 class="text-sm font-bold text-[#005a4e] tracking-wide">Notifikasi</h3>
                </div>
                @if($unreadCount > 0)
                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-[11px] text-[#005a4e] hover:underline font-medium">Tandai semua dibaca</button>
                    </form>
                @endif
            </div>

            <!-- Notification Item List -->
            <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                @php
                    $notifications = \App\Models\Notification::with('borrowing.user')->latest()->take(5)->get();
                @endphp

                @forelse($notifications as $notif)
                    <div class="p-3.5 hover:bg-emerald-50/40 transition flex items-start gap-3 {{ $notif->status === 'unread' ? 'bg-emerald-50/20' : '' }}">
                        <div class="mt-0.5 shrink-0">
                            @if(in_array($notif->type, ['keterlambatan', 'denda']))
                                <span class="material-icons text-red-500 text-lg">warning</span>
                            @else
                                <span class="material-icons text-[#005a4e] text-lg">info</span>
                            @endif
                        </div>

                        <div class="flex-1 text-xs">
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="font-semibold text-gray-800 leading-tight">{{ $notif->title }}</span>
                                <span class="text-[10px] text-gray-400 shrink-0 ml-2">{{ $notif->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-gray-600 leading-normal">{{ $notif->message }}</p>

                            @if($notif->borrowing && $notif->borrowing->user)
                                <p class="text-[10px] text-[#005a4e] mt-1 font-medium">
                                    Anggota: {{ $notif->borrowing->user->name ?? '-' }}
                                </p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-gray-400">
                        Tidak ada notifikasi baru.
                    </div>
                @endforelse
            </div>

            <!-- Dropdown Footer -->
            <div class="p-3 bg-gray-50/80 text-center border-t border-gray-100">
                <a href="{{ route('notifikasi.index') }}" class="text-xs text-[#005a4e] hover:text-[#004d40] font-bold block transition">
                    Lihat Semua Notifikasi &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Form Logout -->
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit"
            class="bg-[#005a4e] hover:bg-[#004d40] text-white px-4 py-1.5 rounded font-bold text-sm tracking-wide transition shadow-sm">
            LogOut
        </button>
    </form>

    <!-- Logo Brand -->
    <div class="flex items-center h-full pl-2">
        <img src="{{ asset('image/covers/darussalam.png') }}"
             alt="Logo Darussalam"
             class="h-14 py-1 object-contain">
    </div>
</header>

<script>
    function toggleNotifDropdown() {
        const dropdown = document.getElementById('notif-dropdown');
        dropdown.classList.toggle('hidden');
    }

    document.addEventListener('click', function(event) {
        const btn = document.getElementById('notif-btn');
        const dropdown = document.getElementById('notif-dropdown');

        if (btn && dropdown && !btn.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>