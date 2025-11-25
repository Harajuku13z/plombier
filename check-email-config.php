#!/usr/bin/env php
<?php

/**
 * Script de vérification de la configuration email
 * 
 * Usage: php check-email-config.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Setting;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║  📧 VÉRIFICATION CONFIGURATION EMAIL                              ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$issues = [];
$warnings = [];
$success = [];

// 1. Vérifier email_enabled
echo "🔍 Vérification de l'activation des emails...\n";
$emailEnabled = Setting::get('email_enabled', false);
if ($emailEnabled == '1' || $emailEnabled === true) {
    echo "   ✅ Emails activés\n";
    $success[] = "Emails activés";
} else {
    echo "   ❌ Emails DÉSACTIVÉS\n";
    $issues[] = "email_enabled = false dans settings. Exécutez : UPDATE settings SET value='1' WHERE name='email_enabled';";
}
echo "\n";

// 2. Vérifier email admin
echo "🔍 Vérification de l'email administrateur...\n";
$adminEmail = Setting::get('admin_notification_email');
$companyEmail = Setting::get('company_email');

if ($adminEmail) {
    echo "   ✅ admin_notification_email: {$adminEmail}\n";
    $success[] = "Email admin configuré: {$adminEmail}";
} elseif ($companyEmail) {
    echo "   ⚠️  admin_notification_email non défini, utilisation de company_email: {$companyEmail}\n";
    $warnings[] = "Définir admin_notification_email pour plus de clarté";
    $adminEmail = $companyEmail;
} else {
    echo "   ❌ Aucun email admin configuré\n";
    $issues[] = "Configurer admin_notification_email ou company_email";
}
echo "\n";

// 3. Vérifier configuration SMTP
echo "🔍 Vérification de la configuration SMTP...\n";
$smtpConfig = [
    'mail_host' => Setting::get('mail_host'),
    'mail_port' => Setting::get('mail_port'),
    'mail_username' => Setting::get('mail_username'),
    'mail_password' => Setting::get('mail_password') ? '***' : null,
    'mail_encryption' => Setting::get('mail_encryption'),
    'mail_from_address' => Setting::get('mail_from_address'),
    'mail_from_name' => Setting::get('mail_from_name'),
];

$smtpOk = true;
foreach ($smtpConfig as $key => $value) {
    if ($value) {
        echo "   ✅ {$key}: {$value}\n";
    } else {
        echo "   ❌ {$key}: NON CONFIGURÉ\n";
        $issues[] = "Configurer {$key} dans settings";
        $smtpOk = false;
    }
}

if ($smtpOk) {
    $success[] = "Configuration SMTP complète";
}
echo "\n";

// 4. Vérifier le dossier submissions
echo "🔍 Vérification du dossier de stockage des photos...\n";
$submissionsPath = storage_path('app/public/submissions');
if (file_exists($submissionsPath)) {
    echo "   ✅ Dossier existe: {$submissionsPath}\n";
    
    if (is_writable($submissionsPath)) {
        echo "   ✅ Dossier accessible en écriture\n";
        $success[] = "Dossier submissions OK";
    } else {
        echo "   ❌ Dossier NON accessible en écriture\n";
        $issues[] = "Exécuter : chmod -R 775 {$submissionsPath}";
    }
} else {
    echo "   ⚠️  Dossier n'existe pas (sera créé automatiquement)\n";
    $warnings[] = "Dossier submissions sera créé au premier upload";
}
echo "\n";

// 5. Vérifier les soumissions récentes avec photos
echo "🔍 Vérification des soumissions récentes...\n";
$recentSubmissions = DB::table('submissions')
    ->whereNotNull('tracking_data')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

if ($recentSubmissions->count() > 0) {
    echo "   📊 {$recentSubmissions->count()} soumission(s) récente(s) trouvée(s)\n";
    
    $submissionsWithPhotos = 0;
    foreach ($recentSubmissions as $sub) {
        $trackingData = json_decode($sub->tracking_data, true);
        if (!empty($trackingData['photos'])) {
            $submissionsWithPhotos++;
            echo "   ✅ Soumission #{$sub->id} : " . count($trackingData['photos']) . " photo(s)\n";
        }
    }
    
    if ($submissionsWithPhotos > 0) {
        $success[] = "{$submissionsWithPhotos} soumission(s) avec photos";
    } else {
        $warnings[] = "Aucune photo dans les soumissions récentes";
    }
} else {
    echo "   ℹ️  Aucune soumission récente\n";
}
echo "\n";

// 6. Vérifier les fichiers temporaires
echo "🔍 Vérification des fichiers temporaires...\n";
$tempPath = storage_path('app/public/simulator-temp');
if (file_exists($tempPath)) {
    $tempFiles = glob($tempPath . '/*');
    $tempCount = count($tempFiles);
    
    if ($tempCount > 0) {
        echo "   ⚠️  {$tempCount} fichier(s) temporaire(s) trouvé(s)\n";
        $warnings[] = "Fichiers temporaires non nettoyés (peuvent être supprimés)";
    } else {
        echo "   ✅ Aucun fichier temporaire (bon signe)\n";
        $success[] = "Pas de fichiers temporaires";
    }
} else {
    echo "   ℹ️  Dossier simulator-temp n'existe pas\n";
}
echo "\n";

// Résumé
echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║  📊 RÉSUMÉ                                                         ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

if (!empty($success)) {
    echo "✅ SUCCÈS (" . count($success) . "):\n";
    foreach ($success as $item) {
        echo "   • {$item}\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️  AVERTISSEMENTS (" . count($warnings) . "):\n";
    foreach ($warnings as $warning) {
        echo "   • {$warning}\n";
    }
    echo "\n";
}

if (!empty($issues)) {
    echo "❌ PROBLÈMES À CORRIGER (" . count($issues) . "):\n";
    foreach ($issues as $issue) {
        echo "   • {$issue}\n";
    }
    echo "\n";
    echo "💡 Consultez GUIDE-CONFIGURATION-EMAIL.md pour les solutions détaillées\n";
    echo "\n";
    exit(1);
} else {
    echo "🎉 Configuration email OK !\n";
    echo "\n";
    echo "📝 Prochaines étapes :\n";
    echo "   1. Vérifier SPF/DKIM/DMARC sur le DNS\n";
    echo "   2. Tester avec https://www.mail-tester.com/\n";
    echo "   3. Envoyer un email de test via le formulaire\n";
    echo "\n";
    exit(0);
}

