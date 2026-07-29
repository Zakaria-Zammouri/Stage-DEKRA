<x-app-layout>

    <x-slot name="header">
        Liste des virements
    </x-slot>

    <style>
        .font-display{ font-family:'Space Grotesk',sans-serif; }
        .btn-orange{ background:#E8590C; color:white; padding:10px 18px; border-radius:10px; font-weight:600; transition:.2s; text-decoration:none; }
        .btn-orange:hover{ background:#c74c0a; color:white; }
        .btn-pdf{ background:#178754; color:white; padding:7px 14px; border-radius:8px; text-decoration:none; font-size:.85rem; font-weight:500; }
        .btn-pdf:hover{ color:white; background:#106640; }
        .btn-delete{ background:#dc3545; color:white; padding:7px 14px; border:none; border-radius:8px; cursor:pointer; font-size:.85rem; font-weight:500; }
        .btn-delete:hover{ background:#bb2d3b; }
        .btn-traite{ background:#16a34a; color:white; padding:7px 14px; border:none; border-radius:8px; cursor:pointer; font-size:.85rem; font-weight:500; }
        .btn-traite:hover{ background:#15803d; }
        .vers-row{ border-bottom:1px solid #F0EDE7; transition:background .15s; }
        .vers-row:last-child{ border-bottom:none; }
        .vers-row:hover{ background:#FAFAF8; }
        .dark .vers-row{ border-bottom-color:#374151; }
        .dark .vers-row:hover{ background:rgba(255,255,255,.03); }
    </style>

    <div class="py-8">

        <div class="max-w-7xl mx-auto px-6 space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-xl text-sm font-medium" style="background:#EAF6EF;color:#106640;border:1px solid #C9EAD9;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center">

                <h2 class="font-display text-2xl font-bold text-gray-900 dark:text-white">
                    Virements
                </h2>

                <a href="{{ route('virements.create') }}" class="btn-orange">
                    + Nouveau virement
                </a>

            </div>

            <!-- Tableau (desktop) -->
            <div class="hidden md:block bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #E4E0D8;">

                <table class="w-full text-sm">

                   <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-black font-semibold" style="background:#FAFAF8;">
                            <th class="px-6 py-3.5 font-semibold">Bénéficiaire</th>
                            <th class="px-6 py-3.5 font-semibold">Montant</th>
                            <th class="px-6 py-3.5 font-semibold">Date</th>
                            <th class="px-6 py-3.5 font-semibold">Statut</th>
                            <th class="px-6 py-3.5 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($virements as $virement)

                        <tr class="vers-row">

                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ $virement->beneficiaire }}
                            </td>

                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                {{ number_format($virement->montant,2,',',' ') }} DH
                            </td>

                            <td class="px-6 py-4 font-mono-data text-xs text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($virement->date_virement)->format('d/m/Y') }}
                            </td>

                            <td class="px-6 py-4">
                                @if($virement->statut === 'Traité')
                                    <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700">Traité</span>
                                @else
                                    <span class="px-2 py-1 rounded text-xs font-semibold bg-amber-100 text-amber-700">En attente</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">

                                    <a href="{{ route('virements.show',$virement) }}" class="btn-pdf">
                                        Voir
                                    </a>

                                    @if($virement->statut !== 'Traité')
                                        <form action="{{ route('virements.marquer-traite', $virement) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn-traite">
                                                Marquer traité
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('virements.destroy',$virement) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            class="btn-delete"
                                            onclick="return confirm('Supprimer ce virement ?')">
                                            Supprimer
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <p class="text-gray-400 dark:text-gray-500 text-sm">Aucun virement enregistré.</p>
                                <a href="{{ route('virements.create') }}" class="text-sm font-medium mt-2 inline-block" style="color:#178754;">Ajouter le premier virement</a>
                            </td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

            <!-- Cartes (mobile) -->
            <div class="md:hidden space-y-3">
                @forelse($virements as $virement)
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4" style="border:1px solid #E4E0D8;">

                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $virement->beneficiaire }}</span>
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ number_format($virement->montant,2,',',' ') }} DH</span>
                        </div>

                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-mono-data">
                                {{ \Carbon\Carbon::parse($virement->date_virement)->format('d/m/Y') }}
                            </p>

                            @if($virement->statut === 'Traité')
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700">Traité</span>
                            @else
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-amber-100 text-amber-700">En attente</span>
                            @endif
                        </div>

                        <div class="flex gap-2 flex-wrap">
                            <a href="{{ route('virements.show',$virement) }}" class="btn-pdf">Voir</a>

                            @if($virement->statut !== 'Traité')
                                <form action="{{ route('virements.marquer-traite', $virement) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn-traite">Marquer traité</button>
                                </form>
                            @endif

                            <form action="{{ route('virements.destroy',$virement) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn-delete" onclick="return confirm('Supprimer ce virement ?')">Supprimer</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-400 text-sm py-10">Aucun virement enregistré.</p>
                @endforelse
            </div>

        </div>

    </div>

</x-app-layout>