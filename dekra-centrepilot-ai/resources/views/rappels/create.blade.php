<x-app-layout>

    <x-slot name="header">
        Ajouter un rappel
    </x-slot>

    <style>
        .font-display{
            font-family:'Space Grotesk',sans-serif;
        }

        label{
            font-weight:600;
            color:#374151;
            margin-bottom:8px;
            display:block;
        }

        .form-input{
            border:1px solid #E4E0D8;
            border-radius:10px;
            padding:10px;
            width:100%;
            background:#ffffff;
            color:#111827;
        }

        .form-input:focus{
            border-color:#E8590C;
            box-shadow:0 0 0 3px rgba(232,89,12,.15);
            outline:none;
        }

        .form-input::placeholder{
            color:#9CA3AF;
        }

        .btn-primary{
            background:#E8590C;
            color:white;
            padding:10px 18px;
            border-radius:10px;
            font-weight:600;
        }

        .btn-primary:hover{
            background:#cc4b08;
        }
    </style>

    <div class="py-8">
        <div class="max-w-3xl mx-auto">

            <div class="bg-white rounded-2xl shadow p-8">

                <h2 class="font-display text-xl font-semibold mb-6" style="color:#111827;">
                    Nouveau rappel
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

                <form action="{{ route('rappels.store') }}" method="POST">

                    @csrf

                    <div class="mb-5">
                        <label>
                            Titre
                        </label>

                        <input
                            type="text"
                            name="titre"
                            class="form-input"
                            value="{{ old('titre') }}"
                            required
                        >
                    </div>

                    <div class="mb-5">
                        <label>
                            Description
                        </label>

                        <textarea
                            name="description"
                            rows="5"
                            class="form-input"
                        >{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-5">
                        <label>
                            Date du rappel
                        </label>

                        <input
                            type="date"
                            name="date_rappel"
                            class="form-input"
                            value="{{ old('date_rappel') }}"
                            required
                        >
                    </div>

                    <button class="btn-primary">
                        Enregistrer
                    </button>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>