@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card p-4 shadow-lg" style="width: 400px;">
        <h3 class="text-center mb-4">Connexion</h3>

        <!-- Message d'erreur -->
        @if (session('error'))
            <div class="alert alert-danger text-center">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Champ Email -->
            <div class="mb-3">
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                    placeholder="✉ Adresse email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Champ Mot de passe -->
            <div class="mb-3">
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                    placeholder="🔒 Mot de passe" required>
                @error('password')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Case à cocher Se souvenir -->
            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Se souvenir de moi</label>
            </div>

            <!-- Bouton Connexion -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary w-100">Se connecter</button>
            </div>

            <!-- Liens : Mot de passe oublié & Inscription -->
            <div class="text-center mt-3">
                <a href="{{ route('register') }}" class="text-primary">Créer un compte</a> |
                <a href="{{ route('password.request') }}" class="text-secondary">Mot de passe oublié ?</a>
            </div>
        </form>
    </div>
</div>
@endsection
