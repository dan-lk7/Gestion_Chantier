@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="text-center mb-5">📅 Historique des stocks utilisés</h2>

    @foreach($movements as $date => $entries)
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📆 {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Type</th>
                                <th>Stock</th>
                                <th>Quantité</th>
                                <th>Utilisateur</th>
                                <th>Chantier Source</th>
                                <th>Chantier Destination</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entries as $mouvement)
                                <tr>
                                    <td>
                                        @switch($mouvement->type)
                                            @case('entrée')
                                                <span class="badge bg-success">Entrée</span>
                                                @break
                                            @case('sortie')
                                                <span class="badge bg-danger">Sortie</span>
                                                @break
                                            @case('utilisé')
                                                <span class="badge bg-primary">Utilisé</span>
                                                @break
                                            @case('transfert')
                                                <span class="badge bg-warning text-dark">Transfert</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">Inconnu</span>
                                        @endswitch
                                    </td>
                                    <td>{{ optional($mouvement->stock)->nom ?? 'Supprimé' }}</td>
                                    <td>{{ $mouvement->quantite }}</td>
                                    <td>{{ optional($mouvement->user)->name ?? 'N/A' }}</td>
                                    <td>{{ optional($mouvement->chantierSource)->nom ?? 'N/A' }}</td>
                                    <td>{{ optional($mouvement->chantierDestination)->nom ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
