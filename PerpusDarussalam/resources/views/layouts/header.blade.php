<header class="bg-white border-b border-gray-200 px-8 flex justify-end items-center gap-4 shadow-sm h-20">
    <button type="button" onclick="toggleSidebar()" class="text-gray-600 hover:text-[#004d40] transition mr-auto">
        <span class="material-icons text-2xl">menu</span>
    </button>

    <button type="button" class="text-gray-600 hover:text-[#004d40] transition">
        <span class="material-icons text-2xl">notifications</span>
    </button>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit"
            class="bg-[#005a4e] hover:bg-[#004d40] text-white px-4 py-1.5 rounded font-bold text-sm tracking-wide transition shadow-sm">
            LogOut
        </button>
    </form>

    <div class="flex items-center h-full pl-2">
        <img src="{{ asset('image/covers/darussalam.png') }}"
             alt="Logo Darussalam"
             class="h-14 py-1 object-contain">
    </div>
</header>