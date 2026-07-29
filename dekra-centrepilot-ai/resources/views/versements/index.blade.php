<x-app-layout>

    <x-slot name="header">
        Journal des versements
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 px-4">

        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">
                💰 Versements
            </h1>

            <a href="{{ route('versements.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">
                ➕ Nouveau versement
            </a>
        </div>

        {{-- Rapprochement facturé / versé --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total facturé ({{ $mois->translatedFormat('F Y') }})</p>
                <p class="text-2xl font-bold mt-1">{{ number_format($totalFacture, 2, ',', ' ') }} DH</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total versé ({{ $mois->translatedFormat('F Y') }})</p>
                <p class="text-2xl font-bold mt-1">{{ number_format($totalVerse, 2, ',', ' ') }} DH</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Écart</p>
                <p class="text-2xl font-bold mt-1 {{ $ecart > 0 ? 'text-red-600' : 'text-green-600' }}">
                    {{ number_format($ecart, 2, ',', ' ') }} DH
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    {{ $ecart > 0 ? 'Reste à encaisser' : ($ecart < 0 ? 'Versé en trop' : 'Soldé') }}
                </p>
            </div>

        </div>

        {{-- Filtres --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 mb-6">
            <form method="GET" action="{{ route('versements.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div>
                        <label class="block text-sm font-medium mb-2 dark:text-gray-300">🔍 Recherche</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Référence, client, notes..."
                               class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 dark:text-gray-300">📅 Mois</label>
                        <input type="month" name="mois" value="{{ request('mois', $mois->format('Y-m')) }}"
                               class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white px-5 py-2 rounded-lg">
                            Filtrer
                        </button>
                    </div>

                </div>
            </form>
        </div>

        {{-- Tableau --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700">
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Client / Origine</th>
                        <th class="px-4 py-3">Référence</th>
                        <th class="px-4 py-3">Mode de règlement</th>
                        <th class="px-4 py-3 text-right">Montant</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($versements as $versement)
                        <tr class="border-t border-gray-100 dark:border-gray-700">
                            <td class="px-4 py-3">{{ $versement->date_versement->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $versement->client ?: '—' }}</td>
                            <td class="px-4 py-3">{{ $versement->reference ?: '—' }}</td>
                            <td class="px-4 py-3">{{ $versement->mode_reglement }}</td>
                            <td class="px-4 py-3 text-right font-medium">{{ number_format($versement->montant, 2, ',', ' ') }} DH</td>
                           <td class="px-4 py-3 text-right">

    <div class="flex justify-end gap-2">

        <a href="{{ route('versements.show', $versement) }}"
           class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg w-24 transition">
            Voir
        </a>

        <a href="{{ route('versements.edit', $versement) }}"
           class="inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium px-4 py-2 rounded-lg w-24 transition">
            Modifier
        </a>

        <form action="{{ route('versements.destroy', $versement) }}"
              method="POST"
              onsubmit="return confirm('Supprimer ce versement ?');">

            @csrf
            @method('DELETE')

            <button type="submit"
                    class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg w-24 transition">
                Supprimer
            </button>

        </form>

    </div>

</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                Aucun versement enregistré pour cette période.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</x-app-layout>