<x-app-layout>

    <x-slot name="header">
        Détail du mail
    </x-slot>

    <div class="max-w-2xl mx-auto py-8 px-4">

        <h1 class="text-2xl font-bold mb-6">📧 Détail du mail</h1>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Destinataire</p>
                    <p class="font-medium">{{ $email->destinataire }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Modèle utilisé</p>
                    <p class="font-medium">{{ $email->type_modele }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Date d'envoi</p>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($email->date_generation)->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Statut</p>
                    <p class="font-medium">{{ $email->statut_envoi }}</p>
                </div>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Objet</p>
                <p class="font-medium">{{ $email->objet }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Contenu</p>
                <p class="font-medium whitespace-pre-line">{{ $email->contenu }}</p>
            </div>

            @if($email->invoice || $email->virement || $email->document)
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Pièce jointe</p>

                    <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-3">

                        <span class="font-medium">
                            @if($email->invoice)
                                📄 Facture {{ $email->invoice->numero }} — {{ $email->invoice->client }}
                            @elseif($email->virement)
                                📄 Virement {{ $email->virement->numero }} — {{ $email->virement->beneficiaire }}
                            @elseif($email->document)
                                📄 {{ $email->document->titre }}
                            @endif
                        </span>

                        <a href="{{ route('mails.piece-jointe', $email) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                            ⬇️ Télécharger
                        </a>

                    </div>
                </div>
            @endif

            <div class="flex justify-end pt-4">
                <a href="{{ route('mails.index') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg font-semibold transition">
                    Retour
                </a>
            </div>

        </div>

    </div>

</x-app-layout>