<x-app-layout>

    <x-slot name="header">
        Détail du virement
    </x-slot>

    <style>
        .font-display{
            font-family:'Space Grotesk',sans-serif;
        }

        .card{
            border:1px solid #E4E0D8;
            border-radius:18px;
        }

        .label{
            font-size:14px;
            color:#6B7280;
            margin-bottom:5px;
        }

        .value{
            font-size:18px;
            font-weight:600;
            color:#111827;
        }

        .btn-orange{
            background:#E8590C;
            color:white;
            padding:12px 22px;
            border-radius:10px;
            text-decoration:none;
            font-weight:600;
        }

        .btn-orange:hover{
            background:#C74C0A;
            color:white;
        }

        .btn-secondary{
            background:#16233A;
            color:white;
            padding:12px 22px;
            border-radius:10px;
            text-decoration:none;
            font-weight:600;
        }

        .btn-secondary:hover{
            background:#223350;
            color:white;
        }
    </style>

    <div class="py-8">

        <div class="max-w-3xl mx-auto">

            <div class="bg-white shadow-sm p-8 card">

                <h2 class="font-display text-2xl font-bold mb-8">
                    Informations du virement
                </h2>

                <div class="grid grid-cols-1 gap-6">

                    <div>
                        <div class="label">
                            Bénéficiaire
                        </div>

                        <div class="value">
                            {{ $virement->beneficiaire }}
                        </div>
                    </div>

                    <div>
                        <div class="label">
                            Montant
                        </div>

                        <div class="value">
                            {{ number_format($virement->montant,2,',',' ') }} DH
                        </div>
                    </div>

                    <div>
                        <div class="label">
                            Montant en lettres
                        </div>

                        <div class="value">
                            {{ $virement->montant_lettres }}
                        </div>
                    </div>

                    <div>
                        <div class="label">
                            Date du virement
                        </div>

                        <div class="value">
                            {{ \Carbon\Carbon::parse($virement->date_virement)->format('d/m/Y') }}
                        </div>
                    </div>

                </div>

                <div class="mt-10 flex gap-4">

                    
                     <a href="{{ route('virements.index') }}"
   class="px-6 py-3 rounded-xl border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 font-semibold transition">
    Retour
</a>

                    <a href="{{ route('virements.pdf',$virement) }}"
                       class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg">

                        Télécharger le PDF

                    </a>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>