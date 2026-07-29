<x-app-layout>

    <x-slot name="header">
        Modifier le versement
    </x-slot>

    <div class="max-w-2xl mx-auto py-8 px-4">

        <h1 class="text-2xl font-bold mb-6">✏️ Modifier le versement</h1>

        @if(session('alertes_coherence'))
            <div class="bg-amber-50 border border-amber-300 text-amber-800 p-4 rounded-lg mb-5 text-sm">
                <p class="font-semibold mb-2">⚠️ Contrôle de cohérence</p>
                <ul class="list-disc list-inside space-y-1">
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

        <form method="POST" action="{{ route('versements.update', $versement) }}"
              class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-5">
            @csrf
            @method('PUT')

            <input type="hidden" name="confirmer_malgre_alertes" id="confirmer_malgre_alertes" value="0">

            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">Client / Origine du paiement</label>
                <input type="text" name="client" value="{{ old('client', $versement->client) }}"
                       class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                @error('client') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">Référence (n° chèque, n° virement...)</label>
                <input type="text" name="reference" value="{{ old('reference', $versement->reference) }}"
                       class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                @error('reference') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Montant (DH)</label>
                    <input type="number" step="0.01" min="0" name="montant"
                           value="{{ old('montant', $versement->montant) }}" required
                           class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('montant') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">Date du versement</label>
                    <input type="date" name="date_versement"
                           value="{{ old('date_versement', $versement->date_versement->format('Y-m-d')) }}" required
                           class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('date_versement') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">Mode de règlement</label>
                <select name="mode_reglement" required
                        class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @foreach(['Espèces', 'Chèque', 'Virement', 'Carte bancaire'] as $mode)
                        <option value="{{ $mode }}" {{ old('mode_reglement', $versement->mode_reglement) === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                    @endforeach
                </select>
                @error('mode_reglement') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">Notes</label>
                <textarea name="notes" rows="3"
                          class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes', $versement->notes) }}</textarea>
                @error('notes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('versements.index') }}"
                   class="px-5 py-2 rounded-lg border dark:border-gray-600 dark:text-gray-300">Annuler</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">
                    Enregistrer les modifications
                </button>
            </div>

        </form>

    </div>

    <script>
        const caseConfirmation = document.getElementById('confirmer_malgre_alertes_visuel');
        if (caseConfirmation) {
            caseConfirmation.addEventListener('change', function () {
                document.getElementById('confirmer_malgre_alertes').value = this.checked ? '1' : '0';
            });
        }
    </script>

</x-app-layout>