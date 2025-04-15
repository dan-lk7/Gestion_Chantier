@extends('layouts.app')

@section('header', 'Ajouter un Stock')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">📦 Ajouter un Stock</div>
        <div class="card-body">
        <form action="{{ route('stocks.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="nom" class="form-label">Nom du Stock</label>
        <input type="text" class="form-control" name="nom" required>
    </div>

    <div class="mb-3">
        <label for="quantite" class="form-label">Quantité</label>
        <input type="number" class="form-control" name="quantite" required>
    </div>

    <div class="mb-3">
        <label for="chantier_id" class="form-label">Chantier</label>
        <select class="form-control" name="chantier_id" required>
            @foreach ($chantiers as $chantier)
                <option value="{{ $chantier->id }}">{{ $chantier->nom }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-success">💾 Enregistrer</button>
    <a href="{{ route('stocks.index') }}" class="btn btn-secondary">↩️ Retour</a>
</form>

        </div>
    </div>
</div>
@endsection
