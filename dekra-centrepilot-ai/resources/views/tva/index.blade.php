<x-app-layout>

    <x-slot name="header">
        Suivi de la TVA
    </x-slot>

    <div class="max-w-6xl mx-auto py-8 px-4">

        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <h1 class="text-3xl font-bold mb-6">🧾 TVA</h1>

        {{-- Taux TVA Moto --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">
                Taux de TVA pour les motos
            </h2>

            <form method="POST" action="{{ route('tva.rates.update') }}">
                @csrf
                @method('PUT')

                @php
                    $moto = $rates->firstWhere('type_vehicule', 'Moto');
                @endphp

                @if($moto)
                    <div class="max-w-sm">
                        <label class="block text-sm font-medium mb-2 dark:text-gray-300">
                            Moto
                        </label>

                        <div class="flex items-center gap-2">
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                name="taux[{{ $moto->id }}]"
                                value="{{ $moto->taux }}"
                                class="w-full border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

                            <span class="text-gray-500 dark:text-gray-400">%</span>
                        </div>
                    </div>
                @endif

                <button
                    type="submit"
                    class="mt-5 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">
                    Enregistrer le taux
                </button>

            </form>
        </div>

        {{-- Filtre --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 mb-6">

            <form method="GET"
                  action="{{ route('tva.index') }}"
                  class="flex flex-wrap items-end gap-4">

                <div>
                    <label class="block text-sm font-medium mb-2 dark:text-gray-300">
                        📅 Mois
                    </label>

                    <input
                        type="month"
                        name="mois"
                        value="{{ request('mois', $mois->format('Y-m')) }}"
                        class="border rounded-lg px-4 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <button
                    type="submit"
                    class="bg-gray-700 hover:bg-gray-800 text-white px-5 py-2 rounded-lg">
                    Filtrer
                </button>

                <a
                    href="{{ route('tva.pdf',['mois'=>request('mois',$mois->format('Y-m'))]) }}"
                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg">

                    📄 Exporter en PDF

                </a>

            </form>

        </div>

        {{-- Tableau TVA --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">

            <table class="w-full text-sm">

                <thead>

                    <tr class="text-left text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700">

                        <th class="px-4 py-3">
                            Type de véhicule
                        </th>

                        <th class="px-4 py-3 text-right">
                            Nb factures
                        </th>

                        <th class="px-4 py-3 text-right">
                            Total HT
                        </th>

                        <th class="px-4 py-3 text-right">
                            Total TVA
                        </th>

                        <th class="px-4 py-3 text-right">
                            Total TTC
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @php
                        $ligneMoto = collect($recap['lignes'])->firstWhere('type', 'Moto');
                    @endphp

                    @if($ligneMoto)

                        <tr class="border-t border-gray-100 dark:border-gray-700">

                            <td class="px-4 py-3 font-medium">
                                Moto
                            </td>

                            <td class="px-4 py-3 text-right">
                                {{ $ligneMoto['nombre'] }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                {{ number_format($ligneMoto['ht'],2,',',' ') }} DH
                            </td>

                            <td class="px-4 py-3 text-right">
                                {{ number_format($ligneMoto['tva'],2,',',' ') }} DH
                            </td>

                            <td class="px-4 py-3 text-right">
                                {{ number_format($ligneMoto['ttc'],2,',',' ') }} DH
                            </td>

                        </tr>

                    @endif

                    <tr class="border-t-2 border-gray-300 dark:border-gray-600 font-bold bg-gray-50 dark:bg-gray-700">

                        <td class="px-4 py-3">
                            Total
                        </td>

                        <td></td>

                        <td class="px-4 py-3 text-right">
                            {{ number_format($recap['total_ht'],2,',',' ') }} DH
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ number_format($recap['total_tva'],2,',',' ') }} DH
                        </td>

                        <td class="px-4 py-3 text-right">
                            {{ number_format($recap['total_ttc'],2,',',' ') }} DH
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</x-app-layout>