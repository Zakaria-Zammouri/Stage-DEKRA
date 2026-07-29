<x-app-layout>

    <x-slot name="header">
        Ajouter un document
    </x-slot>

    <style>
        .font-display{font-family:'Space Grotesk',sans-serif;}
        .doc-form input, .doc-form textarea{
            border:1px solid #E4E0D8 !important;
            border-radius:.65rem !important;
            padding:.65rem .9rem !important;
        }
        .doc-form input:focus, .doc-form textarea:focus, .doc-form select:focus{
            border-color:#E8590C !important;
            box-shadow:0 0 0 3px rgba(232,89,12,.12) !important;
            outline:none !important;
        }
        .doc-form label{font-weight:500;font-size:.875rem;color:#374151;margin-bottom:.4rem;display:block;}
        .dark .doc-form label{color:#D1D5DB;}

        /* Champs toujours lisibles, peu importe le mode clair/sombre */
        .doc-form input[type="text"],
        .doc-form input[type="file"],
        .doc-form textarea,
        .doc-form select{
            background-color:#ffffff !important;
            color:#111827 !important;
        }
        .doc-form select option{
            color:#111827;
        }
        .doc-form input::placeholder,
        .doc-form textarea::placeholder{
            color:#9CA3AF;
        }

        .btn-primary{background:#E8590C;font-family:'Space Grotesk',sans-serif;font-weight:600;border-radius:.65rem;transition:background .15s;}
        .btn-primary:hover{background:#C64A0A;}
    </style>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-6">

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-8" style="border:1px solid #E4E0D8;">

                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:#FFF0E6;">
                        <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none"><path d="M12 5v14M5 12h14" stroke="#E8590C" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </div>
                    <div>
                        <h3 class="font-display font-semibold text-gray-900 dark:text-white">Nouveau document</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Le fichier est requis, le reste peut être proposé par l'IA</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="mb-5 p-4 rounded-lg bg-red-50 text-red-700 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="doc-form space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label>Titre</label>
                            <input type="text" name="titre" value="{{ old('titre') }}" class="w-full" placeholder="Laisser vide pour une détection auto">
                        </div>
                        <div>
                            <label>Type</label>
                            <select name="type" class="w-full" required>

                                <option value="" selected>Choisir...</option>

                                <option value="Auto">
                                    🤖 Détection automatique (IA)
                                </option>

                                <option value="Titre de propriété">
                                    Titre de propriété
                                </option>

                                <option value="Facture">
                                    Facture
                                </option>

                                <option value="Reçu">
                                    Reçu
                                </option>

                                <option value="Virement">
                                    Virement
                                </option>

                                <option value="Autre">
                                    Autre
                                </option>

                            </select>
                            <p class="text-xs text-gray-400 mt-1">Avec "Détection automatique", l'IA lit le document et choisit le type, la catégorie et le titre à ta place — modifiables ensuite.</p>
                        </div>
                    </div>

                    <div>
                        <label>Catégorie</label>
                        <input type="text" name="categorie" value="{{ old('categorie') }}" class="w-full" placeholder="Laisser vide pour une détection auto">
                    </div>

                    <div>
                        <label>Description</label>
                        <textarea name="description" rows="4" class="w-full">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label>Fichier</label>
                        <input type="file" name="fichier" class="w-full" required>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="btn-primary text-white px-5 py-2.5">
                            Enregistrer
                        </button>
                        <a href="{{ route('documents.index') }}" class="px-5 py-2.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            Annuler
                        </a>
                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>