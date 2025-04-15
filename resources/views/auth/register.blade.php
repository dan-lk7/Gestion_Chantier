@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg p-4" style="width: 400px;">
        <h3 class="text-center mb-4">Créer un compte</h3>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Champ Nom -->
            <div class="mb-3">
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                    placeholder="👤 Nom complet" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Champ Email -->
            <div class="mb-3">
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                    placeholder="✉ Adresse email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <select name="role_id" id="role_id" class="form-control" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->nom }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Champ Mot de passe -->
            <div class="mb-3">
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                    placeholder="🔑 Mot de passe" required>
                @error('password')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Champ Confirmation Mot de passe -->
            <div class="mb-3">
                <input type="password" name="password_confirmation" class="form-control" placeholder="🔒 Confirmer le mot de passe" required>
            </div>

            <!-- Bouton S'inscrire -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
            </div>

            <!-- Lien Connexion -->
            <div class="text-center mt-3">
                <a href="{{ route('login') }}" class="text-secondary">Déjà un compte ? Connectez-vous</a>
            </div>
        </form>
    </div>
</div>
@endsection
