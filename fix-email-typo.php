<?php
/**
 * Corriger la faute de frappe dans company_email
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Correction de l'email avec faute de frappe...\n";

// Récupérer l'email actuel
$currentEmail = App\Models\Setting::get('company_email');
echo "Email actuel : {$currentEmail}\n";

if ($currentEmail === 'conact@plombier-versailles78.fr') {
    // Corriger
    App\Models\Setting::set('company_email', 'contact@plombier-versailles78.fr', 'string', 'email');
    echo "✅ Corrigé en : contact@plombier-versailles78.fr\n";
} else {
    echo "✅ Email déjà correct : {$currentEmail}\n";
}

App\Models\Setting::clearCache();
echo "Cache vidé.\n";

