#!/usr/bin/env php
<?php

/**
 * Script pour nettoyer le contenu de financement des annonces existantes
 * 
 * Ce script supprime toutes les sections "Financement et aides" des annonces
 * déjà créées et publiées dans la base de données.
 * 
 * Usage: php clean-financing-from-ads.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n🧹 NETTOYAGE DU CONTENU FINANCEMENT DANS LES ANNONCES\n";
echo str_repeat("=", 70) . "\n\n";

try {
    // 1. Récupérer toutes les annonces
    $ads = DB::table('ads')->get();
    
    echo "📊 Nombre d'annonces trouvées : " . count($ads) . "\n\n";
    
    if (count($ads) === 0) {
        echo "✅ Aucune annonce à nettoyer.\n\n";
        exit(0);
    }
    
    $cleaned = 0;
    $errors = 0;
    $noContent = 0;
    
    foreach ($ads as $ad) {
        echo "🔍 Traitement de l'annonce #{$ad->id} : {$ad->title}\n";
        
        // Vérifier si l'annonce a du contenu HTML
        if (empty($ad->content_html)) {
            echo "   ⚠️  Pas de contenu HTML\n\n";
            $noContent++;
            continue;
        }
        
        $originalContent = $ad->content_html;
        $cleanedContent = $originalContent;
        
        // Patterns pour détecter et supprimer les sections de financement
        $patterns = [
            // Pattern 1: Section complète avec div bg-yellow-50 et titre "Financement"
            '/<div[^>]*class="[^"]*bg-yellow-50[^"]*"[^>]*>.*?<h[1-6][^>]*>.*?[Ff]inancement.*?<\/h[1-6]>.*?<\/div>/s',
            
            // Pattern 2: Section avec border-l-4 border-yellow et "Financement"
            '/<div[^>]*class="[^"]*border-l-4[^"]*border-yellow[^"]*"[^>]*>.*?[Ff]inancement.*?<\/div>/s',
            
            // Pattern 3: Titre h4 "Financement" et contenu suivant jusqu'à la prochaine section
            '/<h[1-6][^>]*>.*?[Ff]inancement et [Aa]ides.*?<\/h[1-6]>.*?(?=<(?:h[1-6]|div class="bg-|div class="mt-|<!-- SECTION))/s',
            
            // Pattern 4: Paragraphes contenant MaPrimeRénov, CEE, éco-PTZ (avec contexte)
            '/<p[^>]*>.*?(?:MaPrimeR[ée]nov|Certificat.*?[ÉE]conomie|[ÉE]co-PTZ|TVA r[ée]duite|Prime CEE|[Éé]co-pr[êe]t|éco-prêt).*?<\/p>/si',
            
            // Pattern 5: Divs avec classe spécifique contenant aides/financement
            '/<div[^>]*class="[^"]*(?:bg-yellow|border-yellow|financing)[^"]*"[^>]*>.*?(?:aide|financement|MaPrime|CEE|PTZ).*?<\/div>/si',
            
            // Pattern 6: Listes (ul/ol) contenant des infos de financement
            '/<(?:ul|ol)[^>]*>.*?(?:MaPrimeR[ée]nov|CEE|[Éé]co-PTZ|TVA r[ée]duite).*?<\/(?:ul|ol)>/si',
            
            // Pattern 7: Strong tags avec financement
            '/<strong>.*?(?:MaPrimeR[ée]nov|Certificat.*?[ÉE]conomie|[Éé]co-PTZ|TVA r[ée]duite|Prime CEE).*?<\/strong>/si',
            
            // Pattern 8: Sections complètes commentées "FINANCEMENT"
            '/<!-- SECTION.*?FINANCEMENT.*?-->.*?(?=<!-- SECTION|$)/si',
        ];
        
        $changesDetected = false;
        
        // Appliquer chaque pattern
        foreach ($patterns as $index => $pattern) {
            $before = $cleanedContent;
            $cleanedContent = preg_replace($pattern, '', $cleanedContent);
            
            if ($before !== $cleanedContent) {
                echo "   ✂️  Pattern " . ($index + 1) . " : contenu supprimé\n";
                $changesDetected = true;
            }
        }
        
        // Nettoyage supplémentaire : supprimer les sections vides
        $cleanedContent = preg_replace('/<div[^>]*class="[^"]*bg-yellow-50[^"]*"[^>]*>\s*<\/div>/s', '', $cleanedContent);
        $cleanedContent = preg_replace('/<div[^>]*class="[^"]*border-l-4[^"]*"[^>]*>\s*<\/div>/s', '', $cleanedContent);
        
        // Nettoyer les espaces multiples et lignes vides
        $cleanedContent = preg_replace('/\n\s*\n\s*\n/', "\n\n", $cleanedContent);
        $cleanedContent = preg_replace('/(<\/div>)\s+(<div)/', '$1' . "\n" . '$2', $cleanedContent);
        $cleanedContent = trim($cleanedContent);
        
        // Mettre à jour si du contenu a été modifié
        if ($originalContent !== $cleanedContent) {
            try {
                DB::table('ads')
                    ->where('id', $ad->id)
                    ->update([
                        'content_html' => $cleanedContent,
                        'updated_at' => now()
                    ]);
                
                $removed = strlen($originalContent) - strlen($cleanedContent);
                echo "   ✅ Annonce nettoyée ! ($removed caractères supprimés)\n";
                $cleaned++;
            } catch (\Exception $e) {
                echo "   ❌ Erreur lors de la mise à jour : " . $e->getMessage() . "\n";
                $errors++;
            }
        } else {
            if ($changesDetected) {
                echo "   ℹ️  Tentative de nettoyage mais pas de changement final\n";
            } else {
                echo "   ℹ️  Aucun contenu de financement trouvé\n";
            }
        }
        
        echo "\n";
    }
    
    // Résumé
    echo str_repeat("=", 70) . "\n";
    echo "📊 RÉSUMÉ DU NETTOYAGE\n";
    echo str_repeat("=", 70) . "\n";
    echo "✅ Annonces nettoyées : $cleaned\n";
    echo "ℹ️  Annonces inchangées : " . (count($ads) - $cleaned - $errors - $noContent) . "\n";
    echo "⚠️  Annonces sans contenu : $noContent\n";
    if ($errors > 0) {
        echo "❌ Erreurs : $errors\n";
    }
    echo "\n";
    
    if ($cleaned > 0) {
        echo "🎉 SUCCÈS ! Les annonces ont été nettoyées.\n";
        echo "💡 Les pages d'annonces n'affichent plus de contenu de financement.\n";
        echo "💡 Le JavaScript et CSS masquent aussi automatiquement tout résidu.\n";
    } else {
        echo "✨ Aucun nettoyage nécessaire.\n";
    }
    echo "\n";
    
    // Statistiques supplémentaires
    if ($cleaned > 0) {
        echo "📈 STATISTIQUES DÉTAILLÉES\n";
        echo str_repeat("-", 70) . "\n";
        
        // Compter les annonces par service
        $adsByService = DB::table('ads')
            ->select('service_id', DB::raw('count(*) as total'))
            ->groupBy('service_id')
            ->get();
        
        echo "Répartition par service :\n";
        foreach ($adsByService as $stat) {
            $service = DB::table('services')->where('id', $stat->service_id)->first();
            $serviceName = $service ? $service->name : "Service #{$stat->service_id}";
            echo "  - {$serviceName} : {$stat->total} annonce(s)\n";
        }
        echo "\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ ERREUR FATALE :\n";
    echo $e->getMessage() . "\n";
    echo "\n" . $e->getTraceAsString() . "\n\n";
    exit(1);
}

echo "✅ Script terminé avec succès.\n\n";
echo "💡 CONSEIL : Exécutez aussi 'php clean-financing-from-templates.php' pour nettoyer les templates.\n";
echo "💡 Ainsi, les futures annonces créées seront également sans contenu de financement.\n\n";

exit(0);

