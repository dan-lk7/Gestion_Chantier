@extends('layouts.app')

@section('header', 'Tableau de bord Responsable')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-center">👷 Espace Responsable_Chantier : {{ $chantier->nom }}</h2>

    <div class="row">
        <!-- Total Stocks -->
        <div class="col-md-4">
            <div class="card shadow-sm border-primary">
                <div class="card-body text-center">
                    <h5 class="card-title text-primary">📦 Stocks</h5>
                    <h2 class="fw-bold">{{ $totalStocks }}</h2>
                </div>
            </div>
        </div>

        <!-- Nom Chantier -->
        <div class="col-md-4">
            <div class="card shadow-sm border-info">
                <div class="card-body text-center">
                    <h5 class="card-title text-info">📍 Chantier</h5>
                    <h4 class="fw-bold">{{ $chantier->nom }}</h4>
                </div>
            </div>
        </div>

        <!-- Stocks faibles -->
        <div class="col-md-4">
            <div class="card shadow-sm border-danger">
                <div class="card-body text-center">
                    <h5 class="card-title text-danger">⚠️ Stocks faibles</h5>
                    <h2 class="fw-bold">{{ $stocksFaibles->count() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h5 class="text-center">📊 Quantité des stocks faibles</h5>
            <canvas id="stocksChart"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('stocksChart');
        const data = {
            labels: @json($stocksFaibles->pluck('nom')),
            datasets: [{
                label: 'Quantité restante',
                data: @json($stocksFaibles->pluck('quantite')),
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        };
        new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
</script>
@endsection
