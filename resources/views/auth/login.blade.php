<!DOCTYPE html>
<html lang="id" class="h-full" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" x-init="$watch('darkMode', v => localStorage.setItem('theme', v ? 'dark' : 'light'))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — BRILink Rambo</title>
    <meta name="description" content="Sistem Informasi Akuntansi Agen BRILink Rambo">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-background transition-colors duration-300 flex flex-col items-center justify-center font-body text-body text-on-surface p-4">

    <!-- Toggle Dark Mode -->
    <div class="absolute top-4 right-4">
        <button @click="darkMode = !darkMode" class="p-2 rounded-full bg-surface-container text-on-surface-variant hover:text-primary hover:bg-surface-container-high transition-colors focus:outline-none" :title="darkMode ? 'Mode Terang' : 'Mode Gelap'">
            <span class="material-symbols-outlined" x-text="darkMode ? 'light_mode' : 'dark_mode'">dark_mode</span>
        </button>
    </div>

    <main class="w-full max-w-md px-margin_lg">
        <div class="bg-surface-container-lowest border border-outline-variant rounded-lg p-margin_lg flex flex-col">
            <div class="text-center mb-margin_lg">
                <h1 class="font-headline-lg text-headline-lg text-primary">BRILink Rambo</h1>
                <p class="font-body text-body text-on-surface-variant mt-margin_sm">Sistem Informasi Akuntansi</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-margin_md p-3 bg-success-dim border border-success-text/30 text-success-text rounded text-sm font-body">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-margin_md">
                @csrf

                <!-- Email -->
                <div class="flex flex-col gap-margin_sm">
                    <label class="font-label text-label text-on-surface" for="email">Alamat Email</label>
                    <input class="px-3 py-2 border @error('email') border-error @else border-outline-variant @enderror rounded input-focus font-body text-body text-on-surface bg-surface-container-lowest outline-none transition-colors" 
                           id="email" type="email" name="email" value="{{ old('email') }}"
                           placeholder="owner@brilink-rambo.id" required autofocus autocomplete="email"/>
                    @error('email')
                        <p class="text-xs text-error font-body mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="flex flex-col gap-margin_sm" x-data="{ showPassword: false }">
                    <div class="flex justify-between items-center">
                        <label class="font-label text-label text-on-surface" for="password">Password</label>
                        @if (Route::has('password.request'))
                            <a class="font-label text-label text-primary hover:underline" href="{{ route('password.request') }}">
                                Lupa password?
                            </a>
                        @endif
                    </div>
                    <div class="relative flex items-center">
                        <input class="w-full px-3 py-2 pr-10 border @error('password') border-error @else border-outline-variant @enderror rounded input-focus font-body text-body text-on-surface bg-surface-container-lowest outline-none transition-colors" 
                               id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password"
                               placeholder="••••••••"/>
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3 text-on-surface-variant hover:text-primary transition-colors focus:outline-none flex items-center justify-center">
                            <span class="material-symbols-outlined text-[20px]" x-text="showPassword ? 'visibility_off' : 'visibility'">visibility</span>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-error font-body mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input id="remember_me" type="checkbox" name="remember"
                               class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-0 focus:ring-offset-0 bg-surface-container-lowest">
                        <span class="font-label text-label text-on-surface-variant">Ingat saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button class="mt-margin_sm bg-primary text-on-primary rounded-lg py-2.5 px-4 font-body-semibold text-body-semibold hover:opacity-90 transition-opacity flex justify-center items-center" type="submit">
                    Masuk
                </button>
            </form>
        </div>

        <!-- Footer note -->
        <p class="text-center text-on-surface-variant text-xs mt-margin_lg opacity-75 font-body">
            Sistem internal Agen BRILink Rambo &copy; {{ date('Y') }}
        </p>
    </main>

</body>
</html>

