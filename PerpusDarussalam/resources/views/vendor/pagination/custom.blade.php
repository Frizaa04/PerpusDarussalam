@if ($paginator->hasPages())
    <div class="flex items-center justify-between border-t border-white/20 pt-3 mt-3 text-xs text-white">
        <div>
            <span>Menampilkan <b>{{ $paginator->firstItem() }}</b> sampai <b>{{ $paginator->lastItem() }}</b> dari <b>{{ $paginator->total() }}</b> data</span>
        </div>
        <div class="flex items-center gap-1">
            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <span class="px-2.5 py-1 bg-white/10 text-white/40 rounded cursor-not-allowed">Prev</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-2.5 py-1 bg-[#003d30] hover:bg-[#004d40] text-white rounded transition">Prev</a>
            @endif

            {{-- Logika Angka Halaman Pintar (Maksimal menampilkan rentang tertentu) --}}
            @php
                $current = $paginator->currentPage();
                $last = $paginator->lastPage();
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);

                if ($current <= 3) {
                    $end = min(5, $last);
                }
                if ($current >= $last - 2) {
                    $start = max(1, $last - 4);
                }
            @endphp

            @if($start > 1)
                <a href="{{ $paginator->url(1) }}" class="px-2.5 py-1 bg-white/10 hover:bg-white/20 text-white rounded transition">1</a>
                @if($start > 2)
                    <span class="px-1 text-white/60">...</span>
                @endif
            @endif

            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $current)
                    <span class="px-2.5 py-1 bg-[#004d40] font-bold text-white rounded shadow">{{ $i }}</span>
                @else
                    <a href="{{ $paginator->url($i) }}" class="px-2.5 py-1 bg-white/10 hover:bg-white/20 text-white rounded transition">{{ $i }}</a>
                @endif
            @endfor

            @if($end < $last)
                @if($end < $last - 1)
                    <span class="px-1 text-white/60">...</span>
                @endif
                <a href="{{ $paginator->url($last) }}" class="px-2.5 py-1 bg-white/10 hover:bg-white/20 text-white rounded transition">{{ $last }}</a>
            @endif

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-2.5 py-1 bg-[#003d30] hover:bg-[#004d40] text-white rounded transition">Next</a>
            @else
                <span class="px-2.5 py-1 bg-white/10 text-white/40 rounded cursor-not-allowed">Next</span>
            @endif
        </div>
    </div>
@endif