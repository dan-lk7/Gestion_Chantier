@extends('layouts.app')

@section('header', 'Modifier un Stock')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5><i class="bi bi-pencil-square"></i> Modifier le Stock</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('stocks.update', $stock->id) }}" method="POST">
                @csrf
                @method('PUT') {{-- Ajout de la méthode PUT pour la mise à jour --}}

                <!-- Nom du Stock -->
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom du Stock</label>
                    <input type="text" class="form-control @error('nom') is-invalid @enderror" name="nom" value="{{ old('nom', $stock->nom) }}" required>
                    @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Quantité -->
                <div class="mb-3">
                    <label for="quantite" class="form-label">Quantité</label>
                    <input type="number" class="form-control @error('quantite') is-invalid @enderror" name="quantite" value="{{ old('quantite', $stock->quantite) }}" required>
                    @error('quantite') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Sélection du Chantier -->
                <div class="mb-3">
                    <label for="chantier_id" class="form-label">Chantier</label>
                    <select name="chantier_id" class="form-control @error('chantier_id') is-invalid @enderror" required>
                        @foreach(App\Models\Chantier::all() as $chantier)
                            <option value="{{ $chantier->id }}" {{ $stock->chantier_id == $chantier->id ? 'selected' : '' }}>
                                {{ $chantier->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('chantier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Boutons -->
                <div class="text-center">
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Mettre à jour</button>
                    <a href="{{ route('stocks.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
