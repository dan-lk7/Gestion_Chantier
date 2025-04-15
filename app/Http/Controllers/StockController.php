<?php

namespace App\Http\Controllers;
use App\Models\Stock;
use Illuminate\Http\Request;
use App\Models\Chantier;
use App\Models\Movement;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role->nom == 'Admin' || $user->role->nom == 'Auditeur') {
            $stocks = Stock::with('chantier')->get();
            $chantiers = Chantier::all();
            $chantierSourceId = null;
            $chantierDestinationId = null;
        } else {
            $stocks = Stock::with('chantier')->where('chantier_id', $user->chantier_id)->get();
            $chantiers = Chantier::where('id', '!=', $user->chantier_id)->get(); // chantiers de destination potentiels
            $chantierSourceId = $user->chantier_id;
            $chantierDestinationId = null; // ou un chantier par défaut si nécessaire
        }

        return view('stocks.index', compact('stocks', 'chantiers', 'chantierSourceId', 'chantierDestinationId'));
    }
    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->role->nom != 'Admin') {
            return redirect()->route('stocks.index')->with('error', 'Accès refusé');
        }
        $request->validate([
            'nom' => 'required|string|max:255',
            'quantite' => 'required|integer',
            'chantier_id' => 'required|exists:chantiers,id'
        ]);
        $stock = Stock::create($request->all());
        // Enregistrer le mouvement d'entrée
        Movement::create([
            'stock_id' => $stock->id,
            'user_id' => auth()->id(),
            'type' => 'entrée',
            'quantite' => $stock->quantite,
            'date' => now(),
        ]);
        return redirect()->route('stocks.index')->with('success', 'Stock ajouté avec succès !');

    }
    public function show(Stock $stock)
    {
        return response()->json($stock->load('chantier'));
    }

    public function edit(Stock $stock)
    {
        return view('stocks.edit', compact('stock'));
    }
    public function update(Request $request, Stock $stock)
    {
        $user = auth()->user();
    
        if (!in_array($user->role->nom, ['Admin', 'Responsable'])) {
            return redirect()->route('stocks.index')->with('error', 'Accès refusé');
        }
        if ($user->role->nom == 'Responsable') {
            $stocks = Stock::with('chantier')->where('chantier_id', $user->chantier_id)->get();
            $chantiers = Chantier::where('id', $user->chantier_id)->get();
        }        
        $request->validate([
            'nom' => 'sometimes|string',
            'quantite' => 'sometimes|integer|min:1',
            'chantier_id' => 'sometimes|exists:chantiers,id',
        ]);
    
        $stock->update($request->all());
    
        return redirect()->route('stocks.index')->with('success', 'Stock mis à jour avec succès !');
    }
    public function destroy(Stock $stock)
    {
        $user = auth()->user();
        if ($user->role->nom != 'Admin') {
            return redirect()->route('stocks.index')->with('error', 'Accès refusé');
        }
        // 🔹 Enregistrer un mouvement de **"utilisation"** avant suppression
        Movement::create([
            'stock_id' => $stock->id,
            'user_id' => auth()->id(),
            'type' => 'utilisé',
            'quantite' => $stock->quantite,
            'date' => now(),
            'details' => "Stock utilisé sur chantier"
        ]);

        $stock->delete();
        return redirect()->route('stocks.index')->with('success', 'Stock supprimé avec succès !');
    }
    public function create()
    {
        $chantiers = Chantier::all(); // Récupère tous les chantiers
        return view('stocks.create', compact('chantiers'));
    }
    public function transfer(Request $request)
    {
        $user = auth()->user();
        if ($user->role->nom != 'Admin') {
            return redirect()->route('stocks.index')->with('error', 'Accès refusé');
        }
        $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'quantite' => 'required|integer|min:1',
            'chantier_source_id' => 'required|exists:chantiers,id',
            'destination_chantier_id' => 'required|exists:chantiers,id',
        ]);

        $stockOrigine = Stock::where('id', $request->stock_id)
            ->where('chantier_id', $request->chantier_source_id)
            ->first();

        if (!$stockOrigine) {
            return back()->with('error', 'Stock d\'origine non trouvé.');
        }

        if ($stockOrigine->quantite < $request->quantite) {
            return back()->with('error', 'Quantité insuffisante dans le stock d\'origine.');
        }

        DB::beginTransaction();
        try {
            // Décrémenter manuellement
            $stockOrigine->quantite -= $request->quantite;
            $stockOrigine->save();

            // Recherche ou création du stock dans le chantier destination
            $stockDestination = Stock::firstOrCreate(
                [
                    'nom' => $stockOrigine->nom,
                    'chantier_id' => $request->destination_chantier_id,
                ],
                ['quantite' => 0]
            );
            // Incrémenter manuellement
            $stockDestination->quantite += $request->quantite;
            $stockDestination->save();
            // Enregistrement du mouvement
            Movement::create([
                'stock_id' => $stockOrigine->id,
                'user_id' => auth()->id(),
                'type' => 'transfert',
                'quantite' => $request->quantite,
                'date' => now(),
                'chantier_source_id' => $request->chantier_source_id,
                'destination_chantier_id' => $request->destination_chantier_id,
                'details' => "Transfert de stock vers chantier ID {$request->destination_chantier_id}",
            ]);
            DB::commit();
            return back()->with('success', 'Stock transféré avec succès !');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors du transfert.');
        }
    }
    public function use(Request $request)
    {
        $user = auth()->user();
        if ($user->role->nom != 'Admin') {
            return redirect()->route('stocks.index')->with('error', 'Accès refusé');
        }
        $request->validate([
            'stock_id' => 'required|exists:stocks,id',
            'quantite' => 'required|integer|min:1',
        ]);

        $stock = Stock::findOrFail($request->stock_id);

        if ($stock->quantite < $request->quantite) {
            return redirect()->back()->with('error', 'Stock insuffisant.');
        }
        // Réduire la quantité
        $stock->decrement('quantite', $request->quantite);
        // Enregistrer le mouvement
        Movement::create([
            'stock_id' => $stock->id,
            'user_id' => auth()->id(),
            'type' => 'utilisé',
            'quantite' => $request->quantite,
            'date' => now(),
        ]);
        return redirect()->route('stocks.index')->with('success', 'Stock utilisé avec succès.');
    }
}