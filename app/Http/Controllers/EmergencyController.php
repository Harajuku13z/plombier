<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmergencyController extends Controller
{
    /**
     * Afficher la page SOS URGENCE
     */
    public function index()
    {
        return view('emergency.index');
    }

    /**
     * Soumettre une demande d'urgence
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'emergency_type' => 'required|string',
            'address' => 'required|string|max:500',
            'description' => 'required|string|max:2000',
            'photos.*' => 'nullable|image|max:5120', // 5MB max par image
        ], [
            'name.required' => 'Le nom est requis',
            'email.required' => 'L\'email est requis',
            'email.email' => 'L\'email doit être valide',
            'phone.required' => 'Le téléphone est requis',
            'emergency_type.required' => 'Le type d\'urgence est requis',
            'address.required' => 'L\'adresse est requise',
            'description.required' => 'La description est requise',
            'photos.*.image' => 'Les fichiers doivent être des images',
            'photos.*.max' => 'Chaque image ne doit pas dépasser 5MB',
        ]);

        try {
            // Créer la soumission
            $submission = Submission::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'work_type' => 'URGENCE',
                'emergency_type' => $validated['emergency_type'],
                'address' => $validated['address'],
                'message' => $validated['description'],
                'is_emergency' => true,
                'status' => 'IN_PROGRESS',
                'urgency_level' => 'urgent', // Très urgent
            ]);

            // Gérer les photos
            if ($request->hasFile('photos')) {
                $photoPaths = [];
                foreach ($request->file('photos') as $photo) {
                    $filename = Str::random(20) . '.' . $photo->getClientOriginalExtension();
                    $path = $photo->storeAs('submissions/' . $submission->id, $filename, 'public');
                    $photoPaths[] = $path;
                }
                $submission->update(['photos' => json_encode($photoPaths)]);
            }

            // Vérifier si l'email est activé
            $emailEnabled = setting('email_enabled', false);
            
            // Envoyer l'email de confirmation à l'utilisateur
            if ($emailEnabled) {
                try {
                    if (!empty($submission->email)) {
                        Log::info('Sending confirmation email to client', ['to' => $submission->email]);
                        
                        Mail::send('emails.emergency-confirmation', [
                            'submission' => $submission,
                            'emergency_type' => $validated['emergency_type'],
                        ], function ($message) use ($submission) {
                            $message->to($submission->email)
                                    ->subject('✅ Votre demande d\'urgence a été reçue - Référence #' . str_pad($submission->id, 4, '0', STR_PAD_LEFT));
                        });
                        
                        Log::info('✅ Confirmation email sent to client');
                    } else {
                        Log::warning('No email address for client, confirmation email not sent');
                    }
                } catch (\Exception $e) {
                    Log::error('Erreur envoi email confirmation client', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            } else {
                Log::warning('Email is disabled in settings, confirmation email not sent');
            }

            // Envoyer l'email d'urgence à l'admin
            if ($emailEnabled) {
                try {
                    $companyEmail = setting('company_email', config('company.email'));
                    
                    if ($companyEmail) {
                        Log::info('Sending emergency notification email to admin', ['to' => $companyEmail]);
                        
                        Mail::send('emails.emergency-submission', [
                            'submission' => $submission,
                            'emergency_type' => $validated['emergency_type'],
                        ], function ($message) use ($companyEmail, $submission) {
                            $message->to($companyEmail)
                                    ->subject('🚨 URGENCE PLOMBERIE - ' . $submission->name);
                        });
                        
                        Log::info('✅ Emergency notification email sent to admin');
                    } else {
                        Log::warning('No company email configured, admin notification not sent');
                    }
                } catch (\Exception $e) {
                    Log::error('Erreur envoi email urgence admin', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            } else {
                Log::warning('Email is disabled in settings, admin notification not sent');
            }

            return redirect()->route('urgence.success')->with('success', 'Votre demande d\'urgence a été envoyée. Nous vous contactons dans les plus brefs délais !');

        } catch (\Exception $e) {
            Log::error('Erreur soumission urgence', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            return back()->withInput()->with('error', 'Une erreur est survenue. Veuillez réessayer ou nous appeler directement.');
        }
    }

    /**
     * Page de succès
     */
    public function success()
    {
        return view('emergency.success');
    }
}

