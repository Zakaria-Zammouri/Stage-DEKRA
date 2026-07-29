<x-app-layout>

    <x-slot name="header">
        Sauvegardes
    </x-slot>

    <div class="max-w-4xl mx-auto py-8 px-4">

        <h1 class="text-3xl font-bold mb-2">💾 Sauvegardes</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
            Une sauvegarde automatique de la base de données et des documents est effectuée chaque jour à 2h du matin.
            Les 10 dernières sauvegardes sont conservées.
        </p>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700">
                        <th class="px-4 py-3">Sauvegarde</th>
                        <th class="px-4 py-3">Taille</th>
                        <th class="px-4 py-3">Base de données</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sauvegardes as $sauvegarde)
                        <tr class="border-t border-gray-100 dark:border-gray-700">
                            <td class="px-4 py-3 font-mono text-xs">{{ $sauvegarde['nom'] }}</td>
                            <td class="px-4 py-3">{{ $sauvegarde['taille'] }} Mo</td>
                            <td class="px-4 py-3">
                                @if($sauvegarde['contient_sql'])
                                    <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700">Présente</span>
                                @else
                                    <span class="px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-700">Manquante</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-400">
                                Aucune sauvegarde effectuée pour le moment. Lance <code>php artisan backup:run</code> pour en créer une.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</x-app-layout>