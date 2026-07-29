<x-app-layout>

    <style>
        .font-display {
            font-family: 'Space Grotesk', sans-serif;
        }

        .stat-card {
            border: 1px solid #E4E0D8;
            transition: .2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,.08);
        }

        .quick-link {
            border: 1px solid #E4E0D8;
            transition: .2s;
        }

        .quick-link:hover {
            border-color: #E8590C;
            background: #FFF7F2;
        }

        .table-row:hover {
            background: #fafafa;
        }

        .icon-badge {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }

        .icon-badge svg {
            width: 22px;
            height: 22px;
        }
    </style>

    <div class="py-8">

        <div class="max-w-7xl mx-auto px-6 space-y-8">

            <!-- Bienvenue -->

            <div class="rounded-2xl p-8 text-white"
                style="background:linear-gradient(120deg,#16233A,#223350);">

                <h1 class="font-display text-3xl font-bold mb-2">
                    Bienvenue {{ Auth::user()->name }}
                </h1>

            </div>

           <!-- Statistiques -->

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

                <!-- Documents -->

                <a href="{{ route('documents.index') }}" class="stat-card bg-white rounded-xl p-6 block">

                    <div class="icon-badge" style="background:#EFF6FF;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <path d="M14 2v6h6"/>
                            <line x1="8" y1="13" x2="16" y2="13"/>
                            <line x1="8" y1="17" x2="16" y2="17"/>
                        </svg>
                    </div>

                    <div class="text-3xl font-bold" style="color:#111827;">
                        {{ $documentsCount }}
                    </div>

                    <div class="mt-2" style="color:#6B7280;">
                        Documents
                    </div>

                </a>

                <!-- Titres -->

                <a href="{{ route('documents.index') }}" class="stat-card bg-white rounded-xl p-6 block">

                    <div class="icon-badge" style="background:#ECFDF5;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12h4l3-8 4 16 3-8h4"/>
                        </svg>
                    </div>

                    <div class="text-3xl font-bold" style="color:#111827;">
                       {{ $ocrCount }}
                    </div>

                    <div class="mt-2" style="color:#6B7280;">
                       Documents analysés par OCR
                    </div>

                </a>

                <!-- Semaine -->

                <a href="{{ route('documents.index', ['periode' => 'semaine']) }}" class="stat-card bg-white rounded-xl p-6 block">

                    <div class="icon-badge" style="background:#EEF2FF;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#4F46E5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>

                    <div class="text-3xl font-bold" style="color:#111827;">
                        {{ $recentCount }}
                    </div>

                    <div class="mt-2" style="color:#6B7280;">
                        Ajouts cette semaine
                    </div>

                </a>

                <!-- Emails -->

                <a href="{{ route('mails.index') }}" class="stat-card bg-white rounded-xl p-6 block">

                    <div class="icon-badge" style="background:#F5F3FF;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                            <path d="M2 7l10 6 10-6"/>
                        </svg>
                    </div>

                    <div class="text-3xl font-bold" style="color:#111827;">
                        {{ $emailsCount }}
                    </div>

                    <div class="mt-2" style="color:#6B7280;">
                        Emails
                    </div>

                </a>

                <!-- Factures du mois -->

                <a href="{{ route('invoices.index') }}" class="stat-card bg-white rounded-xl p-6 block">

                    <div class="icon-badge" style="background:#FEF2F2;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2h9l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
                            <path d="M14 2v5h5"/>
                            <line x1="8" y1="13" x2="14" y2="13"/>
                            <line x1="8" y1="17" x2="12" y2="17"/>
                        </svg>
                    </div>

                    <div class="text-3xl font-bold" style="color:#111827;">
                        {{ $facturesMoisCount }}
                    </div>

                    <div class="mt-2" style="color:#6B7280;">
                        Factures ce mois-ci
                    </div>

                </a>

                <!-- TVA à déclarer -->

                <a href="{{ route('tva.index') }}" class="stat-card bg-white rounded-xl p-6 block">

                    <div class="icon-badge" style="background:#ECFEFF;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#0891B2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 11H3v10h6"/>
                            <path d="M9 21h12v-6a2 2 0 0 0-2-2h-4"/>
                            <path d="M9 11V5a2 2 0 0 1 2-2h4l4 4v4"/>
                            <line x1="13" y1="7" x2="17" y2="7"/>
                        </svg>
                    </div>

                    <div class="text-3xl font-bold" style="color:#111827;">
                        {{ number_format($tvaADeclarer, 2, ',', ' ') }} DH
                    </div>

                    <div class="mt-2" style="color:#6B7280;">
                        TVA à déclarer (mois)
                    </div>

                </a>

                <!-- Virements en attente -->

                <a href="{{ route('virements.index') }}" class="stat-card bg-white rounded-xl p-6 block">

                    <div class="icon-badge" style="background:#FFF7ED;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#EA580C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 1l4 4-4 4"/>
                            <path d="M3 11V9a4 4 0 0 1 4-4h14"/>
                            <path d="M7 23l-4-4 4-4"/>
                            <path d="M21 13v2a4 4 0 0 1-4 4H3"/>
                        </svg>
                    </div>

                    <div class="text-3xl font-bold" style="color:#111827;">
                        {{ $virementsEnAttenteCount }}
                    </div>

                    <div class="mt-2" style="color:#6B7280;">
                        Virements en attente
                    </div>

                </a>

                <!-- Rappels à venir -->

                <a href="{{ route('rappels.index') }}" class="stat-card bg-white rounded-xl p-6 block">

                    <div class="icon-badge" style="background:#FDF2F8;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#DB2777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                    </div>

                    <div class="text-3xl font-bold" style="color:#111827;">
                        {{ $rappelsAVenirCount }}
                    </div>

                    <div class="mt-2" style="color:#6B7280;">
                        Rappels à venir
                    </div>

                </a>

            </div>

            <!-- Graphique d'évolution mensuelle -->

            <div class="bg-white rounded-xl shadow p-6">

                <h2 class="font-display text-xl font-bold mb-4 flex items-center gap-2" style="color:#111827;">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#E8590C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                        <polyline points="17 6 23 6 23 12"/>
                    </svg>
                    Évolution mensuelle du chiffre d'affaires facturé
                </h2>

                <canvas id="graphiqueFactures" height="80"></canvas>

            </div>

            <!-- Accès rapides -->

            <div>

                <h2 class="font-display text-xl font-bold mb-4" style="color:#111827;">
                    Accès rapides
                </h2>

                <div class="grid md:grid-cols-2 gap-4">

                    <a href="{{ route('documents.create') }}"
                        class="quick-link bg-white rounded-xl p-5 flex items-center gap-4">

                        <div class="icon-badge" style="background:#FFF7F2; margin-bottom:0;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#E8590C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </div>

                        <div>

                            <div class="font-semibold" style="color:#111827;">
                                Ajouter un document
                            </div>

                            <div class="text-sm" style="color:#6B7280;">
                                Importer un nouveau document
                            </div>

                        </div>

                    </a>

                    <a href="{{ route('documents.index') }}"
                        class="quick-link bg-white rounded-xl p-5 flex items-center gap-4">

                        <div class="icon-badge" style="background:#FFF7F2; margin-bottom:0;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#E8590C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                            </svg>
                        </div>

                        <div>

                            <div class="font-semibold" style="color:#111827;">
                                Voir les documents
                            </div>

                            <div class="text-sm" style="color:#6B7280;">
                                Consulter les archives
                            </div>

                        </div>

                    </a>

                </div>

            </div>

            <!-- Derniers documents -->

            <div class="bg-white rounded-xl shadow">

                <div class="border-b p-5">

                    <h2 class="font-display text-xl font-bold" style="color:#111827;">
                        Derniers documents
                    </h2>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-gray-100">

                            <tr>

                                <th class="text-left p-3 font-semibold" style="color:#111827;">Titre</th>

                                <th class="text-left p-3 font-semibold" style="color:#111827;">Propriétaire</th>

                                <th class="text-left p-3 font-semibold" style="color:#111827;">Marque</th>

                                <th class="text-left p-3 font-semibold" style="color:#111827;">Modèle</th>

                                <th class="text-left p-3 font-semibold" style="color:#111827;">Immatriculation</th>

                                <th class="text-left p-3 font-semibold" style="color:#111827;">Date</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($lastDocuments as $doc)

                                <tr class="table-row border-b">

                                    <td class="p-3" style="color:#374151;">
                                        {{ $doc->titre }}
                                    </td>

                                    <td class="p-3" style="color:#374151;">
                                        {{ $doc->nom_proprietaire }}
                                    </td>

                                    <td class="p-3" style="color:#374151;">
                                        {{ $doc->marque }}
                                    </td>

                                    <td class="p-3" style="color:#374151;">
                                        {{ $doc->modele }}
                                    </td>

                                    <td class="p-3" style="color:#374151;">
                                        {{ $doc->immatriculation }}
                                    </td>

                                    <td class="p-3" style="color:#374151;">
                                        {{ $doc->created_at?->format('d/m/Y') }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6" class="text-center p-5" style="color:#6B7280;">
                                        Aucun document disponible.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

           <!-- Rappels à venir -->

            <div class="bg-white rounded-xl shadow">

                <div class="border-b p-5 flex justify-between items-center">

                    <h2 class="font-display text-xl font-bold flex items-center gap-2" style="color:#111827;">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#DB2777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        Rappels à venir
                    </h2>

                    <a href="{{ route('rappels.index') }}"
                       class="text-orange-600 hover:underline font-semibold">
                        Voir tous
                    </a>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-gray-100">

                            <tr>

                                <th class="text-left p-3 font-semibold" style="color:#111827;">Titre</th>

                                <th class="text-left p-3 font-semibold" style="color:#111827;">Description</th>

                                <th class="text-left p-3 font-semibold" style="color:#111827;">Date</th>

                                <th class="text-left p-3 font-semibold" style="color:#111827;">État</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($rappels as $rappel)

                                <tr class="border-b hover:bg-gray-50">

                                    <td class="p-3" style="color:#374151;">
                                        {{ $rappel->titre }}
                                    </td>

                                    <td class="p-3" style="color:#374151;">
                                        {{ $rappel->description }}
                                    </td>

                                    <td class="p-3" style="color:#374151;">
                                        {{ \Carbon\Carbon::parse($rappel->date_rappel)->format('d/m/Y') }}
                                    </td>

                                    <td class="p-3">

                                        @if(\Carbon\Carbon::parse($rappel->date_rappel)->isToday())

                                            <span class="px-2 py-1 rounded bg-red-100 text-red-700 text-xs">
                                                Aujourd'hui
                                            </span>

                                        @elseif(\Carbon\Carbon::parse($rappel->date_rappel)->isTomorrow())

                                            <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-700 text-xs">
                                                Demain
                                            </span>

                                        @else

                                            <span class="px-2 py-1 rounded bg-green-100 text-green-700 text-xs">
                                                À venir
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="4" class="text-center p-5" style="color:#6B7280;">
                                        Aucun rappel à venir.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>

    <script>
        const ctx = document.getElementById('graphiqueFactures').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($mois),
                datasets: [{
                    label: 'Montant facturé (DH)',
                    data: @json($totauxFactures),
                    backgroundColor: '#E8590C',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>

</x-app-layout>