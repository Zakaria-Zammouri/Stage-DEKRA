<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Assistant IA
        </h2>
    </x-slot>

    <div class="py-8">

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-8">

                <h1 class="text-3xl font-bold mb-3">
                    Assistant IA CentrePilot
                </h1>

                <p class="text-gray-600 dark:text-gray-300 mb-8">
                    Posez une question concernant vos documents enregistrés.
                </p>

                {{-- ============================================= --}}
                {{-- FORMULAIRE : POSER UNE QUESTION (existant) --}}
                {{-- ============================================= --}}
                <form method="POST" action="{{ route('assistant.ask') }}">

                    @csrf

                    <textarea
                        name="question"
                        rows="4"
                        required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                        placeholder="Exemple : Qui est le propriétaire de la Peugeot ?"></textarea>

                    <button
                        type="submit"
                        class="mt-5 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">

                        Poser la question

                    </button>

                </form>

                @isset($reponse)

                    <div class="mt-10">

                        <h2 class="text-xl font-bold mb-4">
                            Réponse de l'IA
                        </h2>

                        <div class="bg-green-100 border border-green-300 rounded-lg p-5 whitespace-pre-line">

                            {{ $reponse }}

                        </div>

                    </div>

                @endisset

                {{-- ============================================= --}}
                {{-- FORMULAIRE : ANALYSE DE DOCUMENT (nouveau) --}}
                {{-- ============================================= --}}
                <div class="mt-12 border-t pt-8">

                    <h2 class="text-2xl font-bold mb-3">
                        Analyse de document
                    </h2>

                    <p class="text-gray-600 dark:text-gray-300 mb-6">
                        Téléversez une facture ou un document administratif (PDF ou image) pour en extraire
                        automatiquement les informations importantes.
                    </p>

                    <form method="POST" action="{{ route('assistant.analyser-document') }}" enctype="multipart/form-data">

                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">
                                Type de document (optionnel)
                            </label>
                            <select name="type_document"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="">Détection automatique</option>
                                <option value="Facture">Facture</option>
                                <option value="Titre de propriété">Titre de propriété</option>
                                <option value="Virement">Virement</option>
                                <option value="Reçu">Reçu</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">
                                Fichier (PDF, JPG, PNG)
                            </label>
                            <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                        </div>

                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
                            Analyser le document
                        </button>

                    </form>

                    @isset($resultatAnalyse)

                        @if(!empty($resultatAnalyse['erreur']) && empty($resultatAnalyse['champs']))

                            <div class="mt-6 bg-red-100 border border-red-300 text-red-700 rounded-lg p-4">
                                {{ $resultatAnalyse['erreur'] }}
                            </div>

                        @else

                            <div class="mt-8">

                                <span class="font-bold text-blue-600">Type détecté :</span>
                                {{ $resultatAnalyse['type_document'] ?? 'Non déterminé' }}

                                <table class="w-full mt-4 border rounded-lg overflow-hidden">
                                    <tbody>
                                        @foreach($resultatAnalyse['champs'] ?? [] as $cle => $valeur)
                                            <tr class="border-b">
                                                <td class="px-4 py-2 font-medium bg-gray-50 dark:bg-gray-900 w-1/3">
                                                    {{ $cle }}
                                                </td>
                                                <td class="px-4 py-2">
                                                    {{ $valeur ?? '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>

                        @endif

                    @endisset

                </div>

                {{-- ============================================= --}}
                {{-- HISTORIQUE (questions ET documents) --}}
                {{-- ============================================= --}}
                @if(isset($historique) && $historique->count())

                    <div class="mt-12">

                        <h2 class="text-2xl font-bold mb-6">
                            Historique des conversations
                        </h2>

                        @foreach($historique as $item)

                            <div class="border rounded-xl p-5 mb-5">

                                @if($item->type === 'document')

                                    <div class="mb-3">

                                        <span class="font-bold text-purple-600">
                                            Document analysé
                                        </span>

                                        <p class="mt-2">
                                            {{ $item->fichier_nom }} — {{ $item->type_document ?? 'Type inconnu' }}
                                        </p>

                                    </div>

                                    @if($item->champs_extraits)
                                        <ul class="text-sm text-gray-700 dark:text-gray-300 list-disc list-inside">
                                            @foreach($item->champs_extraits as $cle => $valeur)
                                                <li><strong>{{ $cle }}</strong> : {{ $valeur ?? '—' }}</li>
                                            @endforeach
                                        </ul>
                                    @endif

                                @else

                                    <div class="mb-3">

                                        <span class="font-bold text-blue-600">
                                            Question
                                        </span>

                                        <p class="mt-2">
                                            {{ $item->question }}
                                        </p>

                                    </div>

                                    <div>

                                        <span class="font-bold text-green-600">
                                            Réponse
                                        </span>

                                        <p class="mt-2 whitespace-pre-line">
                                            {{ $item->reponse }}
                                        </p>

                                    </div>

                                @endif

                                <div class="text-sm text-gray-500 mt-3">

                                    {{ $item->created_at }}

                                </div>

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>

        </div>

    </div>

</x-app-layout>