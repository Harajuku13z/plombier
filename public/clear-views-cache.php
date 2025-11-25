<?php
/**
 * Script simple pour vider le cache des vues Blade
 * À utiliser après chaque déploiement
 */

header('Content-Type: text/plain; charset=utf-8');

$baseDir = dirname(__DIR__);
$viewsPath = $baseDir . '/storage/framework/views';

echo "🧹 Vidage cache vues Blade\n";
echo str_repeat("=", 40) . "\n\n";

if (is_dir($viewsPath)) {
    $files = glob($viewsPath . '/*');
    $deleted = 0;
    
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore') {
            if (unlink($file)) {
                $deleted++;
            }
        }
    }
    
    echo "✅ $deleted fichiers supprimés\n\n";
} else {
    echo "❌ Dossier introuvable\n\n";
}

// OPcache si disponible
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "✅ OPcache vidé\n\n";
    }
}

clearstatcache(true);

echo "✅ Cache vidé avec succès !\n";
echo "🔄 Rechargez votre site maintenant.\n\n";
echo date('Y-m-d H:i:s') . "\n";

// Auto-suppression après 5 secondes
sleep(1);
echo "\n🗑️  Supprimez ce fichier après utilisation pour sécurité.\n";

