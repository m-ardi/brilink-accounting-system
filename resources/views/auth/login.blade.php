<!DOCTYPE html>
<html lang="id" class="h-full" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" x-init="$watch('darkMode', v => localStorage.setItem('theme', v ? 'dark' : 'light'))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — BRILink Rambo</title>
    <meta name="description" content="Sistem Informasi Akuntansi Agen BRILink Rambo">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-background transition-colors duration-300 flex flex-col items-center justify-center p-4">

    <!-- Background pattern -->
    <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none" style="background-image: radial-gradient(circle at 2px 2px, currentColor 1px, transparent 0); background-size: 32px 32px; color: var(--color-on-background)"></div>

    <!-- Toggle Dark Mode -->
    <div class="absolute top-4 right-4">
        <button @click="darkMode = !darkMode" class="p-2 rounded-full bg-surface text-on-surface-variant hover:text-primary transition-colors focus:outline-none shadow-sm" :title="darkMode ? 'Mode Terang' : 'Mode Gelap'">
            <span class="material-symbols-outlined" x-text="darkMode ? 'light_mode' : 'dark_mode'">dark_mode</span>
        </button>
    </div>

    <div class="relative w-full max-w-sm">

        <!-- Card -->
        <div class="bg-surface border custom-border rounded-2xl shadow-xl overflow-hidden transition-colors duration-300">

            <!-- Header -->
            <div class="bg-primary px-8 py-7 text-center transition-colors duration-300">
                <div class="w-14 h-14 bg-surface rounded-2xl mx-auto mb-3 flex items-center justify-center shadow-md">
                    <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/>
                    </svg>
                </div>
                <h1 class="text-on-primary font-bold text-xl">BRILink Rambo</h1>
                <p class="text-on-primary opacity-80 text-xs mt-1">Sistem Informasi Akuntansi</p>
            </div>

            <!-- Form -->
            <div class="px-8 py-7">
                <h2 class="text-on-surface font-semibold text-lg mb-1">Masuk ke Sistem</h2>
                <p class="text-on-surface-variant text-xs mb-6">Dr. Ratulangi Kilometer 5</p>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 p-3 bg-success-dim border border-success-text/30 text-success-text rounded-xl text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-on-surface mb-1.5">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            class="w-full px-4 py-2.5 bg-surface-container-low border input-border rounded-xl text-sm text-on-surface input-focus transition-all duration-200 outline-none"
                            placeholder="owner@brilink-rambo.id" required autofocus autocomplete="email">
                        @error('email')
                            <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-5">
                        <label for="password" class="block text-sm font-medium text-on-surface mb-1.5">Password</label>
                        <input id="password" type="password" name="password"
                            class="w-full px-4 py-2.5 bg-surface-container-low border input-border rounded-xl text-sm text-on-surface input-focus transition-all duration-200 outline-none"
                            placeholder="••••••••" required autocomplete="current-password">
                        @error('password')
                            <p class="mt-1.5 text-xs text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="w-4 h-4 rounded border-outline text-primary focus:ring-primary focus:ring-opacity-50">
                            <span class="text-sm text-on-surface-variant">Ingat saya</span>
                        </label>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                        class="w-full btn-primary py-2.5 rounded-xl shadow-sm hover:shadow-md text-sm transition-all duration-200">
                        Masuk
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer note -->
        <p class="text-center text-on-surface-variant text-xs mt-5 opacity-75">
            Sistem internal Agen BRILink Rambo &copy; {{ date('Y') }}
        </p>
    </div>

</body>
</html>
