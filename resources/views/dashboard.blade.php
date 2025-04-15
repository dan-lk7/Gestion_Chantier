@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-center">📊 Tableau de bord</h2>
    <div class="row">
        <!-- ✅ Total des Chantiers -->
        <div class="col-md-3">
            <div class="card shadow-sm border-info">
                <div class="card-body text-center">
                    <h5 class="card-title text-info"><i class="bi bi-building"></i> Chantiers</h5>
                    <h2 class="fw-bold">{{ $totalChantiers }}</h2>
                    <a href="{{ route('chantiers.index') }}" class="btn btn-outline-info btn-sm mt-2">📍 Voir les chantiers</a>
                </div>
            </div>
        </div>
    <div class="row">
        <!-- Total des Stocks -->
        <div class="col-md-4">
            <div class="card shadow-sm border-primary">
                <div class="card-body text-center">
                    <h5 class="card-title text-primary"><i class="bi bi-box-seam"></i> Stocks</h5>
                    <h2 class="fw-bold">{{ $totalStocks }}</h2>
                </div>
            </div>
        </div>

        <!-- Total des Mouvements -->
        <div class="col-md-4">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center">
                    <h5 class="card-title text-success"><i class="bi bi-arrow-left-right"></i> Mouvements</h5>
                    <h2 class="fw-bold">{{ $totalMovements }}</h2>
                </div>
            </div>
        </div>

        <!-- Stocks faibles -->
        <div class="col-md-4">
            <div class="card shadow-sm border-danger">
                <div class="card-body text-center">
                    <h5 class="card-title text-danger"><i class="bi bi-exclamation-triangle"></i> Stocks faibles</h5>
                    <h2 class="fw-bold">{{ count($stocksFaibles) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row mt-5">
        <div class="col-md-6">
            <h5 class="text-center">📦 Répartition des stocks</h5>
            <canvas id="stocksChart"></canvas>
        </div>
        <div class="col-md-6">
            <h5 class="text-center">📊 Mouvements des stocks</h5>
            <canvas id="movementsChart"></canvas>
        </div>
    </div>

    <!-- Dernières opérations -->
    <div class="mt-5">
        <h4 class="mb-3">📝 Dernières opérations</h4>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th>Stock</th>
                    <th>Type</th>
                    <th>Quantité</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dernieresOperations as $operation)
                    <tr>
                        <td>{{ $operation->date }}</td>
                        <td>{{ $operation->stock ? $operation->stock->nom : 'Stock inconnu' }}</td>
                        <td>
                            <span class="badge {{ $operation->type == 'entrée' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($operation->type) }}
                            </span>
                        </td>
                        <td>{{ $operation->quantite }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Importation de Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Données pour le graphique des stocks
        const stocksData = {
            labels: @json($stocksFaibles->pluck('nom')), // Récupère les noms des stocks faibles
            datasets: [{
                label: 'Quantité restante',
                data: @json($stocksFaibles->pluck('quantite')), // Récupère les quantités
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        };

        const movementsData = {
            labels: ['Entrées', 'Utilisés'],
            datasets: [{
                label: 'Mouvements',
                data: [
                    {{ $dernieresOperations->where('type', 'entrée')->count() }},
                    {{ $dernieresOperations->where('type', 'utilisé')->count() }}
                ],
                backgroundColor: ['#4CAF50', '#F44336']
            }]
        };

        // Configuration et initialisation des graphiques
        new Chart(document.getElementById('stocksChart'), {
            type: 'bar',
            data: stocksData,
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        new Chart(document.getElementById('movementsChart'), {
            type: 'pie',
            data: movementsData,
            options: {
                responsive: true
            }
        });
    });
</script>
@endsection