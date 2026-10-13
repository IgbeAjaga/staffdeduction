<x-layouts.app>
    <div class="max-w-lg mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Edit Job Title</h1>
            <a href="{{ route('job-titles.index') }}" class="text-sm text-zinc-500 hover:text-zinc-700">← Back</a>
        </div>

        <form action="{{ route('job-titles.update', $jobTitle) }}" method="POST"
              class="bg-white dark:bg-zinc-900 rounded-xl shadow p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1 dark:text-zinc-300">Name *</label>
                <input type="text" name="name" value="{{ old('name', $jobTitle->name) }}" required
                       class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" id="is_active"
                       @checked(old('is_active', $jobTitle->is_active))
                       class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500">
                <label for="is_active" class="text-sm dark:text-zinc-300">Active</label>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('job-titles.index') }}" class="px-4 py-2 border rounded-lg text-sm dark:border-zinc-600">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
                    Update Job Title
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>