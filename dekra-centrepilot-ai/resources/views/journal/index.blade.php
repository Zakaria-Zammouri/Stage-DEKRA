<x-app-layout>

    <x-slot name="header">
        Journal des actions
    </x-slot>

    <div class="max-w-6xl mx-auto py-8 px-4">

        <h1 class="text-3xl font-bold mb-2">📋 Journal des actions</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
            Historique de toutes les créations, modifications, suppressions et restaurations.
        </p>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 mb-6">
            <form method="GET" action="{{ route('journal.index') }}" class="flex flex-wrap gap-4 items-end">

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Module</label>
                    <select name="module" class="border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Tous</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}" {{ request('module') === $module ? 'selected' : '' }}>
                                {{ $module }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Action</label>
                    <select name="action" class="border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Toutes</option>
                        <option value="créé" {{ request('action') === 'créé' ? 'selected' : '' }}>Créé</option>
                        <option value="modifié" {{ request('action') === 'modifié' ? 'selected' : '' }}>Modifié</option>
                        <option value="supprimé (corbeille)" {{ request('action') === 'supprimé (corbeille)' ? 'selected' : '' }}>Supprimé (corbeille)</option>
                        <option value="supprimé définitivement" {{ request('action') === 'supprimé définitivement' ? 'selected' : '' }}>Supprimé définitivement</option>
                        <option value="restauré" {{ request('action') === 'restauré' ? 'selected' : '' }}>Restauré</option>
                    </select>
                </div>

                <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white px-5 py-2 rounded-lg">
                    Filtrer
                </button>

               <a href="{{ route('journal.index') }}"
                   class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 px-5 py-2 rounded-lg font-semibold">
                    Réinitialiser
                </a>

            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700">
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Utilisateur</th>
                        <th class="px-4 py-3">Action</th>
                        <th class="px-4 py-3">Module</th>
                        <th class="px-4 py-3">Élément</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="border-t border-gray-100 dark:border-gray-700">
                            <td class="px-4 py-3">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $log->user->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span @class([
                                    'px-2 py-1 rounded text-xs font-semibold',
                                    'bg-green-100 text-green-700' => $log->action === 'créé',
                                    'bg-blue-100 text-blue-700' => $log->action === 'modifié',
                                    'bg-amber-100 text-amber-700' => $log->action === 'supprimé (corbeille)',
                                    'bg-red-100 text-red-700' => $log->action === 'supprimé définitivement',
                                    'bg-purple-100 text-purple-700' => $log->action === 'restauré',
                                ])>
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $log->module }}</td>
                            <td class="px-4 py-3">{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                Aucune action enregistrée pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $logs->links() }}
        </div>

    </div>

</x-app-layout>