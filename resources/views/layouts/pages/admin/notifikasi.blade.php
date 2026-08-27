@extends('layouts.pages.admin.provider.app')

@section('content')
<div class="p-6 sm:p-8 max-w-7xl mx-auto min-h-screen bg-slate-50/50">
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-[#005a4e] flex items-center gap-2">
                <span class="material-icons text-[#005a4e]">notifications</span>
                Daftar Semua Notifikasi
            </h1>
            <p class="text-sm text-slate-500 mt-1">Riwayat seluruh pemberitahuan sistem dan aktivitas anggota.</p>
        </div>

        @php
            $hasUnread = $notifications->where('status', 'unread')->count() > 0;
        @endphp

        @if($hasUnread)
            <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 bg-[#005a4e] hover:bg-[#004d40] text-white text-xs font-semibold px-4 py-2 rounded-lg transition shadow-sm">
                    <span class="material-icons text-sm">done_all</span>
                    Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>

    <!-- Container Card Utama -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        
        <!-- List Notifikasi -->
        <div class="divide-y divide-slate-100">
            @forelse($notifications as $notif)
                <div class="p-4 sm:p-5 hover:bg-slate-50 transition flex items-start justify-between gap-4 {{ $notif->status === 'unread' ? 'bg-[#f0fdf4]/60' : '' }}">
                    
                    <div class="flex items-start gap-3.5">
                        <!-- Icon Status -->
                        <div class="mt-0.5 p-2 rounded-xl shrink-0 {{ in_array($notif->type, ['keterlambatan', 'denda']) ? 'bg-red-50 text-red-500' : 'bg-[#f0fdf4] text-[#005a4e]' }}">
                            @if(in_array($notif->type, ['keterlambatan', 'denda']))
                                <span class="material-icons text-xl">warning</span>
                            @else
                                <span class="material-icons text-xl">info</span>
                            @endif
                        </div>

                        <!-- Content Text -->
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-bold text-sm text-slate-800">{{ $notif->title }}</h3>
                                @if($notif->status === 'unread')
                                    <span class="inline-block w-2 h-2 rounded-full bg-red-500"></span>
                                @endif
                            </div>
                            
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">{{ $notif->message }}</p>

                            <!-- Detail Anggota jika relasi borrowing ada -->
                            @if($notif->borrowing && $notif->borrowing->user)
                                <div class="mt-2 inline-flex items-center gap-1 text-[11px] font-medium text-[#005a4e] bg-[#f0fdf4] border border-[#005a4e]/10 px-2.5 py-1 rounded-md">
                                    <span class="material-icons text-xs">person</span>
                                    Anggota: {{ $notif->borrowing->user->name ?? '-' }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Timestamp -->
                    <div class="text-right shrink-0">
                        <span class="text-[11px] text-slate-400 font-medium block">
                            {{ $notif->created_at->diffForHumans() }}
                        </span>
                        <span class="text-[10px] text-slate-300 block mt-0.5">
                            {{ $notif->created_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                </div>
            @empty
                <!-- Empty State (Disesuaikan dengan Gambar 2) -->
                <div class="p-12 text-center">
                    <div class="w-16 h-16 mx-auto bg-[#f0fdf4] text-[#005a4e] rounded-2xl flex items-center justify-center mb-3">
                        <span class="material-icons text-3xl">notifications_off</span>
                    </div>
                    <h4 class="text-sm font-semibold text-slate-800">Belum Ada Notifikasi</h4>
                    <p class="text-xs text-slate-400 mt-1">Seluruh riwayat pemberitahuan sistem akan muncul di sini.</p>
                </div>
            @endforelse
        </div>

        <!-- Footer / Pagination -->
        @if(method_exists($notifications, 'links'))
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection