<?php
/**
 * Test d'envoi d'email d'urgence avec la dernière soumission
 * À exécuter via : php test-emergency-email-send.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TEST ENVOI EMAIL URGENCE ===\n\n";

// Récupérer la dernière soumission d'urgence
$submission = App\Models\Submission::where('is_emergency', true)
    ->orderBy('id', 'desc')
    ->first();

if (!$submission) {
    echo "❌ Aucune soumission d'urgence trouvée\n";
    exit;
}

echo "Soumission trouvée:\n";
echo "  - ID: {$submission->id}\n";
echo "  - Nom: {$submission->name}\n";
echo "  - Email: {$submission->email}\n";
echo "  - Photos: " . count($submission->photos ?? []) . "\n\n";

// Récupérer l'email admin
$adminEmail = App\Models\Setting::get('admin_notification_email') ?: App\Models\Setting::get('company_email');
echo "Email admin: {$adminEmail}\n\n";

// Test 1: Email sans pièces jointes
echo "TEST 1 - Email SANS pièces jointes:\n";
try {
    Illuminate\Support\Facades\Mail::send('emails.emergency-submission', [
        'submission' => $submission,
        'emergency_type' => $submission->emergency_type ?? 'fuite',
    ], function ($mail) use ($adminEmail, $submission) {
        $mail->to($adminEmail)
             ->subject('🚨 TEST URGENCE (sans PJ) - ' . $submission->name);
    });
    echo "  ✅ Email envoyé avec succès (SANS pièces jointes)\n\n";
} catch (\Exception $e) {
    echo "  ❌ Erreur: " . $e->getMessage() . "\n";
    echo "  Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
}

// Test 2: Email avec pièces jointes
echo "TEST 2 - Email AVEC pièces jointes:\n";
try {
    Illuminate\Support\Facades\Mail::send('emails.emergency-submission', [
        'submission' => $submission,
        'emergency_type' => $submission->emergency_type ?? 'fuite',
    ], function ($mail) use ($adminEmail, $submission) {
        $mail->to($adminEmail)
             ->subject('🚨 TEST URGENCE (avec PJ) - ' . $submission->name);
        
        // Attacher les photos
        if ($submission->photos && count($submission->photos) > 0) {
            echo "  Attachement de " . count($submission->photos) . " photo(s):\n";
            foreach ($submission->photos as $photoPath) {
                $fullPath = storage_path('app/public/' . $photoPath);
                echo "    - Chemin: {$fullPath}\n";
                echo "    - Existe: " . (file_exists($fullPath) ? 'OUI' : 'NON') . "\n";
                
                if (file_exists($fullPath)) {
                    try {
                        $mail->attach($fullPath, [
                            'as' => basename($photoPath),
                            'mime' => mime_content_type($fullPath),
                        ]);
                        echo "    ✅ Photo attachée\n";
                    } catch (\Exception $attachError) {
                        echo "    ❌ Erreur attachement: " . $attachError->getMessage() . "\n";
                    }
                }
            }
        }
    });
    echo "  ✅ Email envoyé avec succès (AVEC pièces jointes)\n\n";
} catch (\Exception $e) {
    echo "  ❌ Erreur: " . $e->getMessage() . "\n";
    echo "  Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
}

echo "=== FIN DU TEST ===\n";
echo "\nVérifiez votre boîte email (et les spams) : {$adminEmail}\n";

