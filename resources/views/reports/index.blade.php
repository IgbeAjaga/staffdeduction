<x-layouts.app>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Reports</h1>
            <div class="flex gap-2">
                <a href="{{ route('reports.export.excel', request()->query()) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm">Export Excel</a>
                <a href="{{ route('reports.export.pdf', request()->query()) }}" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm">Export PDF</a>
                <a href="{{ route('reports.print', request()->query()) }}" target="_blank" class="px-4 py-2 border rounded-lg text-sm">Print</a>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-zinc-900 rounded-xl p-4 shadow">
                <p class="text-xs text-zinc-500">Total Amount</p>
                <p class="text-xl font-bold dark:text-white">{{ number_format($stats['total_amount'], 2) }}</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 rounded-xl p-4 shadow">
                <p class="text-xs text-zinc-500">Total Records</p>
                <p class="text-xl font-bold dark:text-white">{{ $stats['total_count'] }}</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 rounded-xl p-4 shadow">
                <p class="text-xs text-zinc-500">Pending</p>
                <p class="text-xl font-bold text-yellow-600">{{ $stats['pending_count'] }}</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 rounded-xl p-4 shadow">
                <p class="text-xs text-zinc-500">Approved</p>
                <p class="text-xl font-bold text-green-600">{{ $stats['approved_count'] }}</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 rounded-xl p-4 shadow">
                <p class="text-xs text-zinc-500">Applied</p>
                <p class="text-xl font-bold text-blue-600">{{ $stats['applied_count'] }}</p>
            </div>
        </div>

        {{-- Filters + Table (same style as deductions/index) --}}
        {{-- You can reuse most of the deductions index table here --}}
    </div>
</x-layouts.app>