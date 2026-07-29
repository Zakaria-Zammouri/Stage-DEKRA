<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'DEKRA CentrePilot AI') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:500,600,700|inter:400,500,600|jetbrains-mono:400,500" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root{
            --accent:#178754;
            --accent-dark:#106640;
            --navy:#16233A;
            --navy-light:#223350;
            --paper:#F7F5F1;
            --line:#E4E0D8;
        }
        .font-display{font-family:'Space Grotesk',sans-serif;}
        .font-mono-data{font-family:'JetBrains Mono',monospace;}

        .brand-panel{
            background:radial-gradient(circle at 20% 20%, var(--navy-light), var(--navy) 70%);
            background-image:
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px),
                radial-gradient(circle at 20% 20%, var(--navy-light), var(--navy) 70%);
            background-size:32px 32px, 32px 32px, 100% 100%;
        }
        .stamp{
            border:2px solid var(--accent);
            border-radius:9999px;
            transform:rotate(-8deg);
        }
        .auth-card input:not([type=checkbox]){
            font-family:'Inter',sans-serif;
            border:1px solid var(--line) !important;
            border-radius:.65rem !important;
            padding:.65rem .9rem !important;
            transition:border-color .15s, box-shadow .15s;
        }
        .auth-card input:focus{
            border-color:var(--accent) !important;
            box-shadow:0 0 0 3px rgba(23,135,84,.15) !important;
            outline:none !important;
        }
        .auth-card button[type=submit]{
            background:var(--accent) !important;
            font-family:'Space Grotesk',sans-serif;
            font-weight:600;
            letter-spacing:.01em;
            border-radius:.65rem !important;
            transition:background .15s, transform .1s;
        }
        .auth-card button[type=submit]:hover{background:var(--accent-dark) !important;}
        .auth-card button[type=submit]:active{transform:scale(.98);}
        .auth-card a.text-accent{color:var(--accent);}
        .auth-card a.text-accent:hover{color:var(--accent-dark);}
    </style>
</head>
<body class="antialiased" style="background:var(--paper);">

    <div class="min-h-screen flex">

        <!-- Panneau gauche : identité -->
        <div class="brand-panel hidden lg:flex lg:w-1/2 flex-col justify-between p-12 text-white relative overflow-hidden">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:var(--accent);">
                    <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5"><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6l7-3z" stroke="white" stroke-width="1.8" stroke-linejoin="round"/></svg>
                </span>
                <span class="font-display font-semibold text-lg tracking-tight">InspectFlow IA</span>
            </div>

            <div class="max-w-sm">
                <div class="stamp w-24 h-24 flex items-center justify-center mb-8">
                    <svg viewBox="0 0 24 24" class="w-10 h-10" fill="none"><path d="M5 12.5l4.5 4.5L19 7" stroke="#178754" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h1 class="font-display text-3xl font-semibold leading-tight mb-4">
                    Gestion Administrative pour un centre de visite technique.
                </h1>
            </div>

          <p class="font-mono-data text-xs text-white/30">© {{ date('Y') }} InspectFlow IA</p>
        </div>

        <!-- Panneau droit : formulaire -->
        <div class="flex-1 flex items-center justify-center p-6 sm:p-10">
            <div class="w-full max-w-sm">

                <div class="lg:hidden flex items-center gap-3 mb-8">
                    <span class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:var(--accent);">
                        <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5"><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6l7-3z" stroke="white" stroke-width="1.8" stroke-linejoin="round"/></svg>
                    </span>
                    <span class="font-display font-semibold text-lg" style="color:var(--navy);">InspectFlow IA</span>
                </div>

                <div class="auth-card bg-white p-8 rounded-2xl shadow-sm" style="border:1px solid var(--line);">
                    {{ $slot }}
                </div>

            </div>
        </div>

    </div>

</body>
</html>