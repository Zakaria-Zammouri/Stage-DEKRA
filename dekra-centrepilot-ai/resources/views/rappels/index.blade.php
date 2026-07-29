<x-app-layout>

    <x-slot name="header">
        Liste des rappels
    </x-slot>

    <style>
        .font-display{
            font-family:'Space Grotesk',sans-serif;
        }

        .btn-primary{
            background:#E8590C;
            color:white;
            padding:10px 18px;
            border-radius:10px;
            font-weight:600;
            text-decoration:none;
        }

        .btn-primary:hover{
            background:#cc4b08;
        }

        .btn-show{
            background:#0d6efd;
            color:white;
            padding:6px 12px;
            border-radius:8px;
            text-decoration:none;
            font-size:14px;
        }

        .btn-show:hover{
            background:#0b5ed7;
        }

        .btn-edit{
            background:#198754;
            color:white;
            padding:6px 12px;
            border-radius:8px;
            text-decoration:none;
            font-size:14px;
        }

        .btn-edit:hover{
            background:#157347;
        }

        .btn-delete{
            background:#dc3545;
            color:white;
            padding:6px 12px;
            border-radius:8px;
            border:none;
            cursor:pointer;
            font-size:14px;
        }

        .btn-delete:hover{
            background:#bb2d3b;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th{
            background:#16233A;
            color:white;
            padding:12px;
            text-align:left;
        }
td{
            padding:12px;
            border-bottom:1px solid #E4E0D8;
            color:#111827;
        }

        tr:hover{
            background:#f9fafb;
        }
    </style>

    <div class="py-8">

        <div class="max-w-7xl mx-auto px-6">

            @if(session('success'))

                <div class="mb-5 p-4 rounded-lg bg-green-100 text-green-800">
                    {{ session('success') }}
                </div>

            @endif

            <div class="flex justify-between items-center mb-6">

                <h2 class="font-display text-2xl font-semibold">
                    Les Rappels
                </h2>

                <a href="{{ route('rappels.create') }}" class="btn-primary">
                    + Nouveau rappel
                </a>

            </div>
            <!-- Recherche et filtres -->

<div class="bg-white rounded-xl shadow p-5 mb-6">

    <form method="GET" action="{{ route('rappels.index') }}">

        <div class="grid md:grid-cols-4 gap-4">

            <!-- Recherche -->

            <div>

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Recherche
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Titre ou description..."
                    class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">

            </div>

            <!-- Statut -->

            <div>

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Statut
                </label>

                <select
                    name="statut"
                    class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">

                    <option value="">Tous</option>

                    <option value="À venir"
                        {{ request('statut') == 'À venir' ? 'selected' : '' }}>
                        À venir
                    </option>

                    <option value="Aujourd'hui"
                        {{ request('statut') == "Aujourd'hui" ? 'selected' : '' }}>
                        Aujourd'hui
                    </option>

                    <option value="En retard"
                        {{ request('statut') == 'En retard' ? 'selected' : '' }}>
                        En retard
                    </option>

                    <option value="Terminé"
                        {{ request('statut') == 'Terminé' ? 'selected' : '' }}>
                        Terminé
                    </option>

                </select>

            </div>

            <!-- Date -->

            <div>

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Date
                </label>

                <input
                    type="date"
                    name="date"
                    value="{{ request('date') }}"
                    class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500">

            </div>

            <!-- Boutons -->

            <div class="flex items-end gap-2">

             <button
    type="submit"
    class="inline-flex items-center justify-center h-10 px-5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition">
    🔍 Rechercher
</button>

                <a
                   href="{{ route('rappels.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 px-5 py-2 rounded-lg font-semibold">

                    Réinitialiser

                
                </a>

            </div>

        </div>

    </form>

</div>
<div class="flex items-center justify-between mb-4">

  

    <span class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-lg">

        {{ $rappels->count() }}
        {{ $rappels->count() > 1 ? 'rappels trouvés' : 'rappel trouvé' }}

    </span>

</div>

            <div class="bg-white rounded-2xl shadow overflow-hidden">

                <table>

                    <thead>

                        <tr>

                            <th>Date</th>
                            <th>Titre</th>
                            <th>Description</th>
                            <th width="220">Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($rappels as $rappel)

                            <tr>

                                <td>
                                    {{ \Carbon\Carbon::parse($rappel->date_rappel)->format('d/m/Y') }}
                                </td>

                                <td>
                                    {{ $rappel->titre }}
                                </td>

                                <td>
                                    {{ $rappel->description }}
                                </td>

                                <td>

                                    <div class="flex gap-2">

                                      <a href="{{ route('rappels.show', $rappel) }}"
   class="inline-flex items-center justify-center w-24 h-10 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition">
    Voir
</a>

                                       <a href="{{ route('rappels.edit', $rappel) }}"
   class="inline-flex items-center justify-center w-24 h-10 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold transition">
    Modifier
</a>
                                        @if (!$rappel->termine)
    <form action="{{ route('rappels.terminer', $rappel) }}" method="POST" class="inline">
    @csrf
    @method('PATCH')

    <button
        onclick="return confirm('Marquer ce rappel comme terminé ?')"
        class="inline-flex items-center justify-center w-24 h-10 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-semibold transition">
        ✔ Terminer
    </button>
</form>
@endif

                                    <form action="{{ route('rappels.destroy', $rappel) }}" method="POST" class="inline">
    @csrf
    @method('DELETE')

    <button
        onclick="return confirm('Supprimer ce rappel ?')"
        class="inline-flex items-center justify-center w-24 h-10 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition">
        Supprimer
    </button>
</form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4" class="text-center py-8">

                                    Aucun rappel enregistré.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</x-app-layout>