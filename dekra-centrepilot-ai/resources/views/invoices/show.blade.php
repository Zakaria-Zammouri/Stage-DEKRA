<x-app-layout>

    <div class="max-w-4xl mx-auto py-8">

        <div class="bg-white dark:bg-gray-800 shadow rounded-xl">

            <div class="border-b dark:border-gray-700 px-8 py-6 flex justify-between items-center">

                <h1 class="text-2xl font-bold">

                    📄 Détails de la facture

                </h1>

                <span class="text-gray-500 dark:text-gray-400 font-semibold">

                    {{ $invoice->numero }}

                </span>

            </div>

            <div class="p-8">

                <div class="grid grid-cols-2 gap-6">

                    <div>

                        <label class="text-gray-500 dark:text-gray-400 text-sm">
                            Client
                        </label>

                        <p class="font-semibold text-lg">

                            {{ $invoice->client }}

                        </p>

                    </div>

                    <div>

                        <label class="text-gray-500 dark:text-gray-400 text-sm">
                            Date de facture
                        </label>

                        <p class="font-semibold text-lg">

                            {{ $invoice->date_facture->format('d/m/Y') }}

                        </p>

                    </div>

                    <div class="col-span-2">

                        <label class="text-gray-500 dark:text-gray-400 text-sm">
                            Objet
                        </label>

                        <p class="font-semibold text-lg">

                            {{ $invoice->objet }}

                        </p>

                    </div>

                    <div>

                        <label class="text-gray-500 dark:text-gray-400 text-sm">
                            Montant HT
                        </label>

                        <p class="font-semibold text-lg">

                            {{ number_format($invoice->montant_ht,2,',',' ') }} DH

                        </p>

                    </div>

                    <div>

                        <label class="text-gray-500 dark:text-gray-400 text-sm">
                            TVA
                        </label>

                        <p class="font-semibold text-lg">

                            {{ number_format($invoice->tva,2,',',' ') }} DH

                        </p>

                    </div>

                    <div>

                        <label class="text-gray-500 dark:text-gray-400 text-sm">
                            Montant TTC
                        </label>

                        <p class="font-bold text-2xl text-blue-700">

                            {{ number_format($invoice->montant_ttc,2,',',' ') }} DH

                        </p>

                    </div>

                    <div>

                        <label class="text-gray-500 dark:text-gray-400 text-sm">
                            Statut
                        </label>

                        <p>

                            @if($invoice->statut == 'Payée')

                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full">

                                    ✅ Payée

                                </span>

                            @else

                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full">

                                    ❌ Non payée

                                </span>

                            @endif

                        </p>

                    </div>

                </div>

            </div>

            <div class="border-t dark:border-gray-700 px-8 py-6 flex gap-3">

                <a href="{{ route('invoices.edit',$invoice) }}"
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded-lg">

                    ✏️ Modifier

                </a>

                <a href="{{ route('invoices.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg">

                    ⬅ Retour

                </a>
                <a href="{{ route('invoices.pdf', $invoice) }}"
   class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg">

    📄 Télécharger PDF

</a>

            </div>

        </div>

    </div>

</x-app-layout>