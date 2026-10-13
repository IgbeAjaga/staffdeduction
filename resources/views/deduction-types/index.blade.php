<x-layouts.app>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Deduction Types</h1>
            <a href="{{ route('deduction-types.create') }}"
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
                + New Type
            </a>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow overflow-hidden">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-zinc-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($deductionTypes as $type)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-4 py-3 font-medium dark:text-white">
                                {{ $type->name }}
                                @if($type->trashed())
                                    <span class="ml-2 text-xs text-red-500">(Deleted)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $type->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300' }}">
                                    {{ $type->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-3">
                                @if($type->trashed())
                                    <form action="{{ route('deduction-types.restore', $type->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 text-sm">Restore</button>
                                    </form>
                                @else
                                    <a href="{{ route('deduction-types.edit', $type) }}" class="text-blue-600 hover:text-blue-800 text-sm">Edit</a>
                                    <form action="{{ route('deduction-types.destroy', $type) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Delete this deduction type?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10 text-center text-zinc-500">No deduction types found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($deductionTypes->hasPages())
                <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700">
                    {{ $deductionTypes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>