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

                @if(isset($historique) && $historique->count())

                    <div class="mt-12">

                        <h2 class="text-2xl font-bold mb-6">
                            Historique des conversations
                        </h2>

                        @foreach($historique as $item)

                            <div class="border rounded-xl p-5 mb-5">

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