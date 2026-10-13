<x-layouts.app>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Branches</h1>
            <a href="{{ route('branches.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">+ New Branch</a>
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
                    @forelse($branches as $branch)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-4 py-3 font-medium dark:text-white">
                                {{ $branch->name }}
                                @if($branch->trashed())
                                    <span class="ml-2 text-xs text-red-500">(Deleted)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $branch->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-zinc-100 text-zinc-600' }}">
                                    {{ $branch->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                @if($branch->trashed())
                                    <form action="{{ route('branches.restore', $branch->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="text-green-600 text-sm">Restore</button>
                                    </form>
                                @else
                                    <a href="{{ route('branches.edit', $branch) }}" class="text-blue-600 text-sm">Edit</a>
                                    <form action="{{ route('branches.destroy', $branch) }}" method="POST" class="inline" onsubmit="return confirm('Delete this branch?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 text-sm">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-zinc-500">No branches found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($branches->hasPages())
                <div class="px-4 py-3 border-t dark:border-zinc-700">{{ $branches->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app>