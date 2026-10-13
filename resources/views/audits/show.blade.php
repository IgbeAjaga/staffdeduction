<x-layouts.app>
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Audit Trail</h1>
                <p class="text-sm text-zinc-500">{{ $deduction->deduction_id }} — {{ $deduction->staff_name }}</p>
            </div>
            <a href="{{ route('deductions.show', $deduction) }}" class="text-sm text-zinc-500 hover:text-zinc-700">← Back to Deduction</a>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow p-6">
            <div class="space-y-6">
                @forelse($audits as $audit)
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                            @if(!$loop->last)
                                <div class="w-0.5 flex-1 bg-zinc-200 dark:bg-zinc-700 my-1"></div>
                            @endif
                        </div>
                        <div class="pb-6">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold dark:text-white">{{ ucfirst($audit->action) }}</span>
                                <span class="text-xs text-zinc-500">by {{ $audit->user?->name ?? 'System' }}</span>
                            </div>
                            <p class="text-xs text-zinc-500 mt-0.5">{{ $audit->created_at->format('d M Y • H:i:s') }}</p>
                            @if($audit->description)
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-2">{{ $audit->description }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-zinc-500 text-center py-8">No audit records found for this deduction.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>