<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Stock;
use App\Models\Chantier;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role->nom === 'Admin' || $user->role->nom === 'Auditeur') {
            $movements = Movement::with(['stock.chantier', 'user', 'chantierSource', 'chantierDestination'])->get();
            $chantiers = Chantier::all();
        } elseif ($user->role->nom === 'Responsable') {
            // Responsable : voir seulement les mouvements de son chantier
            $movements = Movement::with(['stock.chantier', 'user', 'chantierSource', 'chantierDestination'])
                ->whereHas('stock', function ($q) use ($user) {
                    $q->where('chantier_id', $user->chantier_id);
                })
                ->get();

            $chantiers = Chantier::where('id', $user->chantier_id)->get();
        } else {
            abort(403, 'Accès non autorisé');
        }

        return view('movements.index', compact('movements', 'chantiers'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // Bloquer les Responsables
        if ($user->role->nom === 'Responsable') {
            return response()->json(['message' => 'Accès refusé. Vous ne pouvez pas créer de mouvement.'], 403);
        }

        $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'type' => 'required|in:' . implode(',', Movement::getTypes()),
            'quantite' => 'required|integer|min:1',
            'date' => 'required|date',
            'chantier_source_id' => 'nullable|exists:chantiers,id',
            'destination_chantier_id' => 'nullable|exists:chantiers,id',
        ]);

        $stock = Stock::findOrFail($request->stock_id);

        if (($request->type === 'utilisé' || $request->type === 'transfert') && $stock->quantite < $request->quantite) {
            return response()->json(['message' => 'Quantité insuffisante en stock'], 400);
        }

        if ($request->type === 'entrée') {
            $stock->increment('quantite', $request->quantite);
        } elseif ($request->type === 'utilisé') {
            $stock->decrement('quantite', $request->quantite);
        } elseif ($request->type === 'transfert') {
            $stock->decrement('quantite', $request->quantite);

            $destinationStock = Stock::firstOrCreate(
                [
                    'chantier_id' => $request->destination_chantier_id,
                    'produit_id' => $stock->produit_id
                ],
                ['quantite' => 0]
            );

            $destinationStock->increment('quantite', $request->quantite);
        }
        $movement = Movement::create([
            'stock_id' => $request->stock_id,
            'user_id' => $user->id,
            'type' => $request->type,
            'quantite' => $request->quantite,
            'date' => $request->date,
            'chantier_source_id' => $request->chantier_source_id,
            'destination_chantier_id' => $request->destination_chantier_id
        ]);

        return response()->json($movement, 201);
    }
    public function destroy(Movement $movement)
    {
        $user = auth()->user();

        if ($user->role->nom !== 'Admin') {
            return response()->json(['message' => 'Seul un administrateur peut supprimer un mouvement'], 403);
        }
        $movement->delete();

        return response()->json(['message' => 'Mouvement supprimé avec succès']);
    }
    public function historique()
    {
        $user = auth()->user();

        $query = Movement::with(['stock', 'user', 'chantierSource', 'chantierDestination'])
            ->where('type', 'utilisé'); // Affiche uniquement les "utilisé"
        if ($user->role->nom === 'Responsable') {
            $query->whereHas('stock', function ($q) use ($user) {
                $q->where('chantier_id', $user->chantier_id);
            });
        }
        $movements = $query->orderBy('date', 'desc')->get()
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->date)->format('Y-m-d');
            });
        return view('movements.historique', compact('movements'));
    }
}
