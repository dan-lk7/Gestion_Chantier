<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gestion Chantiers') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <!-- ✅ jQuery & DataTables (chargés UNE SEULE FOIS) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

</head>
<body class="font-sans antialiased bg-light">
    <div class="min-h-screen">
        
        @if (!request()->is('login') && !request()->is('register'))
             <!-- ✅ Barre de navigation Bootstrap -->
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <div class="container">
                    <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                        🚧 Gestion Chantiers
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">

                            @auth
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                                        <i class="bi bi-speedometer2"></i> Tableau de bord
                                    </a>
                                </li>

                                @if (auth()->user()->role->nom == 'Admin')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('chantiers') ? 'active' : '' }}" href="{{ route('chantiers.index') }}">
                                            <i class="bi bi-building"></i> Chantiers
                                        </a>
                                    </li>
                                @endif

                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('stocks') ? 'active' : '' }}" href="{{ url('/stocks') }}">
                                        <i class="bi bi-box-seam"></i> Stocks
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('movements') ? 'active' : '' }}" href="{{ url('/movements') }}">
                                        <i class="bi bi-arrow-left-right"></i> Mouvements
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline" onsubmit="return confirm('Voulez-vous vraiment vous déconnecter ?');">
                                        @csrf
                                        <button type="submit" class="nav-link text-danger bg-transparent border-0">
                                            <i class="bi bi-box-arrow-right"></i> Déconnexion
                                        </button>
                                    </form>
                                </li>
                            @endauth

                        </ul>
                    </div>
                </div>
            </nav>

            <!-- ✅ Messages Flash -->
            <div class="container mt-3">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>

            <!-- ✅ En-tête de la page -->
            <header class="bg-white shadow">
                <div class="container py-4">
                    <h2 class="text-center">@yield('header', 'Tableau de bord')</h2>
                </div>
            </header>
        @endif

        <!-- ✅ Contenu principal -->
        <main class="container my-4">
            @yield('content')
        </main>

        @if (!request()->is('login') && !request()->is('register'))
            <!-- ✅ Pied de page -->
            <footer class="bg-dark text-white text-center py-3">
                &copy; {{ date('Y') }} dan-lk7 - Tous droits réservés
            </footer>
        @endif

    </div>

    <!-- ✅ Bootstrap JS (nécessaire pour les modals) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ Script principal -->
    <script>
        $(document).ready(function () {
            console.log("📌 Vérification de jQuery :", typeof jQuery !== "undefined");
            console.log("📌 Vérification de DataTables :", typeof $.fn.DataTable !== "undefined");

            if (!$.fn.DataTable) {
                console.error("❌ DataTables n'est pas chargé !");
                return;
            }

            // ✅ Initialisation correcte de DataTables
            if ($('#datatable').length) {
                $('#datatable').DataTable();
                console.log("✅ DataTable initialisé avec succès !");
            }

            // ✅ Gestion des événements avec $(document).on
            $(document).on('click', '.btn-transfer', function () {
                console.log("📌 Bouton Transférer cliqué !");
                
                let stockId = $(this).data('stock-id');
                let stockName = $(this).data('stock-name');
                let stockQuantite = $(this).data('stock-quantite');

                console.log(`ℹ️ ID: ${stockId}, Nom: ${stockName}, Quantité: ${stockQuantite}`);

                $('#transfer_stock_id').val(stockId);
                $('#transfer_stock_name').val(stockName);
                $('#transfer_quantite').attr('max', stockQuantite);

                $('#modalTransfer').modal('show');
            });

            $(document).on('click', '.btn-use', function () {
                console.log("📌 Bouton Utiliser cliqué !");
                
                let stockId = $(this).data('stock-id');
                let stockName = $(this).data('stock-name');
                let stockQuantite = $(this).data('stock-quantite');

                console.log(`ℹ️ ID: ${stockId}, Nom: ${stockName}, Quantité: ${stockQuantite}`);

                $('#use_stock_id').val(stockId);
                $('#use_stock_name').val(stockName);
                $('#use_quantite').attr('max', stockQuantite);

                $('#modalUse').modal('show');
            });
        });
    </script>
</body>
</html>
