@extends('layouts.app')

@section('header', 'Historique des Mouvements')

@section('content')
<div class="container mt-4">
    <h2 class="text-center">🔄 Historique des Mouvements</h2>
    
    <!-- Barre de recherche -->
    <div class="mb-3">
        <input type="text" id="searchMovement" class="form-control" placeholder="🔍 Rechercher un mouvement...">
    </div>

    <!-- Filtrer par type -->
    <div class="mb-3">
        <select id="filterType" class="form-select">
            <option value="">🔄 Tous les types</option>
            <option value="entrée">Entrée</option>
            <option value="transfert">Transfert</option>
            <option value="utilisé">Utilisé</option>
        </select>
    </div>

    <!-- Filtrer par chantier de destination -->
    <div class="mb-3">
        <label for="destination_chantier_id" class="form-label">Chantier de destination (si transfert)</label>
        <select name="destination_chantier_id" id="destination_chantier_id" class="form-select">
            <option value="">-- Sélectionnez un chantier --</option>
            @foreach($chantiers as $chantier)
                <option value="{{ $chantier->id }}">{{ $chantier->nom }}</option>
            @endforeach
        </select>
    </div>

    <div class="table-responsive">
        <table id="datatable" class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
            <th>ID</th>
            <th>Stock</th>
            <th>Type</th>
            <th>Quantité</th>
            <th>Date</th>
            <th>Utilisateur</th>
            <th>Chantier Source</th>
            <th>Chantier de Destination</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($movements as $movement)
                <tr>
                    <td>{{ $movement->id }}</td>
                    <td>{{ optional($movement->stock)->nom ?? 'Non défini' }}</td>
                    <td class="movement-type">
                    @if($movement->type == 'entrée')
                        <span class="badge bg-success">Entrée</span>
                    @elseif($movement->type == 'utilisé')
                        <span class="badge bg-primary">Utilisé</span> <!-- ✅ Ajouté -->
                    @else
                        <span class="badge bg-warning">Transfert</span>
                    @endif
                    </td>
                    <td>{{ $movement->quantite }}</td>
                    <td>{{ $movement->date }}</td>
                    <td>{{ optional($movement->user)->name ?? 'Non défini' }}</td>
                    <td>{{ optional($movement->chantierSource)->nom ?? optional($movement->stock->chantier)->nom ?? 'N/A' }}</td>
                    <td>{{ optional($movement->chantierDestination)->nom ?? 'N/A' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function () {
        let table = $('#datatable').DataTable();

        // Recherche en temps réel
        $('#searchMovement').on('keyup', function () {
            table.search(this.value).draw();
        });

        // Filtrer par type
        $('#filterType').on('change', function () {
            let type = this.value;
            if (type) {
                table.column(2).search(type).draw();
            } else {
                table.column(2).search('').draw();
            }
        });

        // Filtrer par chantier de destination
        $('#destination_chantier_id').on('change', function () {
            let chantierNom = $("#destination_chantier_id option:selected").text();
            if (chantierNom) {
                table.column(6).search(chantierNom).draw();
            } else {
                table.column(6).search('').draw();
            }
        });
    });
</script>
@endsection