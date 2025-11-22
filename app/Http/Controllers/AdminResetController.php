<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminResetController extends Controller
{
    /**
     * Afficher le formulaire de vérification du code super user
     */
    public function showSuperUserForm()
    {
        return view('admin.reset.super-user');
    }

    /**
     * Vérifier le code super user
     */
    public function verifySuperUser(Request $request)
    {
        $request->validate([
            'super_user_code' => 'required|string',
        ]);

        $superUserCode = 'elizo';

        if ($request->super_user_code !== $superUserCode) {
            Log::warning('Tentative d\'accès à la page de réinitialisation avec un code incorrect', [
                'ip' => $request->ip(),
                'code_provided' => $request->super_user_code,
            ]);

            return back()->withInput()->with('error', 'Code super user incorrect.');
        }

        // Stocker la vérification en session
        session(['super_user_verified' => true, 'super_user_verified_at' => now()]);

        return redirect()->route('admin.reset.password.form');
    }

    /**
     * Afficher le formulaire de réinitialisation du mot de passe
     */
    public function showResetForm(Request $request)
    {
        // Vérifier que le code super user a été validé
        if (!session('super_user_verified')) {
            return redirect()->route('admin.reset.super-user')->with('error', 'Vous devez d\'abord vérifier le code super user.');
        }

        return view('admin.reset.password');
    }

    /**
     * Réinitialiser le mot de passe admin
     */
    public function resetPassword(Request $request)
    {
        // Vérifier que le code super user a été validé
        if (!session('super_user_verified')) {
            return redirect()->route('admin.reset.super-user')->with('error', 'Session expirée. Veuillez recommencer.');
        }

        $request->validate([
            'username' => 'required|string|email',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
        ], [
            'username.required' => 'L\'email est requis.',
            'username.email' => 'L\'email doit être valide.',
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password_confirmation.required' => 'La confirmation du mot de passe est requise.',
            'password_confirmation.same' => 'Les mots de passe ne correspondent pas.',
        ]);

        try {
            // Mettre à jour dans le fichier AdminController.php (méthode principale)
            $adminControllerPath = app_path('Http/Controllers/AdminController.php');
            if (file_exists($adminControllerPath)) {
                $content = file_get_contents($adminControllerPath);
                
                // Remplacer l'username
                $content = preg_replace(
                    "/\$adminUsername = '[^']*';/",
                    "\$adminUsername = '" . addslashes($request->username) . "';",
                    $content
                );
                
                // Remplacer le password
                $content = preg_replace(
                    "/\$adminPassword = '[^']*';/",
                    "\$adminPassword = '" . addslashes($request->password) . "';",
                    $content
                );
                
                file_put_contents($adminControllerPath, $content);
            }

            // Essayer de sauvegarder dans la base de données si disponible
            try {
                if (class_exists('\App\Models\Setting')) {
                    Setting::set('admin_username', $request->username);
                    Setting::set('admin_password', $request->password);
                }
            } catch (\Exception $e) {
                // Ignorer si la base de données n'est pas disponible
                Log::warning('Impossible de sauvegarder dans la base de données', [
                    'error' => $e->getMessage(),
                ]);
            }
            
            Log::info('Mot de passe admin réinitialisé via la page secrète', [
                'username' => $request->username,
                'ip' => $request->ip(),
            ]);

            // Supprimer la session de vérification
            session()->forget(['super_user_verified', 'super_user_verified_at']);

            return redirect()->route('admin.reset.success')->with('success', 'Mot de passe admin réinitialisé avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la réinitialisation du mot de passe admin', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return back()->withInput()->with('error', 'Une erreur est survenue lors de la réinitialisation. Veuillez réessayer.');
        }
    }

    /**
     * Afficher la page de succès
     */
    public function showSuccess()
    {
        return view('admin.reset.success');
    }
}

