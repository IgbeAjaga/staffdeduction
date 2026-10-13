<x-layouts.app>
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Audit Trails</h1>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow overflow-hidden">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Deduction</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach($audits as $audit)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ $audit->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 text-sm font-mono">{{ $audit->deduction?->deduction_id }}</td>
                            <td class="px-4 py-3 text-sm">{{ ucfirst($audit->action) }}</td>
                            <td class="px-4 py-3 text-sm">{{ $audit->user?->name }}</td>
                            <td class="px-4 py-3 text-sm text-zinc-500">{{ $audit->description }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-4 py-3">{{ $audits->links() }}</div>
        </div>
    </div>
</x-layouts.app>