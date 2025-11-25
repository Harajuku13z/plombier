<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Setting;
use App\Models\Client;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PlumbingSimulatorController extends Controller
{
    /**
     * Étapes du simulateur
     */
    private $steps = [
        'work-type',      // Type de travaux (sélection multiple)
        'urgency',        // Niveau d'urgence
        'property-type',  // Type de bien
        'photos',         // Upload photos
        'contact',        // Informations de contact
    ];

    /**
     * Types de travaux de plomberie
     */
    private function getWorkTypes()
    {
        return [
            'debouchage' => [
                'name' => 'Débouchage de canalisation',
                'icon' => 'fa-toilet',
                'description' => 'Éviers, lavabos, WC, douches bouchés'
            ],
            'fuite' => [
                'name' => 'Réparation de fuite',
                'icon' => 'fa-tint',
                'description' => 'Fuite d\'eau, robinetterie, tuyauterie'
            ],
            'sanitaires' => [
                'name' => 'Installation sanitaires',
                'icon' => 'fa-bath',
                'description' => 'Lavabo, évier, WC, douche, baignoire'
            ],
            'chauffe-eau' => [
                'name' => 'Chauffe-eau',
                'icon' => 'fa-fire',
                'description' => 'Installation, remplacement, réparation'
            ],
            'salle-bain' => [
                'name' => 'Rénovation salle de bain',
                'icon' => 'fa-home',
                'description' => 'Rénovation complète ou partielle'
            ],
            'chauffage' => [
                'name' => 'Installation chauffage',
                'icon' => 'fa-thermometer-half',
                'description' => 'Radiateurs, chaudière, plancher chauffant'
            ],
            'canalisation' => [
                'name' => 'Rénovation canalisations',
                'icon' => 'fa-toolbox',
                'description' => 'Remplacement tuyauterie complète'
            ],
            'autre' => [
                'name' => 'Autre demande',
                'icon' => 'fa-question-circle',
                'description' => 'Décrivez votre projet'
            ],
        ];
    }

    /**
     * Page d'accueil du simulateur
     */
    public function index()
    {
        session()->forget('simulator_data');
        return redirect()->route('simulator.step', 'work-type');
    }

    /**
     * Afficher une étape
     */
    public function showStep($step)
    {
        if (!in_array($step, $this->steps)) {
            return redirect()->route('simulator.index');
        }

        $data = session('simulator_data', []);
        $workTypes = $this->getWorkTypes();
        
        $companySettings = [
            'name' => Setting::get('company_name', 'Plombier Versailles'),
            'phone' => Setting::get('company_phone', '07 86 48 65 39'),
            'city' => Setting::get('company_city', 'Versailles'),
        ];

        $currentStepIndex = array_search($step, $this->steps);
        $totalSteps = count($this->steps);
        $progress = round((($currentStepIndex + 1) / $totalSteps) * 100);

        return view('simulator.steps.' . $step, compact(
            'step',
            'data',
            'workTypes',
            'companySettings',
            'progress',
            'currentStepIndex',
            'totalSteps'
        ));
    }

    /**
     * Soumettre une étape
     */
    public function submitStep(Request $request, $step)
    {
        if (!in_array($step, $this->steps)) {
            return redirect()->route('simulator.index');
        }

        $data = session('simulator_data', []);

        // Validation selon l'étape
        switch ($step) {
            case 'work-type':
                $validated = $request->validate([
                    'work_types' => 'required|array|min:1',
                    'work_types.*' => 'string',
                    'description' => 'nullable|string|max:500',
                ]);
                break;

            case 'urgency':
                $validated = $request->validate([
                    'urgency' => 'required|in:normal,urgent,emergency',
                ]);
                break;

            case 'property-type':
                $validated = $request->validate([
                    'property_type' => 'required|in:house,apartment,commercial,other',
                ]);
                break;

            case 'photos':
                $validated = [];
                
                // Gérer l'upload des photos
                if ($request->hasFile('photos')) {
                    $photoPaths = [];
                    foreach ($request->file('photos') as $photo) {
                        if ($photo->isValid()) {
                            $filename = \Illuminate\Support\Str::random(20) . '.' . $photo->getClientOriginalExtension();
                            $path = $photo->storeAs('simulator-temp', $filename, 'public');
                            $photoPaths[] = $path;
                        }
                    }
                    // Stocker seulement les chemins (strings), pas les objets UploadedFile
                    if (!empty($photoPaths)) {
                        $validated['photo_paths'] = $photoPaths;
                    }
                } else {
                    // Pas de photos, continuer quand même
                    $validated = [];
                }
                break;

            case 'contact':
                // Validation simplifiée
                $validated = $request->validate([
                    'name' => 'required|string|min:2',
                    'email' => 'required|email',
                    'phone' => 'required|string|min:10',
                    'address' => 'required|string|min:5',
                    'city' => 'required|string|min:2',
                    'postal_code' => 'required|string|min:4',
                ], [
                    'name.required' => '❌ Le NOM est obligatoire',
                    'name.min' => '❌ Le NOM est trop court (minimum 2 caractères)',
                    'email.required' => '❌ L\'EMAIL est obligatoire',
                    'email.email' => '❌ L\'EMAIL n\'est pas valide (doit contenir @)',
                    'phone.required' => '❌ Le TÉLÉPHONE est obligatoire',
                    'phone.min' => '❌ Le TÉLÉPHONE est trop court (minimum 10 caractères)',
                    'address.required' => '❌ L\'ADRESSE est obligatoire',
                    'address.min' => '❌ L\'ADRESSE est trop courte (minimum 5 caractères)',
                    'city.required' => '❌ La VILLE est obligatoire',
                    'city.min' => '❌ La VILLE est trop courte (minimum 2 caractères)',
                    'postal_code.required' => '❌ Le CODE POSTAL est obligatoire',
                    'postal_code.min' => '❌ Le CODE POSTAL est trop court (minimum 4 caractères)',
                ]);
                
                Log::info('Contact validation passed', [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ]);
                break;

            default:
                $validated = $request->all();
        }

        // Sauvegarder les données
        $data = array_merge($data, $validated);
        session(['simulator_data' => $data]);
        
        Log::info('Simulator step submitted', [
            'step' => $step,
            'validated' => $validated,
            'session_data_keys' => array_keys($data),
        ]);

        // Si c'est l'étape contact, créer la soumission
        if ($step === 'contact') {
            Log::info('Creating submission from contact step', [
                'has_work_types' => isset($data['work_types']),
                'has_email' => isset($data['email']),
                'has_phone' => isset($data['phone']),
            ]);
            return $this->createSubmission($data);
        }

        // Passer à l'étape suivante
        $currentIndex = array_search($step, $this->steps);
        $nextStep = $this->steps[$currentIndex + 1] ?? 'summary';

        return redirect()->route('simulator.step', $nextStep);
    }

    /**
     * Créer la soumission
     */
    private function createSubmission($data)
    {
        try {
            Log::info('Creating submission', ['data_keys' => array_keys($data)]);
            
            $workTypes = $this->getWorkTypes();
            
            // Gérer les types de travaux multiples
            $selectedWorkTypes = $data['work_types'] ?? [];
            
            if (empty($selectedWorkTypes)) {
                Log::error('No work types selected');
                return back()->with('error', 'Veuillez sélectionner au moins un type de travaux');
            }
            
            $workTypeNames = array_map(function($key) use ($workTypes) {
                return $workTypes[$key]['name'] ?? $key;
            }, $selectedWorkTypes);
            $workTypesList = implode(', ', $workTypeNames);
            
            Log::info('Work types processed', [
                'selected' => $selectedWorkTypes,
                'names' => $workTypeNames,
            ]);

            $urgencyLabels = [
                'normal' => 'Normal (sous 2-4 semaines)',
                'urgent' => 'Urgent (sous 1 semaine)',
                'emergency' => 'Urgence (dans les 48h)',
            ];

            $propertyLabels = [
                'house' => 'Maison',
                'apartment' => 'Appartement',
                'commercial' => 'Commercial',
                'other' => 'Autre',
            ];

            // Créer le message récapitulatif
            $message = "=== DEMANDE VIA SIMULATEUR DE PRIX ===\n\n";
            $message .= "Types de travaux : {$workTypesList}\n";
            $message .= "Urgence : " . ($urgencyLabels[$data['urgency']] ?? $data['urgency']) . "\n";
            $message .= "Type de bien : " . ($propertyLabels[$data['property_type']] ?? $data['property_type']) . "\n\n";
            
            if (!empty($data['description'])) {
                $message .= "Description :\n{$data['description']}\n\n";
            }

            $message .= "Adresse : {$data['address']}, {$data['postal_code']} {$data['city']}\n";

            // Vérifier que toutes les données requises sont présentes
            if (!isset($data['phone']) || !isset($data['email'])) {
                Log::error('Missing required data', ['data' => $data]);
                return back()->with('error', 'Données de session manquantes. Veuillez recommencer le simulateur.');
            }
            
            // Créer la soumission avec les champs du modèle
            Log::info('Creating submission object');
            
            $submission = new Submission();
            $submission->session_id = session()->getId();
            // Convertir en majuscules pour l'ENUM
            $propertyTypeMap = [
                'house' => 'HOUSE',
                'apartment' => 'APARTMENT',
                'commercial' => 'HOUSE', // Pas dans l'ENUM, on met HOUSE par défaut
                'other' => 'HOUSE',
            ];
            $submission->property_type = $propertyTypeMap[$data['property_type'] ?? 'house'] ?? 'HOUSE';
            $submission->work_types = $selectedWorkTypes; // Array - sera casté automatiquement
            $submission->phone = $data['phone'];
            $submission->email = $data['email'];
            $submission->postal_code = $data['postal_code'] ?? '';
            $submission->city = $data['city'] ?? '';
            $submission->status = 'COMPLETED'; // Formulaire terminé avec succès
            $submission->current_step = 'email'; // Dernière étape
            $submission->completed_at = now(); // Date de complétion
            $submission->ip_address = request()->ip();
            $submission->user_agent = request()->userAgent();
            
            // Stocker toutes les données dans form_data
            $submission->form_data = [
                'name' => $data['name'] ?? '',
                'address' => $data['address'] ?? '',
                'urgency' => $data['urgency'] ?? 'normal',
                'description' => $data['description'] ?? '',
                'work_types_names' => $workTypeNames,
                'photo_paths' => $data['photo_paths'] ?? [],
            ];
            
            Log::info('About to save submission', [
                'email' => $submission->email,
                'phone' => $submission->phone,
                'work_types' => $submission->work_types,
                'form_data' => $submission->form_data,
            ]);
            
            try {
                $submission->save();
                Log::info('Submission saved successfully', ['id' => $submission->id]);
            } catch (\Exception $saveError) {
                Log::error('Error saving submission', [
                    'error' => $saveError->getMessage(),
                    'submission_data' => $submission->toArray(),
                ]);
                throw $saveError;
            }
            
            // Déplacer les photos temporaires vers le dossier permanent
            if (!empty($data['photo_paths']) && is_array($data['photo_paths'])) {
                Log::info('Processing photos from simulator-temp', [
                    'submission_id' => $submission->id,
                    'temp_photos' => $data['photo_paths']
                ]);
                
                $permanentPhotos = [];
                foreach ($data['photo_paths'] as $tempPath) {
                    try {
                        // Le chemin est comme: simulator-temp/xxxxx.jpg
                        if (Storage::disk('public')->exists($tempPath)) {
                            $filename = basename($tempPath);
                            $permanentPath = 'submissions/' . $submission->id . '/' . $filename;
                            
                            // Copier le fichier vers le dossier permanent
                            Storage::disk('public')->copy($tempPath, $permanentPath);
                            
                            // Ajouter au tableau des photos permanentes
                            $permanentPhotos[] = 'storage/' . $permanentPath;
                            
                            Log::info('Photo moved successfully', [
                                'from' => $tempPath,
                                'to' => $permanentPath
                            ]);
                            
                            // Supprimer le fichier temporaire
                            Storage::disk('public')->delete($tempPath);
                        } else {
                            Log::warning('Temp photo not found', ['path' => $tempPath]);
                        }
                    } catch (\Exception $photoError) {
                        Log::error('Error moving photo', [
                            'temp_path' => $tempPath,
                            'error' => $photoError->getMessage()
                        ]);
                    }
                }
                
                // Mettre à jour tracking_data avec les photos permanentes
                if (!empty($permanentPhotos)) {
                    $trackingData = $submission->tracking_data ?? [];
                    $trackingData['photos'] = $permanentPhotos;
                    $submission->tracking_data = $trackingData;
                    $submission->save();
                    
                    Log::info('Photos saved to tracking_data', [
                        'submission_id' => $submission->id,
                        'photo_count' => count($permanentPhotos),
                        'photos' => $permanentPhotos
                    ]);
                }
            }
            
            // Créer ou mettre à jour le client (lead)
            try {
                $this->createOrUpdateClient($submission, $data);
            } catch (\Exception $clientError) {
                Log::error('Error creating/updating client', [
                    'error' => $clientError->getMessage(),
                ]);
                // Ne pas bloquer si la création du client échoue
            }

            // Envoyer l'email à l'admin/entreprise avec EmailService (pour les photos en PJ)
            try {
                Log::info('Preparing to send admin notification with photos', [
                    'submission_id' => $submission->id,
                    'has_photos' => !empty($submission->tracking_data['photos'] ?? [])
                ]);
                
                // Utiliser EmailService pour l'email admin (avec photos en PJ automatiques)
                $emailService = new \App\Services\EmailService();
                $sent = $emailService->sendSubmissionNotification($submission);
                
                if ($sent) {
                    Log::info('✅ Admin notification sent successfully with EmailService');
                } else {
                    Log::warning('⚠️ EmailService returned false (may be disabled or error)');
                }
                
                // Envoyer un email de confirmation au client
                try {
                    if (!empty($submission->email)) {
                        Log::info('Sending confirmation email to client', ['to' => $submission->email]);
                        
                        $companyEmail = Setting::get('company_email', 'contact@plombier-versailles78.fr');
                        
                        Mail::send('emails.simulator-confirmation', [
                            'submission' => $submission,
                            'data' => $data,
                            'workTypes' => $workTypes,
                            'companySettings' => [
                                'name' => Setting::get('company_name', 'Plombier Versailles'),
                                'phone' => Setting::get('company_phone', '07 86 48 65 39'),
                                'email' => $companyEmail,
                            ],
                        ], function ($mail) use ($submission) {
                            $mail->to($submission->email)
                                 ->subject('✅ Votre demande de devis a été reçue - Référence #' . $submission->id);
                        });
                        
                        Log::info('✅ Confirmation email sent to client');
                    }
                } catch (\Exception $confirmError) {
                    Log::error('Failed to send confirmation email to client', [
                        'error' => $confirmError->getMessage(),
                    ]);
                }
                
            } catch (\Exception $e) {
                Log::error('Erreur générale email simulateur', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                // Ne pas bloquer même si l'email échoue
            }

            // Rediriger vers la page de succès
            Log::info('Redirecting to success page', [
                'submission_id' => $submission->id,
                'email_sent_to_company' => !empty(Setting::get('company_email')),
                'email_sent_to_client' => !empty($submission->email),
            ]);
            
            session()->forget('simulator_data');
            session()->flash('submission_id', $submission->id);
            session()->flash('client_email', $submission->email);
            
            return redirect()->route('simulator.success');

        } catch (\Exception $e) {
            Log::error('Erreur création soumission simulateur', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
            ]);

            return back()->withInput()->with('error', 'ERREUR : ' . $e->getMessage() . ' (Ligne: ' . $e->getLine() . ')');
        }
    }

    /**
     * Créer ou mettre à jour un client (lead) depuis une soumission
     */
    private function createOrUpdateClient($submission, $data)
    {
        Log::info('Creating or updating client from submission', [
            'submission_id' => $submission->id,
            'email' => $submission->email,
        ]);
        
        // Extraire nom et prénom du champ "name"
        $fullName = $data['name'] ?? '';
        $nameParts = explode(' ', trim($fullName), 2);
        $prenom = $nameParts[0] ?? '';
        $nom = $nameParts[1] ?? $nameParts[0] ?? '';
        
        // Vérifier si un client existe déjà avec cet email
        $client = Client::where('email', $submission->email)->first();
        
        if ($client) {
            // Mettre à jour le client existant
            Log::info('Client existant trouvé, mise à jour', ['client_id' => $client->id]);
            
            $client->update([
                'nom' => $nom ?: $client->nom,
                'prenom' => $prenom ?: $client->prenom,
                'telephone' => $submission->phone,
                'code_postal' => $submission->postal_code,
                'ville' => $submission->city,
                'adresse' => $data['address'] ?? $client->adresse,
                'notes' => ($client->notes ? $client->notes . "\n\n" : '') . 
                           "Demande simulateur le " . now()->format('d/m/Y H:i') . 
                           "\nTypes de travaux: " . implode(', ', $data['work_types'] ?? []) .
                           "\nUrgence: " . ($data['urgency'] ?? 'normal'),
            ]);
            
            Log::info('Client mis à jour', ['client_id' => $client->id]);
        } else {
            // Créer un nouveau client (lead)
            Log::info('Création d\'un nouveau client');
            
            $client = Client::create([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $submission->email,
                'telephone' => $submission->phone,
                'adresse' => $data['address'] ?? '',
                'code_postal' => $submission->postal_code,
                'ville' => $submission->city,
                'pays' => 'France',
                'notes' => "Lead créé depuis le simulateur le " . now()->format('d/m/Y H:i') . 
                           "\nTypes de travaux: " . implode(', ', $data['work_types'] ?? []) .
                           "\nUrgence: " . ($data['urgency'] ?? 'normal') .
                           "\nDescription: " . ($data['description'] ?? 'Aucune') .
                           "\nSoumission #" . $submission->id,
            ]);
            
            Log::info('Nouveau client créé', [
                'client_id' => $client->id,
                'email' => $client->email,
            ]);
        }
        
        return $client;
    }

    /**
     * Page de succès
     */
    public function success()
    {
        $submissionId = session('submission_id');
        
        $companySettings = [
            'name' => Setting::get('company_name', 'Plombier Versailles'),
            'phone' => Setting::get('company_phone', '07 86 48 65 39'),
            'email' => Setting::get('company_email', 'contact@plombier-versailles78.fr'),
        ];

        return view('simulator.success', compact('companySettings', 'submissionId'));
    }

    /**
     * Étape précédente
     */
    public function previousStep($step)
    {
        $currentIndex = array_search($step, $this->steps);
        
        if ($currentIndex > 0) {
            $previousStep = $this->steps[$currentIndex - 1];
            return redirect()->route('simulator.step', $previousStep);
        }

        return redirect()->route('simulator.index');
    }
}

