<x-app-layout>

    <x-slot name="header">
        Détail du versement
    </x-slot>

    <div class="max-w-2xl mx-auto py-8 px-4">

        <h1 class="text-2xl font-bold mb-6">💰 Détail du versement</h1>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Client / Origine</p>
                    <p class="font-medium">{{ $versement->client ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Référence</p>
                    <p class="font-medium">{{ $versement->reference ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Montant</p>
                    <p class="font-medium">{{ number_format($versement->montant, 2, ',', ' ') }} DH</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Date du versement</p>
                    <p class="font-medium">{{ $versement->date_versement->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Mode de règlement</p>
                    <p class="font-medium">{{ $versement->mode_reglement }}</p>
                </div>
            </div>

            @if($versement->notes)
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Notes</p>
                    <p class="font-medium whitespace-pre-line">{{ $versement->notes }}</p>
                </div>
            @endif

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('versements.index') }}" class="px-5 py-2 rounded-lg border dark:border-gray-600 dark:text-gray-300">
                    Retour
                </a>
                <a href="{{ route('versements.edit', $versement) }}" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2 rounded-lg">
                    Modifier
                </a>
            </div>

        </div>

    </div>

</x-app-layout>