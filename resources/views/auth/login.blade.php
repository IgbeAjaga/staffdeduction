<x-guest-layout>
    <!-- Success Message - Nice Green -->
    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center mb-4" role="alert" 
             style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 16px; border-radius: 8px;">
            <i data-feather="check-circle" style="margin-right: 12px; font-size: 22px;"></i>
            <div>
                <strong>Success!</strong><br>
                {{ session('success') }}
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Error Message -->
    @if (session('error'))
        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
            <i data-feather="alert-circle" style="margin-right: 12px;"></i>
            {{ session('error') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
<div class="mt-4">
    <x-input-label for="password" :value="__('Password')" />

    <div class="relative">
        <x-text-input
            id="password"
            class="block mt-1 w-full pr-10"
            type="password"
            name="password"
            required
            autocomplete="current-password"
        />

        <button
            type="button"
            id="togglePassword"
            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700"
        >
            <!-- Eye Icon -->
            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6.364 0c-1.73 4.039-5.523 7-9.364 7s-7.634-2.961-9.364-7c1.73-4.039 5.523-7 9.364-7s7.634 2.961 9.364 7z" />
            </svg>

            <!-- Eye Slash Icon -->
            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 hidden"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-3.841 0-7.634-2.961-9.364-7a10.522 10.522 0 012.887-4.36m3.087-2.06A9.956 9.956 0 0112 5c3.841 0 7.634 2.961 9.364 7a10.522 10.522 0 01-4.293 5.066M15 12a3 3 0 00-4.243-2.829M9.88 9.88L4 4m5.88 5.88L14.12 14.12M20 20L14.12 14.12" />
            </svg>
        </button>
    </div>

    <x-input-error :messages="$errors->get('password')" class="mt-2" />
</div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const password = document.getElementById('password');
    const toggle = document.getElementById('togglePassword');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');

    toggle.addEventListener('click', function () {

        const isPassword = password.getAttribute('type') === 'password';

        password.setAttribute(
            'type',
            isPassword ? 'text' : 'password'
        );

        eyeOpen.classList.toggle('hidden');
        eyeClosed.classList.toggle('hidden');
    });
});
</script>
