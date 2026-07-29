<x-app-layout>

    <x-slot name="header">
        Détails du document
    </x-slot>

    <style>
        .font-display{font-family:'Space Grotesk',sans-serif;}
        .font-mono-data{font-family:'JetBrains Mono',monospace;}
        .info-row{border-bottom:1px solid #F0EDE7;padding:.9rem 0;}
        .info-row:last-child{border-bottom:none;}
        .dark .info-row{border-color:#374151;}
        .btn-primary{background:#178754;font-family:'Space Grotesk',sans-serif;font-weight:600;border-radius:.65rem;transition:background .15s;}
        .btn-primary:hover{background:#106640;}
    </style>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-6">

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-8" style="border:1px solid #E4E0D8;">

                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-lg flex items-center justify-center shrink-0" style="background:#EEF1FB;">
                            <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none"><path d="M6 3h9l5 5v13H6V3z" stroke="#16233A" stroke-width="1.6" stroke-linejoin="round"/></svg>
                        </div>
                        <div>
                            <h3 class="font-display text-xl font-semibold text-gray-900 dark:text-white">{{ $document->titre }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-mono-data">Ajouté le {{ \Carbon\Carbon::parse($document->date_ajout)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    <div class="info-row flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Type</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $document->type }}</span>
                    </div>
                    <div class="info-row flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Catégorie</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $document->categorie }}</span>
                    </div>
                 @if($document->description)
                    <div class="info-row text-sm">
                        <span class="text-gray-500 dark:text-gray-400 block mb-1">Description</span>
                        <span class="text-gray-900 dark:text-white">{{ $document->description }}</span>
                    </div>
                    @endif

                    @if($document->nom_proprietaire || $document->immatriculation || $document->marque || $document->modele)
                    <div class="info-row text-sm">
                        <span class="text-gray-500 dark:text-gray-400 block mb-2">Champs extraits (véhicule)</span>
                        <div class="grid grid-cols-2 gap-2 text-gray-900 dark:text-white">
                            @if($document->nom_proprietaire)
                                <span>👤 {{ $document->nom_proprietaire }}</span>
                            @endif
                            @if($document->immatriculation)
                                <span>🚗 {{ $document->immatriculation }}</span>
                            @endif
                            @if($document->marque)
                                <span>🏷️ {{ $document->marque }}</span>
                            @endif
                            @if($document->modele)
                                <span>⚙️ {{ $document->modele }}</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                @if($document->texte_ocr)
                <div class="mt-8">
                    <h4 class="font-display font-semibold text-gray-900 dark:text-white text-sm mb-3">
                        📄 Texte extrait par OCR
                    </h4>
                    <div class="rounded-xl p-4 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line max-h-64 overflow-y-auto"
                         style="border:1px solid #E4E0D8; background:#FAFAF8;">
                        {{ $document->texte_ocr }}
                    </div>
                </div>
                @endif

                <!-- Aperçu du fichier -->
                @if($document->chemin_fichier)
                    <div class="mt-8">
                        <h4 class="font-display font-semibold text-gray-900 dark:text-white text-sm mb-4">Aperçu du document</h4>

                        @php
                            $extension = strtolower(pathinfo($document->chemin_fichier, PATHINFO_EXTENSION));
                            $isPdf = $extension === 'pdf';
                            $isImage = in_array($extension, ['jpg','jpeg','png','gif','webp']);
                        @endphp

                        @if($isPdf)
                            <iframe
                                src="{{ asset('storage/'.$document->chemin_fichier) }}"
                                width="100%"
                                height="600"
                                class="rounded-xl"
                                style="border:1px solid #E4E0D8;">
                            </iframe>
                        @elseif($isImage)
                            <img src="{{ asset('storage/'.$document->chemin_fichier) }}" alt="{{ $document->titre }}" class="rounded-xl w-full" style="border:1px solid #E4E0D8;">
                        @else
                            <div class="rounded-xl p-8 text-center text-sm text-gray-500 dark:text-gray-400" style="border:1px dashed #E4E0D8;">
                                Aperçu non disponible pour ce type de fichier (.{{ $extension }}) — ouvre-le directement.
                            </div>
                        @endif
                    </div>
                @endif

                <div class="flex gap-3 mt-8">
                    <a href="{{ asset('storage/'.$document->chemin_fichier) }}" target="_blank" class="btn-primary text-white px-5 py-2.5 text-sm inline-flex items-center gap-2">
                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"><path d="M12 3v12m0 0l-4-4m4 4l4-4M5 21h14" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Ouvrir le fichier
                    </a>
                    <a href="{{ route('documents.index') }}" class="px-5 py-2.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Retour
                    </a>
                </div>

            </div>

        </div>
    </div>

</x-app-layout>