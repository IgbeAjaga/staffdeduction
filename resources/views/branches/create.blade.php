{{-- create.blade.php --}}
<x-layouts.app>
    <div class="max-w-lg mx-auto space-y-6">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">New Branch</h1>

        <form action="{{ route('branches.store') }}" method="POST" class="bg-white dark:bg-zinc-900 rounded-xl shadow p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1 dark:text-zinc-300">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" checked class="rounded">
                <label for="is_active" class="text-sm dark:text-zinc-300">Active</label>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('branches.index') }}" class="px-4 py-2 border rounded-lg text-sm">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm">Create</button>
            </div>
        </form>
    </div>
</x-layouts.app>