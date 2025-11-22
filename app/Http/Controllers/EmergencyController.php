<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

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
                $submission->update(['photos' => $photoPaths]); // Le modèle cast déjà en array
            }

            // Envoyer les emails (inspiré du simulateur)
            try {
                $companyEmail = Setting::get('company_email');
                $adminNotificationEmail = Setting::get('admin_notification_email');
                
                // Déterminer les destinataires
                $recipients = [];
                
                if (!empty($adminNotificationEmail)) {
                    $recipients[] = $adminNotificationEmail;
                    Log::info('Admin notification email configured', ['admin_email' => $adminNotificationEmail]);
                } elseif (!empty($companyEmail)) {
                    $recipients[] = $companyEmail;
                    Log::info('Using company email for notification', ['company_email' => $companyEmail]);
                } else {
                    Log::warning('⚠️ No email configured for notifications');
                }
                
                // Envoyer à tous les destinataires
                foreach ($recipients as $email) {
                    Log::info('Sending emergency notification', ['to' => $email]);
                    
                    try {
                        // Préparer les URLs absolues pour les photos
                        $photoUrls = [];
                        if ($submission->photos && count($submission->photos) > 0) {
                            try {
                                foreach ($submission->photos as $photoPath) {
                                    // Générer l'URL de manière sûre
                                    try {
                                        // Utiliser la route storage.serve pour générer une URL absolue
                                        $photoUrl = url('/storage/' . urlencode($photoPath));
                                        // Forcer HTTPS
                                        $photoUrl = str_replace('http://', 'https://', $photoUrl);
                                        $photoUrls[] = $photoUrl;
                                    } catch (\Exception $urlError) {
                                        Log::warning('Failed to generate photo URL', [
                                            'photo' => $photoPath,
                                            'error' => $urlError->getMessage(),
                                        ]);
                                        // Continuer même si une URL échoue
                                    }
                                }
                            } catch (\Exception $photoError) {
                                Log::warning('Error processing photos for email', [
                                    'error' => $photoError->getMessage(),
                                ]);
                                // Continuer sans les photos si nécessaire
                            }
                        }
                        
                        Mail::send('emails.emergency-submission', [
                            'submission' => $submission,
                            'emergency_type' => $validated['emergency_type'],
                            'photoUrls' => $photoUrls,
                        ], function ($mail) use ($email, $submission) {
                            $mail->to($email)
                                 ->subject('🚨 URGENCE PLOMBERIE - ' . $submission->name . ' - Référence #' . str_pad($submission->id, 4, '0', STR_PAD_LEFT));
                        });
                        
                        Log::info('✅ Emergency notification sent successfully to: ' . $email);
                    } catch (\Exception $mailError) {
                        Log::error('Failed to send emergency notification', [
                            'error' => $mailError->getMessage(),
                            'file' => $mailError->getFile(),
                            'line' => $mailError->getLine(),
                            'trace' => $mailError->getTraceAsString(),
                            'to' => $email,
                        ]);
                    }
                }
                
                // Envoyer un email de confirmation au client
                try {
                    if (!empty($submission->email)) {
                        Log::info('Sending confirmation email to client', ['to' => $submission->email]);
                        
                        Mail::send('emails.emergency-confirmation', [
                            'submission' => $submission,
                            'emergency_type' => $validated['emergency_type'],
                            'companySettings' => [
                                'name' => Setting::get('company_name', 'Plombier Versailles'),
                                'phone' => Setting::get('company_phone', '07 86 48 65 39'),
                                'email' => $companyEmail,
                            ],
                        ], function ($mail) use ($submission) {
                            $mail->to($submission->email)
                                 ->subject('✅ Votre demande d\'urgence a été reçue - Référence #' . str_pad($submission->id, 4, '0', STR_PAD_LEFT));
                        });
                        
                        Log::info('✅ Confirmation email sent to client');
                    }
                } catch (\Exception $confirmError) {
                    Log::error('Failed to send confirmation email to client', [
                        'error' => $confirmError->getMessage(),
                    ]);
                }
                
            } catch (\Exception $e) {
                Log::error('Erreur générale email urgence', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                // Ne pas bloquer même si l'email échoue
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

