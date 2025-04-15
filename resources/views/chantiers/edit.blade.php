@extends('layouts.app')

@section('content')
<div class="container">
    <h2>✏️ Modifier le Chantier</h2>

    <form action="{{ route('chantiers.update', $chantier->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nom du Chantier</label>
            <input type="text" class="form-control" name="nom" value="{{ $chantier->nom }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Adresse</label>
            <input type="text" class="form-control" name="adresse" value="{{ $chantier->adresse }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Pays</label>
            <input type="text" class="form-control" name="pays" value="{{ $chantier->pays }}" required>
        </div>
        <div class="mb-3">
            <label for="responsable_id" class="form-label">Responsable du Chantier</label>
            <select name="responsable_id" class="form-control">
                <option value="">-- Sélectionner un responsable --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $chantier->responsable_id == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
        <a href="{{ route('chantiers.index') }}" class="btn btn-secondary">↩️ Retour</a>
    </form>
</div>
@endsection
