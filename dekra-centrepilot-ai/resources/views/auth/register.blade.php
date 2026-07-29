<x-guest-layout>

    <div class="mb-6">
        <h2 class="font-display text-2xl font-semibold" style="color:#16233A;">Créer un compte</h2>
        <p class="text-sm text-gray-500 mt-1">Rejoignez l'espace de gestion du centre.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nom complet')" class="!font-medium !text-gray-700" />
            <x-text-input id="name" class="block mt-1.5 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Votre nom" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="!font-medium !text-gray-700" />
            <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="vous@centre.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Mot de passe')" class="!font-medium !text-gray-700" />
            <x-text-input id="password" class="block mt-1.5 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password"
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" class="!font-medium !text-gray-700" />
            <x-text-input id="password_confirmation" class="block mt-1.5 w-full"
                            type="password"
                            name="password_confirmation"
                            required autocomplete="new-password"
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between pt-2">
            <a class="text-sm font-medium text-accent hover:underline" href="{{ route('login') }}">
                {{ __('Déjà inscrit ?') }}
            </a>

            <x-primary-button class="!py-2.5">
                {{ __('S\'inscrire') }}
            </x-primary-button>
        </div>
    </form>

</x-guest-layout>