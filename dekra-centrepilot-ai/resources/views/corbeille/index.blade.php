<x-app-layout>

    <x-slot name="header">
        Corbeille
    </x-slot>

    <div class="max-w-6xl mx-auto py-8 px-4">

        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <h1 class="text-3xl font-bold mb-2">🗑️ Corbeille</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
            Les éléments supprimés restent ici. Tu peux les restaurer ou les supprimer définitivement (action irréversible).
        </p>

        @php
            $sections = [
                'facture' => ['label' => 'Factures', 'items' => $invoices, 'champ' => 'numero'],
                'virement' => ['label' => 'Virements', 'items' => $virements, 'champ' => 'beneficiaire'],
                'versement' => ['label' => 'Versements', 'items' => $versements, 'champ' => 'client'],
                'document' => ['label' => 'Documents', 'items' => $documents, 'champ' => 'titre'],
                'rappel' => ['label' => 'Rappels', 'items' => $rappels, 'champ' => 'titre'],
            ];
        @endphp

        @foreach($sections as $type => $section)

            @if($section['items']->count() > 0)

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden mb-6">

                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 font-semibold text-sm">
                        {{ $section['label'] }} ({{ $section['items']->count() }})
                    </div>

                    <table class="w-full text-sm">
                        <tbody>
                            @foreach($section['items'] as $item)
                                <tr class="border-t border-gray-100 dark:border-gray-700">
                                    <td class="px-4 py-3">{{ $item->{$section['champ']} ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                        Supprimé le {{ $item->deleted_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-right space-x-2">

                                        <form action="{{ route('corbeille.restore', [$type, $item->id]) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold">
                                                Restaurer
                                            </button>
                                        </form>

                                        <form action="{{ route('corbeille.force-delete', [$type, $item->id]) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Suppression définitive et irréversible. Continuer ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold">
                                                Supprimer définitivement
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

            @endif

        @endforeach

        @if($invoices->isEmpty() && $virements->isEmpty() && $versements->isEmpty() && $documents->isEmpty() && $rappels->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-10 text-center text-gray-400">
                La corbeille est vide.
            </div>
        @endif

    </div>

</x-app-layout>