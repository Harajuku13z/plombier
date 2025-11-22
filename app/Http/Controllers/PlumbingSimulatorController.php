<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PlumbingSimulatorController extends Controller
{
    /**
     * Étapes du simulateur
     */
    private $steps = [
        'work-type',      // Type de travaux
        'urgency',        // Niveau d'urgence
        'property-type',  // Type de bien
        'contact',        // Informations de contact
        'summary'         // Récapitulatif
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
        $progress = round((($currentStepIndex + 1) / count($this->steps)) * 100);

        return view('simulator.steps.' . $step, compact(
            'step',
            'data',
            'workTypes',
            'companySettings',
            'progress',
            'currentStepIndex'
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
                    'work_type' => 'required|string',
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

            case 'contact':
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'phone' => 'required|string|max:20',
                    'address' => 'required|string|max:500',
                    'city' => 'required|string|max:100',
                    'postal_code' => 'required|string|max:10',
                ]);
                break;

            default:
                $validated = $request->all();
        }

        // Sauvegarder les données
        $data = array_merge($data, $validated);
        session(['simulator_data' => $data]);

        // Si c'est l'étape contact, créer la soumission
        if ($step === 'contact') {
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
            $workTypes = $this->getWorkTypes();
            $workTypeName = $workTypes[$data['work_type']]['name'] ?? $data['work_type'];

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
            $message .= "Type de travaux : {$workTypeName}\n";
            $message .= "Urgence : " . ($urgencyLabels[$data['urgency']] ?? $data['urgency']) . "\n";
            $message .= "Type de bien : " . ($propertyLabels[$data['property_type']] ?? $data['property_type']) . "\n\n";
            
            if (!empty($data['description'])) {
                $message .= "Description :\n{$data['description']}\n\n";
            }

            $message .= "Adresse : {$data['address']}, {$data['postal_code']} {$data['city']}\n";

            // Créer la soumission
            $submission = Submission::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'work_type' => $data['work_type'],
                'property_type' => $data['property_type'],
                'urgency_level' => $data['urgency'],
                'address' => $data['address'],
                'city' => $data['city'],
                'postal_code' => $data['postal_code'],
                'message' => $message,
                'source' => 'simulator',
                'status' => 'pending',
            ]);

            // Envoyer l'email
            try {
                $companyEmail = Setting::get('company_email');
                if ($companyEmail) {
                    Mail::send('emails.simulator-submission', [
                        'submission' => $submission,
                        'data' => $data,
                        'workTypes' => $workTypes,
                    ], function ($mail) use ($companyEmail, $data) {
                        $mail->to($companyEmail)
                             ->subject('Nouvelle demande de devis - Simulateur');
                    });
                }
            } catch (\Exception $e) {
                Log::error('Erreur envoi email simulateur: ' . $e->getMessage());
            }

            // Rediriger vers la page de succès
            session()->forget('simulator_data');
            return redirect()->route('simulator.success')->with('submission_id', $submission->id);

        } catch (\Exception $e) {
            Log::error('Erreur création soumission simulateur', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return back()->withInput()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
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

