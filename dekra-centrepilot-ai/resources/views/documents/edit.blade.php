<x-app-layout>

    <x-slot name="header">
        Modifier le document
    </x-slot>

    <style>
        .font-display{font-family:'Space Grotesk',sans-serif;}
        .doc-form input, .doc-form textarea{
            border:1px solid #E4E0D8 !important;
            border-radius:.65rem !important;
            padding:.65rem .9rem !important;
        }
        .doc-form input:focus, .doc-form textarea:focus, .doc-form select:focus{
            border-color:#178754 !important;
            box-shadow:0 0 0 3px rgba(23,135,84,.12) !important;
            outline:none !important;
        }
        .doc-form label{font-weight:500;font-size:.875rem;color:#374151;margin-bottom:.4rem;display:block;}
        .dark .doc-form label{color:#D1D5DB;}

        /* Select toujours lisible, peu importe le mode clair/sombre */
       /* Select toujours lisible, peu importe le mode clair/sombre */
.doc-form select{
    border:1px solid #E4E0D8 !important;
    border-radius:.65rem !important;
    padding:.65rem .9rem !important;
    background-color:#ffffff !important;
    color:#111827 !important;
}
.doc-form select option{
    color:#111827;
}

        .btn-primary{background:#178754;font-family:'Space Grotesk',sans-serif;font-weight:600;border-radius:.65rem;transition:background .15s;}
        .btn-primary:hover{background:#106640;}
    </style>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-6">

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-8" style="border:1px solid #E4E0D8;">

                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:#FFF0E6;">
                        <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4 12.5-12.5z" stroke="#E8590C" stroke-width="1.6" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <h3 class="font-display font-semibold text-gray-900 dark:text-white">Modifier le document</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $document->titre }}</p>
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

                <form action="{{ route('documents.update', $document) }}" method="POST" class="doc-form space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label>Titre</label>
                            <input type="text" name="titre" value="{{ old('titre', $document->titre) }}" class="w-full dark:bg-gray-700 dark:text-white" required>
                        </div>
                        <div>
                            <label>Type</label>
                           <select name="type" class="w-full" required>

    <option value="" selected>Choisir...</option>

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
                        </div>
                    </div>

                    <div>
                        <label>Catégorie</label>
                        <input type="text" name="categorie" value="{{ old('categorie', $document->categorie) }}" class="w-full dark:bg-gray-700 dark:text-white" required>
                    </div>

                    <div>
                        <label>Description</label>
                        <textarea name="description" rows="4" class="w-full dark:bg-gray-700 dark:text-white">{{ old('description', $document->description) }}</textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="btn-primary text-white px-5 py-2.5 text-sm">
                            Enregistrer les modifications
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