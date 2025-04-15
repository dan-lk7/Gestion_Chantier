<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Movement;
use App\Models\Chantier;
class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user && $user->role && $user->role->nom == 'Admin') {
            return view('dashboard', [
                'totalChantiers' => \App\Models\Chantier::count(),
                'totalStocks' => Stock::count(),
                'totalMovements' => Movement::count(),
                'stocksFaibles' => Stock::where('quantite', '<=', 25)->get(),
                'dernieresOperations' => Movement::latest()->take(5)->get(),
            ]);
        }
        if ($user->role->nom == 'Responsable') {
            $chantier = $user->chantier;
            $stocks = Stock::where('chantier_id', $chantier->id)->get();
            $stocksFaibles = $stocks->filter(fn($stock) => $stock->quantite <= 10);
            $mouvements = Movement::with(['stock', 'user'])
                ->whereHas('stock', fn($q) => $q->where('chantier_id', $chantier->id))
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard_responsable', [
                'chantier' => $chantier,
                'totalStocks' => $stocks->count(),
                'stocksFaibles' => $stocksFaibles,
                'mouvements' => $mouvements
            ]);
        }
        if ($user->role->nom == 'Auditeur') {
            return view('dashboard_auditeur', [
                'totalStocks' => Stock::count(),
                'totalMovements' => Movement::count(),
                'stocksFaibles' => Stock::where('quantite', '<=', 10)->get(),
                'dernieresOperations' => Movement::latest()->take(5)->get(),
            ]);
        }
    }
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function dashboardResponsable()
    {
        $user = auth()->user();
        $chantier = $user->chantier;

        $stocks = \App\Models\Stock::where('chantier_id', $chantier->id)->get();
        $stocksFaibles = $stocks->filter(fn($stock) => $stock->quantite <= 10);
        $mouvements = \App\Models\Movement::with(['stock', 'user'])
            ->whereHas('stock', function ($query) use ($chantier) {
                $query->where('chantier_id', $chantier->id);
            })
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard_responsable', [
            'chantier' => $chantier,
            'totalStocks' => $stocks->count(),
            'stocksFaibles' => $stocksFaibles,
            'mouvements' => $mouvements
        ]);
    }
}
