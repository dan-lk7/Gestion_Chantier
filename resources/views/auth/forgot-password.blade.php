@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg p-4" style="width: 400px;">
        <h3 class="text-center mb-4">🔑 Réinitialiser le mot de passe</h3>

        @if (session('status'))
            <div class="alert alert-success text-center">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Champ Email -->
            <div class="mb-3">
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                    placeholder="✉ Adresse email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Bouton Envoyer -->
            <div class="text-center">
                <button type="submit" class="btn btn-warning w-100">Envoyer le lien</button>
            </div>

            <!-- Lien Connexion -->
            <div class="text-center mt-3">
                <a href="{{ route('login') }}" class="text-secondary">Retour à la connexion</a>
            </div>
        </form>
    </div>
</div>
@endsection
