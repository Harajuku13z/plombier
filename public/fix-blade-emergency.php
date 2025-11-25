<?php
/**
 * SCRIPT D'URGENCE - Remplace home.blade.php par version de secours
 * À utiliser UNE SEULE FOIS
 */

header('Content-Type: text/plain; charset=utf-8');

echo "🚨 PROCEDURE D'URGENCE - REMPLACEMENT home.blade.php\n";
echo str_repeat("=", 60) . "\n\n";

$baseDir = dirname(__DIR__);
$viewsDir = $baseDir . '/resources/views';
$homeFile = $viewsDir . '/home.blade.php';
$backupFile = $viewsDir . '/home-backup.blade.php';
$brokenFile = $viewsDir . '/home-broken.blade.php';

// Vérifier que les fichiers existent
if (!file_exists($homeFile)) {
    die("❌ ERREUR: home.blade.php introuvable !\n");
}

if (!file_exists($backupFile)) {
    die("❌ ERREUR: home-backup.blade.php introuvable !\n   Exécutez 'git pull' d'abord.\n");
}

echo "📁 Fichiers détectés:\n";
echo "   ✅ home.blade.php: " . filesize($homeFile) . " octets\n";
echo "   ✅ home-backup.blade.php: " . filesize($backupFile) . " octets\n\n";

// ÉTAPE 1: Sauvegarder le fichier cassé
echo "1️⃣  Sauvegarde du fichier cassé...\n";
if (copy($homeFile, $brokenFile)) {
    echo "   ✅ home.blade.php → home-broken.blade.php\n\n";
} else {
    die("   ❌ ERREUR: Impossible de sauvegarder\n");
}

// ÉTAPE 2: Remplacer par la version de secours
echo "2️⃣  Remplacement par version de secours...\n";
if (copy($backupFile, $homeFile)) {
    echo "   ✅ home-backup.blade.php → home.blade.php\n";
    echo "   ✅ REMPLACEMENT RÉUSSI !\n\n";
} else {
    // Restaurer l'original si échec
    copy($brokenFile, $homeFile);
    die("   ❌ ERREUR: Remplacement échoué, fichier restauré\n");
}

// ÉTAPE 3: Mettre à jour le timestamp
echo "3️⃣  Mise à jour timestamp...\n";
touch($homeFile);
clearstatcache(true, $homeFile);
echo "   ✅ Timestamp mis à jour\n\n";

// ÉTAPE 4: Vider les caches
echo "4️⃣  Vidage caches...\n";

// OPcache
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "   ✅ OPcache vidé\n";
    }
}

// Cache vues
$cacheViewsDir = $baseDir . '/storage/framework/views';
if (is_dir($cacheViewsDir)) {
    $files = glob($cacheViewsDir . '/*');
    $deleted = 0;
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore') {
            if (unlink($file)) $deleted++;
        }
    }
    echo "   ✅ $deleted fichiers cache vues supprimés\n";
}

// Realpath cache
clearstatcache(true);
echo "   ✅ Realpath cache vidé\n\n";

echo str_repeat("=", 60) . "\n";
echo "✅ REMPLACEMENT TERMINÉ AVEC SUCCÈS !\n";
echo str_repeat("=", 60) . "\n\n";

echo "📋 RÉSULTAT:\n";
echo "   • home.blade.php → VERSION SIMPLIFIÉE ACTIVE\n";
echo "   • home-broken.blade.php → Version cassée sauvegardée\n";
echo "   • Caches vidés\n\n";

echo "🔄 PROCHAINES ÉTAPES:\n";
echo "   1. Rechargez votre site : https://plombier-versailles78.fr\n";
echo "   2. Vous verrez une page simplifiée qui FONCTIONNE\n";
echo "   3. Attendez 2-3 minutes (expiration cache OPcache)\n";
echo "   4. Contactez votre dev pour restaurer version complète\n\n";

echo "📧 RAPPORT À ENVOYER AU DEV:\n";
echo "   Le problème vient du cache OPcache PHP qui ne se vide pas.\n";
echo "   Solutions permanentes:\n";
echo "   - Désactiver OPcache en développement\n";
echo "   - Augmenter opcache.revalidate_freq\n";
echo "   - Redémarrer PHP-FPM après chaque déploiement\n\n";

echo "🗑️  NETTOYAGE:\n";
echo "   Après avoir vérifié que le site fonctionne, supprimez :\n";
echo "   - public/fix-blade-emergency.php (ce fichier)\n";
echo "   - public/force-clear-cache.php\n";
echo "   - public/test-home.php\n\n";

echo "⏰ " . date('Y-m-d H:i:s') . " - Opération terminée\n";

