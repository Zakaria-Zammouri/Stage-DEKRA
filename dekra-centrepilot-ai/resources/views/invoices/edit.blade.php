<x-app-layout>

    <div class="max-w-3xl mx-auto py-8">

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-8">

            <h1 class="text-2xl font-bold mb-6">
                ✏️ Modifier la facture
            </h1>

            @if ($errors->any())

                <div class="bg-red-100 border border-red-300 text-red-700 p-4 rounded mb-5">

                    <ul class="list-disc ml-5">

                        @foreach($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif

            @if(session('alertes_coherence'))
                <div class="bg-amber-50 border border-amber-300 text-amber-800 p-4 rounded mb-5 text-sm">
                    <p class="font-semibold mb-2">⚠️ Contrôle de cohérence</p>
                    <ul class="list-disc ml-5 space-y-1">
                        @foreach(session('alertes_coherence') as $alerte)
                            <li>{{ $alerte }}</li>
                        @endforeach
                    </ul>
                    <label class="flex items-center gap-2 mt-4 font-medium">
                        <input type="checkbox" id="confirmer_malgre_alertes_visuel" style="width:18px;height:18px;min-width:18px;padding:0;border:1px solid #9CA3AF;accent-color:#E8590C;">
                        <span>Je confirme ces informations malgré l'alerte ci-dessus</span>
                    </label>
                </div>
            @endif

            <form action="{{ route('invoices.update', $invoice) }}" method="POST">

                @csrf
                @method('PUT')

                <input type="hidden" name="confirmer_malgre_alertes" id="confirmer_malgre_alertes" value="0">

                <div class="mb-5">

                    <label class="block mb-2 font-semibold dark:text-gray-300">
                        Numéro de facture
                    </label>

                    <input
                        type="text"
                        value="{{ $invoice->numero }}"
                        class="w-full border rounded-lg px-4 py-2 bg-gray-100 dark:bg-gray-600 dark:border-gray-600 dark:text-gray-300"
                        readonly>

                </div>

                <div class="mb-5">

                    <label class="block mb-2 font-semibold dark:text-gray-300">
                        Client
                    </label>

                    <input
                        type="text"
                        name="client"
                        value="{{ old('client', $invoice->client) }}"
                        class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        required>

                </div>

                <div class="mb-5">

                    <label class="block mb-2 font-semibold dark:text-gray-300">
                        Objet
                    </label>

                    <input
                        type="text"
                        name="objet"
                        value="{{ old('objet', $invoice->objet) }}"
                        class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        required>

                </div>

                <div class="mb-5">

                    <label class="block mb-2 font-semibold dark:text-gray-300">
                        Type de véhicule
                    </label>

                    <select
                        name="type_vehicule"
                        id="type_vehicule"
                        class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        required>
                        <option value="Voiture" {{ old('type_vehicule', $invoice->type_vehicule) === 'Voiture' ? 'selected' : '' }}>Voiture</option>
                        <option value="Moto" {{ old('type_vehicule', $invoice->type_vehicule) === 'Moto' ? 'selected' : '' }}>Moto</option>
                        <option value="Poids lourd" {{ old('type_vehicule', $invoice->type_vehicule) === 'Poids lourd' ? 'selected' : '' }}>Poids lourd</option>
                    </select>

                </div>

                <div class="grid grid-cols-2 gap-5">

                    <div>

                        <label class="block mb-2 font-semibold dark:text-gray-300">
                            Montant HT
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="montant_ht"
                            id="montant_ht"
                            value="{{ old('montant_ht', $invoice->montant_ht) }}"
                            class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required>

                    </div>

                    <div>

                        <label class="block mb-2 font-semibold dark:text-gray-300">
                            TVA <span class="text-xs font-normal text-gray-400">(calculée automatiquement, modifiable)</span>
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="tva"
                            id="tva"
                            value="{{ old('tva', $invoice->tva) }}"
                            class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required>

                    </div>

                </div>

                <div class="mt-5">

                    <label class="block mb-2 font-semibold dark:text-gray-300">
                        Date de facture
                    </label>

                    <input
                        type="date"
                        name="date_facture"
                        value="{{ old('date_facture', $invoice->date_facture->format('Y-m-d')) }}"
                        class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        required>

                </div>

                <div class="mt-5">

                    <label class="block mb-2 font-semibold dark:text-gray-300">
                        Statut
                    </label>

                    <select
                        name="statut"
                        class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

                        <option value="Non payée"
                            {{ $invoice->statut == 'Non payée' ? 'selected' : '' }}>
                            Non payée
                        </option>

                        <option value="Payée"
                            {{ $invoice->statut == 'Payée' ? 'selected' : '' }}>
                            Payée
                        </option>

                    </select>

                </div>

                <div class="mt-8 flex gap-3">

                    <button
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">

                        Enregistrer les modifications

                    </button>

                    <a href="{{ route('invoices.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                        Retour
                    </a>

                </div>

            </form>

        </div>

    </div>

    <script>
        const tvaRates = @json($rates);

        function calculerTVA() {
            const type = document.getElementById('type_vehicule').value;
            const montantHt = parseFloat(document.getElementById('montant_ht').value) || 0;
            const taux = parseFloat(tvaRates[type]) || 0;

            const tva = (montantHt * taux) / 100;
            document.getElementById('tva').value = tva.toFixed(2);
        }

        document.getElementById('type_vehicule').addEventListener('change', calculerTVA);
        document.getElementById('montant_ht').addEventListener('input', calculerTVA);

        const caseConfirmation = document.getElementById('confirmer_malgre_alertes_visuel');
        if (caseConfirmation) {
            caseConfirmation.addEventListener('change', function () {
                document.getElementById('confirmer_malgre_alertes').value = this.checked ? '1' : '0';
            });
        }
    </script>

</x-app-layout>