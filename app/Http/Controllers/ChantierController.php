<?php

namespace App\Http\Controllers;
use App\Models\Role;
use App\Models\Chantier;
use Illuminate\Http\Request;
use App\Models\User;
class ChantierController extends Controller
{
    public function __construct()
    {
        // Seul un Admin peut accéder aux routes du contrôleur
        $this->middleware('auth');
    }
    // 🔹 Liste des chantiers
    public function index()
    {
        $user = auth()->user();

        if (!$user || optional($user->role)->nom !== 'Admin') {
            return redirect()->route('dashboard')->with('error', 'Accès refusé.');
        }

        $chantiers = Chantier::all();
        return view('chantiers.index', compact('chantiers'));
    }
    // 🔹 Formulaire d'édition
    public function edit(Chantier $chantier)
    {
        $users = User::all();
        return view('chantiers.edit', compact('chantier', 'users'));
    }
    // 🔹 Mettre à jour un chantier
    public function update(Request $request, Chantier $chantier)
    {
        $request->validate([
            'nom' => 'required|string|unique:chantiers,nom,' . $chantier->id,
            'adresse' => 'required|string',
            'pays' => 'required|string'
        ]);
        $chantier->update($request->all());
        return redirect()->route('chantiers.index')->with('success', 'Chantier mis à jour avec succès !');
    }
    // 🔹 Supprimer un chantier
    public function destroy(Chantier $chantier)
    {
        $chantier->delete();
        return redirect()->route('chantiers.index')->with('success', 'Chantier supprimé avec succès !');
    }
    public function create()
    {
        $users = User::all(); // Récupérer tous les utilisateurs
        return view('chantiers.create', compact('users'));
    }
    public function store(Request $request)
    {
        // Validation des champs
        $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'pays' => 'nullable|string|max:40',
            'responsable_id' => 'nullable|exists:users,id',
        ]);
        // Enregistrement dans la base de données
        Chantier::create([
            'nom' => $request->nom,
            'adresse' => $request->adresse,
            'pays' => $request->pays,
            'responsable_id' => $request->responsable_id,
        ]);
        // Redirection avec un message de succès
        return redirect()->route('chantiers.index')->with('success', 'Chantier ajouté avec succès.');
    }
}
