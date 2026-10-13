<x-layouts.app>
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Soft Delete Log Detail</h1>
            <a href="{{ route('soft-delete-logs.index') }}" class="text-sm text-zinc-500 hover:text-zinc-700">← Back</a>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-zinc-500">Model</p>
                    <p class="font-medium dark:text-white">{{ class_basename($softDeleteLog->model_type) }}</p>
                </div>
                <div>
                    <p class="text-zinc-500">Record ID</p>
                    <p class="font-medium dark:text-white">#{{ $softDeleteLog->model_id }}</p>
                </div>
                <div>
                    <p class="text-zinc-500">Deleted By</p>
                    <p class="font-medium dark:text-white">{{ $softDeleteLog->deleter?->name ?? 'Unknown' }}</p>
                </div>
                <div>
                    <p class="text-zinc-500">Deleted At</p>
                    <p class="font-medium dark:text-white">{{ $softDeleteLog->created_at->format('d M Y H:i:s') }}</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-zinc-500">Reason</p>
                    <p class="font-medium dark:text-white">{{ $softDeleteLog->reason ?? '—' }}</p>
                </div>
            </div>

            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <p class="text-sm font-medium mb-2 dark:text-white">Deleted Data Snapshot</p>
                <pre class="bg-zinc-100 dark:bg-zinc-800 rounded-lg p-4 text-xs overflow-x-auto text-zinc-700 dark:text-zinc-300">{{ json_encode($softDeleteLog->data, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    </div>
</x-layouts.app>