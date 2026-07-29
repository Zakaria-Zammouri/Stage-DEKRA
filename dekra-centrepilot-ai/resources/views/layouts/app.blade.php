<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'InspectFlow IA') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:500,600,700|inter:400,500,600|jetbrains-mono:400,500" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root{
            --accent:#E8590C;
            --accent-dark:#C64A0A;
            --navy:#16233A;
            --paper:#F7F5F1;
            --line:#E4E0D8;
        }
        .font-display{font-family:'Space Grotesk',sans-serif;}
        .font-mono-data{font-family:'JetBrains Mono',monospace;}
        body{font-family:'Inter',sans-serif;}
    </style>
</head>

<body class="antialiased transition-colors duration-300" style="background:var(--paper);">

<div class="min-h-screen flex dark:bg-gray-900" x-data="{ sidebarOpen: false }">

    <!-- Overlay mobile -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/40 z-30 lg:hidden"
         style="display:none;">
    </div>

    <!-- Sidebar -->
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed lg:sticky top-0 inset-y-0 left-0 z-40 w-64 h-screen shrink-0 transform transition-transform duration-200 ease-in-out
               bg-white dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700 flex flex-col">
        @include('layouts.navigation')
    </aside>

    <!-- Contenu principal -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- Barre du haut -->
        <div class="h-16 flex items-center justify-between px-4 sm:px-6 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">

            <button @click="sidebarOpen = true" class="lg:hidden inline-flex items-center justify-center p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300 transition">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div class="flex-1"></div>

            <button
                id="theme-toggle"
                class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600
                       text-xl rounded-lg px-3 py-1.5 transition">
                🌙
            </button>
        </div>

        @isset($header)
            <header class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 transition-colors duration-300">
                <div class="py-6 px-6 flex justify-between items-center">
                    <div class="font-display text-2xl font-semibold" style="color:var(--navy);">
                        <span class="dark:text-white">{{ $header }}</span>
                    </div>
                </div>
            </header>
        @endisset

        <main class="flex-1 text-gray-900 dark:text-white transition-colors duration-300">
            {{ $slot }}
        </main>

    </div>

</div>

<script>
const html=document.documentElement;
const button=document.getElementById("theme-toggle");

if(localStorage.getItem("theme")==="dark"){
    html.classList.add("dark");
    if(button) button.innerHTML="☀️";
}else{
    if(button) button.innerHTML="🌙";
}

if(button){
button.addEventListener("click",()=>{
    html.classList.toggle("dark");
    if(html.classList.contains("dark")){
        localStorage.setItem("theme","dark");
        button.innerHTML="☀️";
    }else{
        localStorage.setItem("theme","light");
        button.innerHTML="🌙";
    }
});
}
</script>

</body>
</html>