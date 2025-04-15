<?php

namespace App\Http\Controllers\Auth;
use App\Models\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Compter les admins
        $adminCount = User::whereHas('role', function ($query) {
            $query->where('nom', 'Admin');
        })->count();
        // Récupérer les rôles (Sauf Admin si 3 déjà existants)
        $roles = ($adminCount >= 3)
            ? Role::where('nom', '!=', 'Admin')->get()
            : Role::all();
        return view('auth.register', compact('roles'));
    }
    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id']
        ]);
        // Vérifier la limite des Admins
        $role = Role::find($request->role_id);
        if ($role->nom === 'Admin') {
            $adminCount = User::whereHas('role', function ($query) {
                $query->where('nom', 'Admin');
            })->count();

            if ($adminCount >= 3) {
                return redirect()->back()->with('error', 'Le nombre maximum d\'admins est atteint.');
            }
        }
        // Création de l'utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id
        ]);
        event(new Registered($user));
        Auth::login($user);
        return redirect(RouteServiceProvider::HOME);
    }
}
