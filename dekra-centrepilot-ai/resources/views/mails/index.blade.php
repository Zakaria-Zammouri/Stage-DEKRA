<x-app-layout>

    <x-slot name="header">
        Historique des mails
    </x-slot>

    <div class="max-w-6xl mx-auto py-8 px-4">

        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">📧 Mails</h1>

            <a href="{{ route('mails.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">
                ✉️ Nouveau mail
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 mb-6">
            <form method="GET" action="{{ route('mails.index') }}" class="flex gap-4">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Rechercher par destinataire ou objet..."
                       class="flex-1 border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white px-5 py-2 rounded-lg">
                    Rechercher
                </button>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700">
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Destinataire</th>
                        <th class="px-4 py-3">Objet</th>
                        <th class="px-4 py-3">Modèle</th>
                        <th class="px-4 py-3">Statut</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($emails as $email)
                        <tr class="border-t border-gray-100 dark:border-gray-700">
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($email->date_generation)->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $email->destinataire }}</td>
                            <td class="px-4 py-3">{{ $email->objet }}</td>
                            <td class="px-4 py-3">{{ $email->type_modele }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $email->statut_envoi === 'Envoyé' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $email->statut_envoi }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
<a href="{{ route('mails.show', $email) }}"
   class="inline-flex items-center justify-center px-4 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition">
    Voir
</a>                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                Aucun mail envoyé pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</x-app-layout>