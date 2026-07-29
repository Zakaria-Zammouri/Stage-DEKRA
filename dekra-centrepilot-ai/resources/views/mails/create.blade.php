<x-app-layout>

    <x-slot name="header">
        Nouveau mail
    </x-slot>

    <div class="max-w-2xl mx-auto py-8 px-4">

        <h1 class="text-2xl font-bold mb-6">✉️ Composer un mail</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-300 text-red-700 p-4 rounded mb-5">
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('mails.store') }}"
              class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">Modèle</label>
                <select id="type_modele" name="type_modele"
                        class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="Libre">Libre (rédaction manuelle)</option>
                    <option value="Confirmation RDV modifié">Confirmation de rendez-vous modifié</option>
                    <option value="Annulation RDV">Annulation de rendez-vous</option>
                    <option value="Relance">Relance</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">Destinataire (email)</label>
                <input type="email" name="destinataire" value="{{ old('destinataire') }}" required
                       class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">Objet</label>
                <input type="text" id="objet" name="objet" value="{{ old('objet') }}" required
                       class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">Contexte du dossier (pour l'IA)</label>
                <input type="text" id="contexte" placeholder="Ex : Client Ahmed Benali, nouveau RDV le 15/07/2026 à 10h"
                       class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">
                    Contenu du message
                    <button type="button" id="btn-generer-ia"
                            class="ml-2 text-xs bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded-lg">
                        🤖 Générer avec l'IA
                    </button>
                    <span id="ia-loading" class="text-xs text-gray-400 hidden">Génération en cours...</span>
                </label>
                <textarea id="contenu" name="contenu" rows="8" required
                          class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('contenu') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Le texte est pré-rempli selon le modèle choisi (ou généré par l'IA), toujours modifiable avant l'envoi.</p>
            </div>

          <div>
                <label class="block text-sm font-medium mb-2 dark:text-gray-300">Pièce jointe (optionnel)</label>
                <select name="piece_jointe"
                        class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">— Aucune —</option>

                    @if($invoices->count())
                        <optgroup label="Factures">
                            @foreach($invoices as $invoice)
                                <option value="facture:{{ $invoice->id }}">
                                    Facture {{ $invoice->numero }} — {{ $invoice->client }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endif

                    @if($virements->count())
                        <optgroup label="Virements (PV)">
                            @foreach($virements as $virement)
                                <option value="virement:{{ $virement->id }}">
                                    Virement {{ $virement->numero }} — {{ $virement->beneficiaire }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endif

                    @if($documents->count())
                        <optgroup label="Documents">
                            @foreach($documents as $document)
                                <option value="document:{{ $document->id }}">
                                    {{ $document->titre }} ({{ $document->type }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('mails.index') }}"
                   class="px-5 py-2 rounded-lg border dark:border-gray-600 dark:text-gray-300">Annuler</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">
                    Envoyer le mail
                </button>
            </div>

        </form>

    </div>

    <script>
        const modeles = {
            'Libre': '',
            'Confirmation RDV modifié': {
                objet: 'Confirmation de votre rendez-vous modifié',
                contenu: "Bonjour,\n\nNous vous confirmons que votre rendez-vous de contrôle technique a bien été modifié.\n\nMerci de vous présenter à la nouvelle date convenue muni des documents habituels.\n\nCordialement,\nContrôle Automobile et Expertise — DEKRA"
            },
            'Annulation RDV': {
                objet: 'Annulation de votre rendez-vous',
                contenu: "Bonjour,\n\nNous vous informons que votre rendez-vous de contrôle technique a été annulé.\n\nN'hésitez pas à nous contacter pour reprogrammer une nouvelle date.\n\nCordialement,\nContrôle Automobile et Expertise — DEKRA"
            },
            'Relance': {
                objet: 'Relance — Contrôle technique',
                contenu: "Bonjour,\n\nNous vous rappelons que votre véhicule doit passer son contrôle technique prochainement.\n\nMerci de prendre rendez-vous dans les meilleurs délais.\n\nCordialement,\nContrôle Automobile et Expertise — DEKRA"
            }
        };

        document.getElementById('type_modele').addEventListener('change', function () {
            const modele = modeles[this.value];
            if (modele) {
                document.getElementById('objet').value = modele.objet;
                document.getElementById('contenu').value = modele.contenu;
            }
        });
    </script>

    <script>
        document.getElementById('btn-generer-ia').addEventListener('click', async function () {

            const bouton = this;
            const loading = document.getElementById('ia-loading');
            const typeModele = document.getElementById('type_modele').value;
            const destinataire = document.querySelector('[name="destinataire"]').value;
            const contexte = document.getElementById('contexte').value;

            bouton.disabled = true;
            loading.classList.remove('hidden');

            try {
                const response = await fetch("{{ route('mails.generer-ia') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        type_modele: typeModele,
                        destinataire: destinataire,
                        contexte: contexte,
                    }),
                });

                const data = await response.json();

                if (data.objet) {
                    document.getElementById('objet').value = data.objet;
                }
                if (data.corps) {
                    document.getElementById('contenu').value = data.corps;
                }
                if (data.erreur) {
                    alert(data.erreur);
                }

            } catch (e) {
                alert("Erreur lors de la génération par l'IA.");
            } finally {
                bouton.disabled = false;
                loading.classList.add('hidden');
            }
        });
    </script>

</x-app-layout>