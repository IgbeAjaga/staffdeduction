<x-layouts.app>
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Soft Delete Logs</h1>
        <p class="text-sm text-zinc-500">Only visible to Superadmin</p>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow overflow-hidden">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Model</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Deleted By</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Reason</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-zinc-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($logs as $log)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 text-sm">{{ class_basename($log->model_type) }} #{{ $log->model_id }}</td>
                            <td class="px-4 py-3 text-sm">{{ $log->deleter?->name }}</td>
                            <td class="px-4 py-3 text-sm text-zinc-500">{{ $log->reason ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('soft-delete-logs.show', $log) }}" class="text-blue-600 text-sm">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-4 py-3">{{ $logs->links() }}</div>
        </div>
    </div>
</x-layouts.app>