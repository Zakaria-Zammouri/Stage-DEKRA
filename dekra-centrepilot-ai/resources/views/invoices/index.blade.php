<x-app-layout>

    <div class="max-w-7xl mx-auto py-8">

        @if(session('success'))

            <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg mb-6">

                {{ session('success') }}

            </div>

        @endif

        <div class="flex justify-between items-center mb-6">

            <h1 class="text-3xl font-bold">
                📄 Gestion des factures
            </h1>

            <a href="{{ route('invoices.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">

                ➕ Nouvelle facture

            </a>

        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 mb-6">

    <form method="GET" action="{{ route('invoices.index') }}">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <div>

                <label class="block text-sm font-medium mb-2 dark:text-gray-300">
                    🔍 Recherche
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Numéro, client ou objet..."
                    class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">

            </div>

            <div>

                <label class="block text-sm font-medium mb-2 dark:text-gray-300">
                    📌 Statut
                </label>

                <select
                    name="statut"
                    class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

                    <option value="">
                        Tous
                    </option>

                    <option value="Payée"
                        {{ request('statut') == 'Payée' ? 'selected' : '' }}>
                        Payée
                    </option>

                    <option value="Non payée"
                        {{ request('statut') == 'Non payée' ? 'selected' : '' }}>
                        Non payée
                    </option>

                </select>

            </div>

            <div class="flex items-end">

                <button
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg w-full">

                    Rechercher

                </button>

            </div>

            <div class="flex items-end">

                <a
                    href="{{ route('invoices.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg w-full text-center">

                    Réinitialiser

                </a>

            </div>

        </div>

    </form>

</div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">

            <table class="min-w-full">

                <thead class="bg-gray-100 dark:bg-gray-700">

                    <tr>

                        <th class="px-4 py-3 text-left dark:text-gray-200">N° Facture</th>

                        <th class="px-4 py-3 text-left dark:text-gray-200">Client</th>

                        <th class="px-4 py-3 text-left dark:text-gray-200">Objet</th>

                        <th class="px-4 py-3 text-left dark:text-gray-200">Montant TTC</th>

                        <th class="px-4 py-3 text-left dark:text-gray-200">Statut</th>

                        <th class="px-4 py-3 text-left dark:text-gray-200">Date</th>

                        <th class="px-4 py-3 text-center dark:text-gray-200">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($invoices as $invoice)

                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">

                            <td class="px-4 py-3 font-semibold dark:text-white">

                                {{ $invoice->numero }}

                            </td>

                            <td class="px-4 py-3 dark:text-gray-200">

                                {{ $invoice->client }}

                            </td>

                            <td class="px-4 py-3 dark:text-gray-200">

                                {{ $invoice->objet }}

                            </td>

                            <td class="px-4 py-3 font-semibold dark:text-white">

                                {{ number_format($invoice->montant_ttc,2,',',' ') }} DH

                            </td>

                            <td class="px-4 py-3">

                                @if($invoice->statut == 'Payée')

                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">

                                        ✅ Payée

                                    </span>

                                @else

                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">

                                        ❌ Non payée

                                    </span>

                                @endif

                            </td>

                            <td class="px-4 py-3 dark:text-gray-200">

                                {{ $invoice->date_facture->format('d/m/Y') }}

                            </td>

                            <td class="px-4 py-3">

                                <div class="flex justify-center gap-2">

                                    <a href="{{ route('invoices.show',$invoice) }}"
                                       class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">

                                        Voir

                                    </a>

                                    <a href="{{ route('invoices.edit',$invoice) }}"
                                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">

                                        Modifier

                                    </a>

                                    <form action="{{ route('invoices.destroy',$invoice) }}"
                                          method="POST"
                                          onsubmit="return confirm('Supprimer cette facture ?')">

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">

                                            Supprimer

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7"
                                class="text-center py-8 text-gray-500 dark:text-gray-400">

                                Aucune facture enregistrée.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-app-layout>