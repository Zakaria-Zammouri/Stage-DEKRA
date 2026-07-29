<x-app-layout>

    <x-slot name="header">
        Détails du rappel
    </x-slot>

    <style>
        .font-display{
            font-family:'Space Grotesk',sans-serif;
        }

        .card{
            background:white;
            border-radius:18px;
            padding:35px;
            box-shadow:0 10px 25px rgba(0,0,0,.05);
            border:1px solid #E4E0D8;
        }

        .label{
            font-weight:600;
            color:#6B7280;
            margin-bottom:6px;
        }

        .value{
            font-size:17px;
            color:#111827;
            margin-bottom:25px;
        }

        .btn-edit{
            background:#198754;
            color:white;
            padding:10px 18px;
            border-radius:10px;
            text-decoration:none;
            font-weight:600;
            display:inline-block;
        }

        .btn-edit:hover{
            background:#157347;
        }

        .btn-back{
            background:#6c757d;
            color:white;
            padding:10px 18px;
            border-radius:10px;
            text-decoration:none;
            font-weight:600;
            display:inline-block;
        }

        .btn-back:hover{
            background:#5c636a;
        }
    </style>

    <div class="py-8">

        <div class="max-w-4xl mx-auto px-6">

            <div class="card">

                <h2 class="font-display text-2xl font-semibold mb-8" style="color:#111827;">
                    Détails du rappel
                </h2>

                <div>
                    <div class="label">📅 Date du rappel</div>
                    <div class="value">{{ \Carbon\Carbon::parse($rappel->date_rappel)->format('d/m/Y') }}</div>
                </div>

                <div>
                    <div class="label">📝 Titre</div>
                    <div class="value">{{ $rappel->titre }}</div>
                </div>

                <div>
                    <div class="label">📄 Description</div>
                    <div class="value">{{ $rappel->description ?? 'Aucune description.' }}</div>
                </div>

                <div class="flex gap-3 mt-8">
                    <a href="{{ route('rappels.edit', $rappel) }}" class="btn-edit">Modifier</a>
                    <a href="{{ route('rappels.index') }}" class="btn-back">Retour à la liste</a>
                </div>

            </div>

        </div>

    </div>

</x-app-layout>