<!-- En-tête sidebar -->
<div class="h-16 flex items-center justify-between px-5 border-b border-gray-100 dark:border-gray-700 shrink-0">

    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
        <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:#178754;">
            <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4"><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6l7-3z" stroke="white" stroke-width="1.8" stroke-linejoin="round"/></svg>
        </span>
        <span class="font-display font-semibold text-lg" style="color:#16233A;">
          <span class="dark:text-white">InspectFlow IA</span>
        </span>
    </a>

    <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-gray-600 dark:hover:text-white">
        <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>

<!-- Liens de navigation -->
<nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"/>
        </svg>
        Tableau de bord
    </x-nav-link>

    <x-nav-link :href="route('documents.index')" :active="request()->routeIs('documents.*')">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
            <path d="M14 2v6h6"/>
        </svg>
        Documents
    </x-nav-link>

    <x-nav-link :href="route('assistant.index')" :active="request()->routeIs('assistant.*')">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2a2 2 0 012 2v2h1a3 3 0 013 3v2a3 3 0 01-3 3H9a3 3 0 01-3-3v-2a3 3 0 013-3h1V4a2 2 0 012-2z"/>
            <path d="M9 12v6a3 3 0 003 3 3 3 0 003-3v-6"/>
        </svg>
        Assistant IA
    </x-nav-link>

    <x-nav-link :href="route('virements.index')" :active="request()->routeIs('virements.*')">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
        </svg>
        Virements
    </x-nav-link>
    <x-nav-link :href="route('tva.index')" :active="request()->routeIs('tva.*')">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 7h6m-6 4h6m-6 4h4M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
        </svg>
        TVA
    </x-nav-link>
    <x-nav-link :href="route('versements.index')" :active="request()->routeIs('versements.*')">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 10h18M7 15h1m4 0h1M5 6h14a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"/>
        </svg>
        Versements
    </x-nav-link>
    <x-nav-link :href="route('invoices.index')" :active="request()->routeIs('invoices.*')">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2a2 2 0 012 2v2h1a3 3 0 013 3v2a3 3 0 01-3 3H9a3 3 0 01-3-3v-2a3 3 0 013-3h1V4a2 2 0 012-2z"/>
            <path d="M9 12v6a3 3 0 003 3 3 3 0 003-3v-6"/>
        </svg>
        Factures
    </x-nav-link>
    <x-nav-link :href="route('rappels.index')" :active="request()->routeIs('rappels.*')">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2a2 2 0 012 2v2h1a3 3 0 013 3v2a3 3 0 01-3 3H9a3 3 0 01-3-3v-2a3 3 0 013-3h1V4a2 2 0 012-2z"/>
            <path d="M9 12v6a3 3 0 003 3 3 3 0 003-3v-6"/>
        </svg>
        Rappels
    </x-nav-link>
    <x-nav-link :href="route('mails.index')" :active="request()->routeIs('mails.*')">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        Mails
    </x-nav-link>
    <x-nav-link :href="route('corbeille.index')" :active="request()->routeIs('corbeille.*')">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/>
        </svg>
        Corbeille
    </x-nav-link>
    <x-nav-link :href="route('journal.index')" :active="request()->routeIs('journal.*')">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Journal
    </x-nav-link>
    <x-nav-link :href="route('backups.index')" :active="request()->routeIs('backups.*')">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 8h14M5 8a2 2 0 01-2-2V4a2 2 0 012-2h14a2 2 0 012 2v2a2 2 0 01-2 2M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
        </svg>
        Sauvegardes
    </x-nav-link>

</nav>

<!-- Bas de sidebar : utilisateur -->
<div class="border-t border-gray-100 dark:border-gray-700 p-3 shrink-0">

    <x-dropdown align="right" width="56" direction="up">

        <x-slot name="trigger">
            <button
                class="w-full inline-flex items-center gap-3 px-3 py-2.5 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition text-sm font-medium">

                <span class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-semibold shrink-0" style="background:#16233A;">
                    {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                </span>

                <span class="flex-1 text-left truncate">{{ Auth::user()->name }}</span>

                <svg class="h-4 w-4 fill-current shrink-0" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l5 5a1 1 0 01-1.414 1.414L10 5.414 5.707 9.707a1 1 0 01-1.414-1.414l5-5A1 1 0 0110 3zm-5.707 9.293a1 1 0 011.414 0L10 16.586l4.293-4.293a1 1 0 011.414 1.414l-5 5a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </x-slot>

        <x-slot name="content">
            <x-dropdown-link :href="route('profile.edit')">Profil</x-dropdown-link>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                    Déconnexion
                </x-dropdown-link>
            </form>
        </x-slot>

    </x-dropdown>

</div>