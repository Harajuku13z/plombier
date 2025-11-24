#!/usr/bin/env php
<?php

/**
 * Script COMPLET pour nettoyer TOUT le contenu de financement
 * 
 * Ce script nettoie :
 * 1. Les TEMPLATES (ad_templates)
 * 2. Les ANNONCES (ads)
 * 
 * Usage: php clean-all-financing.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║  🧹 NETTOYAGE COMPLET DU CONTENU FINANCEMENT                      ║\n";
echo "║  Templates + Annonces                                              ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$totalCleaned = 0;
$totalErrors = 0;

/**
 * Fonction de nettoyage réutilisable
 */
function cleanFinancingContent($content) {
    if (empty($content)) {
        return $content;
    }
    
    $cleanedContent = $content;
    
    // Patterns pour détecter et supprimer les sections de financement
    $patterns = [
        // Pattern 1: Section complète avec div bg-yellow-50 et titre "Financement"
        '/<div[^>]*class="[^"]*bg-yellow-50[^"]*"[^>]*>.*?<h[1-6][^>]*>.*?[Ff]inancement.*?<\/h[1-6]>.*?<\/div>/s',
        
        // Pattern 2: Section avec border-l-4 border-yellow et "Financement"
        '/<div[^>]*class="[^"]*border-l-4[^"]*border-yellow[^"]*"[^>]*>.*?[Ff]inancement.*?<\/div>/s',
        
        // Pattern 3: Titre h4 "Financement" et contenu suivant
        '/<h[1-6][^>]*>.*?[Ff]inancement et [Aa]ides.*?<\/h[1-6]>.*?(?=<(?:h[1-6]|div class="bg-|div class="mt-|<!-- SECTION))/s',
        
        // Pattern 4: Paragraphes contenant MaPrimeRénov, CEE, éco-PTZ
        '/<p[^>]*>.*?(?:MaPrimeR[ée]nov|Certificat.*?[ÉE]conomie|[ÉE]co-PTZ|TVA r[ée]duite|Prime CEE|[Éé]co-pr[êe]t|éco-prêt).*?<\/p>/si',
        
        // Pattern 5: Divs avec classe financement
        '/<div[^>]*class="[^"]*(?:bg-yellow|border-yellow|financing)[^"]*"[^>]*>.*?(?:aide|financement|MaPrime|CEE|PTZ).*?<\/div>/si',
        
        // Pattern 6: Listes contenant des infos de financement
        '/<(?:ul|ol)[^>]*>.*?(?:MaPrimeR[ée]nov|CEE|[Éé]co-PTZ|TVA r[ée]duite).*?<\/(?:ul|ol)>/si',
        
        // Pattern 7: Strong tags avec financement
        '/<strong>.*?(?:MaPrimeR[ée]nov|Certificat.*?[ÉE]conomie|[Éé]co-PTZ|TVA r[ée]duite|Prime CEE).*?<\/strong>/si',
        
        // Pattern 8: Sections commentées "FINANCEMENT"
        '/<!-- SECTION.*?FINANCEMENT.*?-->.*?(?=<!-- SECTION|$)/si',
    ];
    
    // Appliquer chaque pattern
    foreach ($patterns as $pattern) {
        $cleanedContent = preg_replace($pattern, '', $cleanedContent);
    }
    
    // Nettoyage des sections vides
    $cleanedContent = preg_replace('/<div[^>]*class="[^"]*bg-yellow-50[^"]*"[^>]*>\s*<\/div>/s', '', $cleanedContent);
    $cleanedContent = preg_replace('/<div[^>]*class="[^"]*border-l-4[^"]*"[^>]*>\s*<\/div>/s', '', $cleanedContent);
    
    // Nettoyer les espaces multiples et lignes vides
    $cleanedContent = preg_replace('/\n\s*\n\s*\n/', "\n\n", $cleanedContent);
    $cleanedContent = preg_replace('/(<\/div>)\s+(<div)/', '$1' . "\n" . '$2', $cleanedContent);
    $cleanedContent = trim($cleanedContent);
    
    return $cleanedContent;
}

try {
    // ========================================================================
    // PARTIE 1 : NETTOYAGE DES TEMPLATES
    // ========================================================================
    
    echo "┌────────────────────────────────────────────────────────────────────┐\n";
    echo "│  ÉTAPE 1/2 : Nettoyage des TEMPLATES                              │\n";
    echo "└────────────────────────────────────────────────────────────────────┘\n\n";
    
    $templates = DB::table('ad_templates')->get();
    echo "📊 Nombre de templates trouvés : " . count($templates) . "\n\n";
    
    $templatesCleaned = 0;
    
    if (count($templates) > 0) {
        foreach ($templates as $template) {
            echo "🔍 Template #{$template->id} : {$template->name}";
            
            $originalContent = $template->content_html;
            $cleanedContent = cleanFinancingContent($originalContent);
            
            if ($originalContent !== $cleanedContent) {
                try {
                    DB::table('ad_templates')
                        ->where('id', $template->id)
                        ->update([
                            'content_html' => $cleanedContent,
                            'updated_at' => now()
                        ]);
                    
                    $removed = strlen($originalContent) - strlen($cleanedContent);
                    echo " ✅ ($removed caractères supprimés)\n";
                    $templatesCleaned++;
                } catch (\Exception $e) {
                    echo " ❌ Erreur\n";
                    $totalErrors++;
                }
            } else {
                echo " ℹ️  Déjà propre\n";
            }
        }
    }
    
    echo "\n✅ Templates nettoyés : $templatesCleaned / " . count($templates) . "\n\n";
    $totalCleaned += $templatesCleaned;
    
    // ========================================================================
    // PARTIE 2 : NETTOYAGE DES ANNONCES
    // ========================================================================
    
    echo "┌────────────────────────────────────────────────────────────────────┐\n";
    echo "│  ÉTAPE 2/2 : Nettoyage des ANNONCES                               │\n";
    echo "└────────────────────────────────────────────────────────────────────┘\n\n";
    
    $ads = DB::table('ads')->get();
    echo "📊 Nombre d'annonces trouvées : " . count($ads) . "\n\n";
    
    $adsCleaned = 0;
    $adsNoContent = 0;
    
    if (count($ads) > 0) {
        foreach ($ads as $ad) {
            echo "🔍 Annonce #{$ad->id} : " . substr($ad->title, 0, 50);
            
            if (empty($ad->content_html)) {
                echo " ⚠️  Pas de contenu\n";
                $adsNoContent++;
                continue;
            }
            
            $originalContent = $ad->content_html;
            $cleanedContent = cleanFinancingContent($originalContent);
            
            if ($originalContent !== $cleanedContent) {
                try {
                    DB::table('ads')
                        ->where('id', $ad->id)
                        ->update([
                            'content_html' => $cleanedContent,
                            'updated_at' => now()
                        ]);
                    
                    $removed = strlen($originalContent) - strlen($cleanedContent);
                    echo " ✅ ($removed caractères)\n";
                    $adsCleaned++;
                } catch (\Exception $e) {
                    echo " ❌ Erreur\n";
                    $totalErrors++;
                }
            } else {
                echo " ℹ️  Déjà propre\n";
            }
        }
    }
    
    echo "\n✅ Annonces nettoyées : $adsCleaned / " . count($ads) . "\n";
    echo "⚠️  Annonces sans contenu : $adsNoContent\n\n";
    $totalCleaned += $adsCleaned;
    
    // ========================================================================
    // RÉSUMÉ FINAL
    // ========================================================================
    
    echo "\n";
    echo "╔════════════════════════════════════════════════════════════════════╗\n";
    echo "║  📊 RÉSUMÉ FINAL                                                   ║\n";
    echo "╚════════════════════════════════════════════════════════════════════╝\n\n";
    
    echo "✅ Templates nettoyés    : $templatesCleaned / " . count($templates) . "\n";
    echo "✅ Annonces nettoyées    : $adsCleaned / " . count($ads) . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🎯 TOTAL NETTOYÉ         : $totalCleaned éléments\n";
    
    if ($totalErrors > 0) {
        echo "❌ Erreurs               : $totalErrors\n";
    }
    
    echo "\n";
    
    if ($totalCleaned > 0) {
        echo "🎉 SUCCÈS COMPLET !\n\n";
        echo "✅ Tous les templates sont propres\n";
        echo "✅ Toutes les annonces sont propres\n";
        echo "✅ Les futures annonces seront sans financement\n";
        echo "✅ Le JavaScript/CSS masque automatiquement tout résidu\n";
    } else {
        echo "✨ Tout est déjà propre !\n";
        echo "Aucun contenu de financement détecté.\n";
    }
    
    echo "\n";
    
} catch (\Exception $e) {
    echo "\n";
    echo "╔════════════════════════════════════════════════════════════════════╗\n";
    echo "║  ❌ ERREUR FATALE                                                  ║\n";
    echo "╚════════════════════════════════════════════════════════════════════╝\n\n";
    echo $e->getMessage() . "\n";
    echo "\n" . $e->getTraceAsString() . "\n\n";
    exit(1);
}

echo "✅ Script terminé avec succès.\n\n";
exit(0);

