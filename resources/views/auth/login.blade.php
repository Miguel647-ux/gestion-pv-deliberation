<x-guest-layout>
    <div class="flex flex-col items-center w-full max-w-[400px] gap-6">

        <!-- Logo -->
        <img
            src="{{ asset('images/logo-eneam.png') }}"
            alt="Logo de l'ENEAM"
            class="w-44 h-44 object-contain mix-blend-multiply select-none"
            width="176"
            height="176"
        >

        <!-- Titre & sous-titre -->
        <div class="text-center">
            <h1 class="font-serif text-[36px] leading-tight font-semibold text-login-ink tracking-tight">
                Connexion
            </h1>
            <p class="mt-1 text-[13px] text-login-muted leading-snug">
                Archives des procès-verbaux de délibération de l'ENEAM
            </p>
        </div>

        <!-- Statut session (réinitialisation mdp, etc.) -->
        <x-auth-session-status class="w-full" :status="session('status')" />

        <!-- Formulaire -->
        <form
            method="POST"
            action="{{ route('login') }}"
            class="flex flex-col gap-5 w-full"
        >
            @csrf

            <!-- Adresse e-mail -->
            <div class="flex flex-col gap-1.5">
                <label for="email" class="text-[13px] font-medium text-login-ink">
                    Adresse e-mail
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="
                        w-full h-[42px] px-3
                        bg-white border border-login-border rounded-login
                        text-[14px] text-login-ink
                        focus:outline-none focus:ring-2 focus:ring-login-green focus:border-login-green
                        transition-colors
                        @error('email') border-error @enderror
                    "
                >
                <x-input-error :messages="$errors->get('email')" class="text-[12px]" />
            </div>

            <!-- Mot de passe -->
            <div class="flex flex-col gap-1.5" x-data="{ show: false }">
                <label for="password" class="text-[13px] font-medium text-login-ink">
                    Mot de passe
                </label>
                <div class="relative">
                    <input
                        id="password"
                        :type="show ? 'text' : 'password'"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="
                            w-full h-[42px] pl-3 pr-24
                            bg-white border border-login-border rounded-login
                            text-[14px] text-login-ink
                            focus:outline-none focus:ring-2 focus:ring-login-green focus:border-login-green
                            transition-colors
                            @error('password') border-error @enderror
                        "
                    >
                    <button
                        type="button"
                        @click="show = !show"
                        aria-label="Afficher le mot de passe"
                        class="
                            absolute right-3 top-1/2 -translate-y-1/2
                            text-[12px] font-medium text-login-muted hover:text-login-green
                            focus:outline-none focus:ring-2 focus:ring-login-green rounded
                            transition-colors cursor-pointer
                        "
                        x-text="show ? 'Masquer' : 'Afficher'"
                    ></button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="text-[12px]" />
            </div>

            <!-- Se souvenir de moi -->
            <div class="flex items-center gap-2">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="
                        w-4 h-4 rounded border-login-border
                        text-login-green focus:ring-login-green focus:ring-2
                        cursor-pointer
                    "
                >
                <label for="remember_me" class="text-[13px] text-login-muted cursor-pointer select-none">
                    Se souvenir de moi
                </label>
            </div>

            <!-- Bouton Se connecter -->
            <button
                type="submit"
                class="
                    w-full h-[46px]
                    bg-login-green hover:bg-login-green-dark active:bg-login-green-dark
                    text-white font-semibold text-[14px] rounded-login
                    focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-login-green
                    transition-colors cursor-pointer
                "
            >
                Se connecter
            </button>

        </form>
    </div>
</x-guest-layout>
