<x-app-layout>

    <x-slot name="header">
        Nouveau virement
    </x-slot>

    <style>
        .font-display{
            font-family:'Space Grotesk',sans-serif;
        }

        .card{
            border:1px solid #E4E0D8;
            border-radius:18px;
        }

        label{
            font-weight:600;
            color:#374151;
            margin-bottom:6px;
            display:block;
        }

        input, select{
            width:100%;
            border:1px solid #D1D5DB;
            border-radius:10px;
            padding:12px;
            background:#ffffff;
            color:#111827;
        }

        input:focus, select:focus{
            border-color:#E8590C;
            outline:none;
            box-shadow:0 0 0 3px rgba(232,89,12,.15);
        }

        input::placeholder{
            color:#9CA3AF;
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus{
            -webkit-text-fill-color:#111827;
            -webkit-box-shadow:0 0 0 1000px #ffffff inset;
            transition:background-color 9999s ease-in-out 0s;
        }

        .btn-orange{
            background:#E8590C;
            color:white;
            padding:12px 25px;
            border-radius:10px;
            font-weight:600;
            transition:.2s;
        }

        .btn-orange:hover{
            background:#c74c0a;
        }

        input[type="checkbox"]{
            width:18px;
            height:18px;
            min-width:18px;
            padding:0;
            border:1px solid #9CA3AF;
            accent-color:#E8590C;
            flex-shrink:0;
        }

        .case-confirmation{
            display:flex;
            align-items:center;
            gap:8px;
        }
    </style>

    <div class="py-8">

        <div class="max-w-3xl mx-auto">

            <div class="bg-white shadow-sm p-8 card">

                <h2 class="text-2xl font-display font-bold mb-6" style="color:#111827;">
                    Nouveau virement
                </h2>

                @if ($errors->any())
                    <div class="mb-5 p-4 rounded-lg bg-red-50 text-red-700 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('alertes_coherence'))
                    <div class="mb-5 p-4 rounded-lg bg-amber-50 text-amber-800 text-sm" style="border:1px solid #FDE68A;">
                        <p class="font-semibold mb-2">⚠️ Contrôle de cohérence</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach(session('alertes_coherence') as $alerte)
                                <li>{{ $alerte }}</li>
                            @endforeach
                        </ul>
                        <label class="case-confirmation mt-4 font-medium">
                            <input type="checkbox" id="confirmer_malgre_alertes_visuel">
                            <span>Je confirme ces informations malgré l'alerte ci-dessus</span>
                        </label>
                    </div>
                @endif

                <form action="{{ route('virements.store') }}" method="POST">

                    @csrf

                    <input type="hidden" name="confirmer_malgre_alertes" id="confirmer_malgre_alertes" value="0">

                    <div class="mb-6">

                        <label>Bénéficiaire</label>

                        <select name="beneficiaire" required>
                            <option value="DEKRA" {{ old('beneficiaire') === 'DEKRA' ? 'selected' : '' }}>DEKRA</option>
                            <option value="SECURITY DOCUMENT" {{ old('beneficiaire') === 'SECURITY DOCUMENT' ? 'selected' : '' }}>SECURITY DOCUMENT</option>
                        </select>

                    </div>

                    <div class="mb-6">

                        <label>Montant (MAD)</label>

                        <input
                            type="number"
                            step="0.01"
                            name="montant"
                            value="{{ old('montant') }}"
                            required>

                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Motif du virement
                        </label>

                        <input
                            type="text"
                            name="motif"
                            value="{{ old('motif') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            placeholder="Ex : Paiement facture">
                    </div>

                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700">
                            Date du virement
                        </label>

                       <input
    type="date"
    name="date_virement"
    value="{{ old('date_virement', date('Y-m-d')) }}"
    required>
                    </div>

                    <button class="btn-orange mt-6">
                        Enregistrer le virement
                    </button>

                </form>

            </div>

        </div>

    </div>

    <script>
        const caseConfirmation = document.getElementById('confirmer_malgre_alertes_visuel');
        if (caseConfirmation) {
            caseConfirmation.addEventListener('change', function () {
                document.getElementById('confirmer_malgre_alertes').value = this.checked ? '1' : '0';
            });
        }
    </script>

</x-app-layout>