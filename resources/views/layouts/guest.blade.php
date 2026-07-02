<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" x-init="$watch('darkMode', v => localStorage.setItem('theme', v ? 'dark' : 'light'))" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BRILink Rambo') }} - Login</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-body text-on-background antialiased bg-background transition-colors duration-300">
        
        <!-- Toggle Dark Mode (Optional for Guest) -->
        <div class="absolute top-4 right-4">
            <button @click="darkMode = !darkMode" class="p-2 rounded-full bg-surface-container text-on-surface-variant hover:text-primary hover:bg-surface-container-high transition-colors focus:outline-none" :title="darkMode ? 'Mode Terang' : 'Mode Gelap'">
                <span class="material-symbols-outlined" x-text="darkMode ? 'light_mode' : 'dark_mode'">dark_mode</span>
            </button>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div>
                <a href="/">
                    <div class="w-20 h-20 bg-primary text-on-primary rounded-full flex items-center justify-center font-bold text-3xl shadow-lg">
                        BR
                    </div>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-surface border custom-border shadow-sm overflow-hidden sm:rounded-xl transition-colors duration-300">
                <div class="mb-6 text-center">
                    <h2 class="font-headline-md text-headline-md font-bold text-primary dark:text-primary-fixed-dim">Sistem Akuntansi</h2>
                    <p class="font-body text-body text-on-surface-variant">Agen BRILink Rambo</p>
                </div>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
