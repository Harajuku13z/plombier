<?php
/**
 * SCRIPT D'URGENCE - Vidage force de TOUS les caches
 * À utiliser une seule fois puis supprimer
 */

header('Content-Type: text/plain; charset=utf-8');

echo "🧹 NETTOYAGE FORCE DE TOUS LES CACHES\n";
echo str_repeat("=", 50) . "\n\n";

$baseDir = dirname(__DIR__);
$errors = [];
$success = [];

// 1. VIDER CACHE OPCACHE PHP
echo "1️⃣  Vidage OPcache PHP...\n";
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        $success[] = "✅ OPcache vidé";
        echo "   ✅ OPcache vidé\n";
    } else {
        $errors[] = "❌ Échec vidage OPcache";
        echo "   ❌ Échec vidage OPcache\n";
    }
} else {
    echo "   ⚠️  OPcache non disponible (normal sur certains hébergements)\n";
}

// 2. VIDER CACHE VUES BLADE
echo "\n2️⃣  Vidage cache vues Blade...\n";
$viewsPath = $baseDir . '/storage/framework/views';
$viewsDeleted = 0;
if (is_dir($viewsPath)) {
    $files = glob($viewsPath . '/*');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore') {
            if (unlink($file)) {
                $viewsDeleted++;
            }
        }
    }
    $success[] = "✅ $viewsDeleted fichiers vues supprimés";
    echo "   ✅ $viewsDeleted fichiers vues supprimés\n";
} else {
    $errors[] = "❌ Dossier views introuvable";
    echo "   ❌ Dossier views introuvable\n";
}

// 3. VIDER CACHE APPLICATION
echo "\n3️⃣  Vidage cache application...\n";
$cachePath = $baseDir . '/storage/framework/cache/data';
$cacheDeleted = 0;
if (is_dir($cachePath)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cachePath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getFilename() !== '.gitignore') {
            if (unlink($file->getRealPath())) {
                $cacheDeleted++;
            }
        }
    }
    $success[] = "✅ $cacheDeleted fichiers cache supprimés";
    echo "   ✅ $cacheDeleted fichiers cache supprimés\n";
}

// 4. VIDER CACHE BOOTSTRAP
echo "\n4️⃣  Vidage cache bootstrap...\n";
$bootstrapPath = $baseDir . '/bootstrap/cache';
$bootstrapDeleted = 0;
if (is_dir($bootstrapPath)) {
    $files = glob($bootstrapPath . '/*.php');
    foreach ($files as $file) {
        $basename = basename($file);
        if ($basename !== '.gitignore' && is_file($file)) {
            if (unlink($file)) {
                $bootstrapDeleted++;
            }
        }
    }
    $success[] = "✅ $bootstrapDeleted fichiers bootstrap supprimés";
    echo "   ✅ $bootstrapDeleted fichiers bootstrap supprimés\n";
}

// 5. CLEAR REALPATH CACHE
echo "\n5️⃣  Vidage realpath cache...\n";
clearstatcache(true);
$success[] = "✅ Realpath cache vidé";
echo "   ✅ Realpath cache vidé\n";

// 6. TOUCHER LE FICHIER home.blade.php
echo "\n6️⃣  Mise à jour timestamp home.blade.php...\n";
$homeFile = $baseDir . '/resources/views/home.blade.php';
if (file_exists($homeFile)) {
    touch($homeFile);
    $success[] = "✅ Timestamp home.blade.php mis à jour";
    echo "   ✅ Timestamp home.blade.php mis à jour\n";
}

// RÉSUMÉ
echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 RÉSUMÉ\n";
echo str_repeat("=", 50) . "\n\n";

if (!empty($success)) {
    echo "✅ SUCCÈS (" . count($success) . "):\n";
    foreach ($success as $s) {
        echo "   $s\n";
    }
}

if (!empty($errors)) {
    echo "\n❌ ERREURS (" . count($errors) . "):\n";
    foreach ($errors as $e) {
        echo "   $e\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ NETTOYAGE TERMINÉ !\n\n";
echo "📋 PROCHAINES ÉTAPES:\n";
echo "   1. Rechargez votre site : https://plombier-versailles78.fr\n";
echo "   2. Si l'erreur persiste, attendez 30 secondes et réessayez\n";
echo "   3. En dernier recours, redémarrez PHP-FPM\n\n";

echo "🔒 SÉCURITÉ:\n";
echo "   Supprimez ce fichier immédiatement après utilisation !\n";
echo "   rm " . __FILE__ . "\n\n";

// AUTO-SUPPRESSION (décommenter si souhaité)
// @unlink(__FILE__);
// echo "🗑️  Ce fichier s'est auto-supprimé.\n";

echo "\n" . date('Y-m-d H:i:s') . " - Nettoyage effectué\n";

