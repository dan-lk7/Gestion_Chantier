@extends('layouts.app')

@section('header', 'Gestion des Stocks')

@section('content')
<div class="container mt-4">
    <h2 class="text-center">📦 Liste des Stocks</h2>

    @if(auth()->user()->role->nom == 'Admin')
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('stocks.create') }}" class="btn btn-success">
                🆕 Ajouter un Stock
            </a>
        </div>
    @endif

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('movements.historique') }}" class="btn btn-info">
            📅 Voir l'historique d'utilisation
        </a>
    </div>

    <div class="table-responsive">
        <table id="datatable" class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Quantité</th>
                    <th>Chantier</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stocks as $stock)
                    <tr>
                        <td>{{ $stock->id }}</td>
                        <td class="stock-name">{{ $stock->nom }}</td>
                        <td>
                            @if($stock->quantite <= 10)
                                <span class="badge bg-danger">{{ $stock->quantite }} 🔴</span>
                            @else
                                <span class="badge bg-success">{{ $stock->quantite }} ✅</span>
                            @endif
                        </td>
                        <td class="stock-chantier">
                            {{ $stock->chantier ? $stock->chantier->nom : 'Aucun chantier' }}
                        </td>
                        <td>
                            @if(auth()->user()->role->nom == 'Admin')
                                <a href="{{ route('stocks.edit', $stock->id) }}" class="btn btn-warning btn-sm">✏️ Modifier</a>

                                <button class="btn btn-info btn-sm btn-transfer" 
                                    data-stock-id="{{ $stock->id }}" 
                                    data-stock-name="{{ $stock->nom }}" 
                                    data-stock-quantite="{{ $stock->quantite }}"
                                    data-chantier-source-id="{{ $stock->chantier_id }}">
                                    🔄 Transférer
                                </button>

                                <button class="btn btn-primary btn-sm btn-use" 
                                    data-stock-id="{{ $stock->id }}" 
                                    data-stock-name="{{ $stock->nom }}" 
                                    data-stock-quantite="{{ $stock->quantite }}">
                                    🏗️ Utiliser
                                </button>

                                <form action="{{ route('stocks.destroy', $stock->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce stock ?')">
                                        🗑️ Supprimer
                                    </button>
                                </form>
                            @else
                                <span class="text-muted">🔒 Accès restreint</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- ✅ MODAL DE TRANSFERT -->
        <div class="modal fade" id="modalTransfer" tabindex="-1" aria-labelledby="modalTransferLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">🔄 Transférer un Stock</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formTransfer" action="{{ route('stocks.transfer') }}" method="POST">
                            @csrf
                            <input type="hidden" id="transfer_stock_id" name="stock_id">
                            <input type="hidden" id="transfer_chantier_source_id" name="chantier_source_id">

                            <div class="mb-3">
                                <label class="form-label">Nom du Stock</label>
                                <input type="text" id="transfer_stock_name" class="form-control" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Quantité à transférer</label>
                                <input type="number" id="transfer_quantite" name="quantite" class="form-control" required min="1">
                            </div>

                            <div class="mb-3">
                                <label for="transfer_chantier_id" class="form-label">Chantier de destination</label>
                                <select id="transfer_chantier_id" name="destination_chantier_id" class="form-select" required>
                                    <option value="">-- Sélectionner un chantier --</option>
                                    @foreach($chantiers as $chantier)
                                        <option value="{{ $chantier->id }}">{{ $chantier->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success">✅ Confirmer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ✅ MODAL D'UTILISATION -->
        <div class="modal fade" id="modalUse" tabindex="-1" aria-labelledby="modalUseLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">🏗️ Utilisation du Stock</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formUse" action="{{ route('stocks.use') }}" method="POST">
                            @csrf
                            <input type="hidden" id="use_stock_id" name="stock_id">

                            <div class="mb-3">
                                <label class="form-label">Nom du Stock</label>
                                <input type="text" id="use_stock_name" class="form-control" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Quantité utilisée</label>
                                <input type="number" id="use_quantite" name="quantite" class="form-control" required min="1">
                            </div>

                            <button type="submit" class="btn btn-primary">✅ Confirmer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ✅ SCRIPT JQUERY -->
<script>
    $(document).ready(function () {
        let table = $('#datatable').DataTable();

        // 🔍 Recherche directe
        $('#searchStock').on('keyup', function () {
            table.search(this.value).draw();
        });

        // 🔄 Filtrage par chantier
        $('#filterChantier').on('change', function () {
            let chantier = this.value;
            if (chantier) {
                table.column(3).search(chantier).draw();
            } else {
                table.column(3).search("").draw();
            }
        });

        // 🔄 Ouverture de la modale de transfert
        $(document).on('click', '.btn-transfer', function () {
            let stockId = $(this).data('stock-id');
            let stockName = $(this).data('stock-name');
            let stockQuantite = $(this).data('stock-quantite');
            let chantierSourceId = $(this).data('chantier-source-id');

            $('#transfer_stock_id').val(stockId);
            $('#transfer_stock_name').val(stockName);
            $('#transfer_quantite').attr('max', stockQuantite);
            $('#transfer_chantier_source_id').val(chantierSourceId);

            $('#modalTransfer').modal('show');
        });

        // 🏗️ Utilisation
        $(document).on('click', '.btn-use', function () {
            let stockId = $(this).data('stock-id');
            let stockName = $(this).data('stock-name');
            let stockQuantite = $(this).data('stock-quantite');

            $('#use_stock_id').val(stockId);
            $('#use_stock_name').val(stockName);
            $('#use_quantite').attr('max', stockQuantite);

            $('#modalUse').modal('show');
        });
    });
</script>
@endsection
