@extends('layouts.app')

@section('header', 'Ajouter un Chantier')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">🏗️ Ajouter un Chantier</div>
        <div class="card-body">
            <form action="{{ route('chantiers.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom du Chantier</label>
                    <input type="text" class="form-control" name="nom" required>
                </div>

                <div class="mb-3">
                    <label for="adresse" class="form-label">Adresse</label>
                    <input type="text" class="form-control" name="adresse" required>
                </div>

                <div class="mb-3">
                    <label for="pays" class="form-label">Pays</label>
                    <input type="text" class="form-control" name="pays" required>
                </div>

                <!-- Ajout du champ de sélection des rôles -->
                <div class="mb-3">
                    <label for="responsable_id" class="form-label">Responsable du Chantier</label>
                    <select name="responsable_id" class="form-control">
                        <option value="">-- Sélectionner un responsable --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-success">💾 Enregistrer</button>
                <a href="{{ route('chantiers.index') }}" class="btn btn-secondary">↩️ Retour</a>
            </form>
        </div>
    </div>
</div>
@endsection
