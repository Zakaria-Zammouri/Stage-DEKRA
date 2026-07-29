<x-app-layout>

    <x-slot name="header">
        Gestion des documents
    </x-slot>

    <style>
        .font-display{font-family:'Space Grotesk',sans-serif;}
        .font-mono-data{font-family:'JetBrains Mono',monospace;}
        .doc-row{border-bottom:1px solid #F0EDE7;transition:background .15s;}
        .doc-row:last-child{border-bottom:none;}
        .doc-row:hover{background:#FAFAF8;}
        .dark .doc-row:hover{background:rgba(255,255,255,.03);}
        .btn-primary{background:#178754;font-family:'Space Grotesk',sans-serif;font-weight:600;border-radius:.65rem;transition:background .15s;}
        .btn-primary:hover{background:#106640;}
        .search-input{border:1px solid #E4E0D8;border-radius:.65rem;padding:.6rem .9rem .6rem 2.5rem;}
        .search-input:focus{border-color:#178754;box-shadow:0 0 0 3px rgba(23,135,84,.12);outline:none;}
        .badge{font-size:.7rem;font-weight:600;padding:.2rem .55rem;border-radius:9999px;letter-spacing:.02em;}
        .icon-btn{width:2rem;height:2rem;display:inline-flex;align-items:center;justify-content:center;border-radius:.5rem;transition:background .15s;}
        .icon-btn:hover{background:#F0EDE7;}
        .dark .icon-btn:hover{background:rgba(255,255,255,.08);}
    </style>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-6 space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-xl text-sm font-medium" style="background:#EAF6EF;color:#106640;border:1px solid #C9EAD9;">
                    {{ session('success') }}
                </div>
            @endif

            <!-- En-tête + actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="font-display text-xl font-semibold text-gray-900 dark:text-white">
                        {{ $documents->count() }} document(s)
                    </h1>
                </div>

                <a href="{{ route('documents.create') }}" class="btn-primary text-white px-5 py-2.5 text-sm inline-flex items-center gap-2 justify-center">
                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"><path d="M12 5v14M5 12h14" stroke="white" stroke-width="1.8" stroke-linecap="round"/></svg>
                    Ajouter un document
                </a>
            </div>

            <!-- Recherche (serveur) -->
            <form action="{{ route('documents.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1 max-w-md">
                    <svg viewBox="0 0 24 24" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="M21 21l-4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <input type="text" name="recherche" value="{{ $recherche ?? request('recherche') }}" placeholder="Rechercher par titre, type ou catégorie..." class="search-input w-full text-sm bg-white dark:bg-gray-800 dark:text-white">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn-primary text-white px-5 py-2.5 text-sm">Rechercher</button>
                    <a href="{{ route('documents.index') }}" class="px-5 py-2.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition" style="border:1px solid #E4E0D8;">Réinitialiser</a>
                </div>
            </form>

            <!-- Tableau (desktop) -->
            <div class="hidden md:block bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #E4E0D8;">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-black font-semibold" style="background:#FAFAF8;">
                            <th class="px-6 py-3.5 font-semibold">Titre</th>
                            <th class="px-6 py-3.5 font-semibold">Type</th>
                            <th class="px-6 py-3.5 font-semibold">Catégorie</th>
                            <th class="px-6 py-3.5 font-semibold">Date d'ajout</th>
                            <th class="px-6 py-3.5 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $document)
                            <tr class="doc-row">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background:#EEF1FB;">
                                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"><path d="M6 3h9l5 5v13H6V3z" stroke="#16233A" stroke-width="1.6" stroke-linejoin="round"/></svg>
                                        </div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $document->titre }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="badge" style="background:#EAF6EF;color:#106640;">{{ $document->type }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $document->categorie }}</td>
                                <td class="px-6 py-4 font-mono-data text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($document->date_ajout)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('documents.show', $document) }}" class="icon-btn" title="Voir">
                                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z" stroke="#374151" stroke-width="1.6"/><circle cx="12" cy="12" r="3" stroke="#374151" stroke-width="1.6"/></svg>
                                        </a>
                                        <a href="{{ route('documents.edit', $document) }}" class="icon-btn" title="Modifier">
                                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4 12.5-12.5z" stroke="#374151" stroke-width="1.6" stroke-linejoin="round"/></svg>
                                        </a>
                                        <form action="{{ route('documents.destroy', $document) }}" method="POST" onsubmit="return confirm('Supprimer ce document ?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="icon-btn" title="Supprimer">
                                                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"><path d="M4 7h16M9 7V4h6v3m-8 0l1 13h8l1-13" stroke="#DC2626" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <p class="text-gray-400 dark:text-gray-500 text-sm">Aucun document trouvé.</p>
                                    <a href="{{ route('documents.create') }}" class="text-sm font-medium mt-2 inline-block" style="color:#178754;">Ajouter le premier document</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Cartes (mobile) -->
            <div class="md:hidden space-y-3">
                @forelse($documents as $document)
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4" style="border:1px solid #E4E0D8;">
                        <a href="{{ route('documents.show', $document) }}" class="flex items-center gap-3 mb-2">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0" style="background:#EEF1FB;">
                                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"><path d="M6 3h9l5 5v13H6V3z" stroke="#16233A" stroke-width="1.6" stroke-linejoin="round"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white text-sm truncate">{{ $document->titre }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-mono-data">{{ \Carbon\Carbon::parse($document->date_ajout)->format('d/m/Y') }}</p>
                            </div>
                        </a>
                        <div class="flex items-center justify-between">
                            <span class="badge" style="background:#EAF6EF;color:#106640;">{{ $document->type }}</span>
                            <div class="flex gap-1">
                                <a href="{{ route('documents.edit', $document) }}" class="icon-btn" title="Modifier">
                                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4 12.5-12.5z" stroke="#374151" stroke-width="1.6" stroke-linejoin="round"/></svg>
                                </a>
                                <form action="{{ route('documents.destroy', $document) }}" method="POST" onsubmit="return confirm('Supprimer ce document ?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="icon-btn" title="Supprimer">
                                        <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none"><path d="M4 7h16M9 7V4h6v3m-8 0l1 13h8l1-13" stroke="#DC2626" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-400 text-sm py-10">Aucun document trouvé.</p>
                @endforelse
            </div>

        </div>
    </div>

</x-app-layout>