<!-- SLEEK REUSABLE DATE FILTER COMPONENT -->
<div x-data="{ customModal: false }" class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div class="flex items-center gap-2">
        <div>
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Rentang Waktu Laporan</p>
            <h4 class="text-sm font-extrabold text-gray-800">{{ $dateFilter['label'] ?? 'Bulan Ini' }}</h4>
        </div>
    </div>

    <!-- Filter Buttons & Custom Trigger -->
    <div class="flex flex-wrap items-center gap-1.5 w-full md:w-auto">
        <a href="{{ request()->fullUrlWithQuery(['date_range' => 'today', 'start_date' => null, 'end_date' => null]) }}" 
           class="px-3 py-1.5 text-xs font-bold rounded-lg transition {{ ($dateFilter['range'] ?? '') == 'today' ? 'bg-amber-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Hari Ini
        </a>
        <a href="{{ request()->fullUrlWithQuery(['date_range' => 'this_week', 'start_date' => null, 'end_date' => null]) }}" 
           class="px-3 py-1.5 text-xs font-bold rounded-lg transition {{ ($dateFilter['range'] ?? '') == 'this_week' ? 'bg-amber-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Minggu Ini
        </a>
        <a href="{{ request()->fullUrlWithQuery(['date_range' => 'this_month', 'start_date' => null, 'end_date' => null]) }}" 
           class="px-3 py-1.5 text-xs font-bold rounded-lg transition {{ ($dateFilter['range'] ?? '') == 'this_month' ? 'bg-amber-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Bulan Ini
        </a>
        <a href="{{ request()->fullUrlWithQuery(['date_range' => 'this_year', 'start_date' => null, 'end_date' => null]) }}" 
           class="px-3 py-1.5 text-xs font-bold rounded-lg transition {{ ($dateFilter['range'] ?? '') == 'this_year' ? 'bg-amber-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Tahun Ini
        </a>
        <a href="{{ request()->fullUrlWithQuery(['date_range' => 'all_time', 'start_date' => null, 'end_date' => null]) }}" 
           class="px-3 py-1.5 text-xs font-bold rounded-lg transition {{ ($dateFilter['range'] ?? '') == 'all_time' ? 'bg-amber-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            All Time
        </a>

        <!-- Custom Date Trigger -->
        <button @click="customModal = true" 
                class="px-3 py-1.5 text-xs font-bold rounded-lg transition flex items-center gap-1 {{ ($dateFilter['range'] ?? '') == 'custom' ? 'bg-amber-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            <span>Custom Date</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </button>
    </div>

    <!-- MODAL CUSTOM DATE PICKER -->
    <div x-show="customModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" @keydown.escape.window="customModal = false">
        <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl relative" @click.away="customModal = false">
            <h3 class="text-base font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                <span>Pilih Rentang Tanggal Kustom</span>
            </h3>

            <form action="{{ url()->current() }}" method="GET" class="space-y-4">
                <input type="hidden" name="date_range" value="custom">
                @if(request()->has('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Dari Tanggal (Start)</label>
                    <input type="date" name="start_date" value="{{ $dateFilter['raw_start'] ?? '' }}" required class="w-full border border-gray-300 p-2 rounded-lg text-xs font-semibold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Sampai Tanggal (End)</label>
                    <input type="date" name="end_date" value="{{ $dateFilter['raw_end'] ?? '' }}" required class="w-full border border-gray-300 p-2 rounded-lg text-xs font-semibold">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" @click="customModal = false" class="px-4 py-2 text-xs font-bold text-gray-500 hover:text-gray-700">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold bg-amber-600 text-white rounded-lg hover:bg-amber-700 shadow-sm">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
