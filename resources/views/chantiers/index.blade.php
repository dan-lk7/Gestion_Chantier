@extends('layouts.app')

@section('header', 'Gestion des Chantiers')

@section('content')
<div class="container mt-4">
    <h2 class="text-center">🏗️ Liste des Chantiers</h2>

    <a href="{{ route('chantiers.create') }}" class="btn btn-success mb-3">➕ Ajouter un Chantier</a>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Adresse</th>
                    <th>Pays</th>
                    <th>Responsable</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chantiers as $chantier)
                    <tr>
                        <td>{{ $chantier->id }}</td>
                        <td>{{ $chantier->nom }}</td>
                        <td>{{ $chantier->adresse }}</td>
                        <td>{{ $chantier->pays }}</td>
                        <td>{{ $chantier->responsable ? $chantier->responsable->name : 'Non assigné' }}</td>
                        <td>
                            <a href="{{ route('chantiers.edit', $chantier->id) }}" class="btn btn-warning btn-sm">✏️ Modifier</a>
                            <form action="{{ route('chantiers.destroy', $chantier->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce chantier ?')">🗑️ Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
