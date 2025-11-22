<?php
/**
 * Script de diagnostic pour les emails d'urgence
 * À exécuter via : php diagnose-emergency-email.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DIAGNOSTIC EMAIL URGENCE ===\n\n";

// 1. Vérifier la configuration email
echo "1. Configuration Email:\n";
echo "   - email_enabled: " . (App\Models\Setting::get('email_enabled') ? 'OUI' : 'NON') . "\n";
echo "   - company_email: " . (App\Models\Setting::get('company_email') ?: 'NON CONFIGURÉ') . "\n";
echo "   - admin_notification_email: " . (App\Models\Setting::get('admin_notification_email') ?: 'NON CONFIGURÉ') . "\n";
echo "   - mail_host: " . (App\Models\Setting::get('mail_host') ?: 'NON CONFIGURÉ') . "\n";
echo "   - mail_from_address: " . (App\Models\Setting::get('mail_from_address') ?: 'NON CONFIGURÉ') . "\n";
echo "\n";

// 2. Vérifier la dernière soumission d'urgence
echo "2. Dernière soumission d'urgence:\n";
$lastEmergency = App\Models\Submission::where('is_emergency', true)
    ->orderBy('id', 'desc')
    ->first();

if ($lastEmergency) {
    echo "   - ID: " . $lastEmergency->id . "\n";
    echo "   - Nom: " . $lastEmergency->name . "\n";
    echo "   - Email: " . $lastEmergency->email . "\n";
    echo "   - Date: " . $lastEmergency->created_at . "\n";
    echo "   - Photos: " . (count($lastEmergency->photos ?? []))  . " photo(s)\n";
} else {
    echo "   - Aucune soumission d'urgence trouvée\n";
}
echo "\n";

// 3. Test d'envoi d'email
echo "3. Test d'envoi d'email:\n";
$adminEmail = App\Models\Setting::get('admin_notification_email') ?: App\Models\Setting::get('company_email');

if ($adminEmail) {
    echo "   - Destinataire: " . $adminEmail . "\n";
    echo "   - Tentative d'envoi...\n";
    
    try {
        Illuminate\Support\Facades\Mail::raw('Test email d\'urgence - ' . date('Y-m-d H:i:s'), function ($mail) use ($adminEmail) {
            $mail->to($adminEmail)
                 ->subject('🚨 TEST EMAIL URGENCE');
        });
        echo "   ✅ Email envoyé avec succès!\n";
    } catch (\Exception $e) {
        echo "   ❌ Erreur: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ❌ Aucun email admin configuré\n";
}
echo "\n";

// 4. Vérifier les logs récents
echo "4. Logs récents (10 dernières lignes avec 'Emergency'):\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logs = shell_exec("tail -200 " . escapeshellarg($logFile) . " | grep -i 'emergency' | tail -10");
    echo $logs ?: "   - Aucun log d'urgence trouvé\n";
} else {
    echo "   - Fichier de log introuvable\n";
}

echo "\n=== FIN DU DIAGNOSTIC ===\n";

