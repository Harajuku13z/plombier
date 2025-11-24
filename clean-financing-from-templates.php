#!/usr/bin/env php
<?php

/**
 * Script pour nettoyer le contenu de financement des templates existants
 * 
 * Ce script supprime toutes les sections "Financement et aides" des templates
 * déjà créés dans la base de données.
 * 
 * Usage: php clean-financing-from-templates.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n🧹 NETTOYAGE DU CONTENU FINANCEMENT DANS LES TEMPLATES\n";
echo str_repeat("=", 70) . "\n\n";

try {
    // 1. Récupérer tous les templates
    $templates = DB::table('ad_templates')->get();
    
    echo "📊 Nombre de templates trouvés : " . count($templates) . "\n\n";
    
    if (count($templates) === 0) {
        echo "✅ Aucun template à nettoyer.\n\n";
        exit(0);
    }
    
    $cleaned = 0;
    $errors = 0;
    
    foreach ($templates as $template) {
        echo "🔍 Traitement du template #{$template->id} : {$template->name}\n";
        
        $originalContent = $template->content_html;
        $cleanedContent = $originalContent;
        
        // Patterns pour détecter et supprimer les sections de financement
        $patterns = [
            // Pattern 1: Section complète avec div bg-yellow-50 et titre "Financement"
            '/<div[^>]*class="[^"]*bg-yellow-50[^"]*"[^>]*>.*?<h[1-6][^>]*>.*?[Ff]inancement.*?<\/h[1-6]>.*?<\/div>/s',
            
            // Pattern 2: Section avec border-l-4 border-yellow et "Financement"
            '/<div[^>]*class="[^"]*border-l-4[^"]*border-yellow[^"]*"[^>]*>.*?[Ff]inancement.*?<\/div>/s',
            
            // Pattern 3: Titre h4 "Financement" et contenu suivant jusqu'à la prochaine balise fermante
            '/<h[1-6][^>]*>.*?[Ff]inancement et [Aa]ides.*?<\/h[1-6]>.*?(?=<(?:h[1-6]|div class="bg-|div class="mt-))/s',
            
            // Pattern 4: Paragraphes contenant MaPrimeRénov, CEE, éco-PTZ
            '/<p[^>]*>.*?(?:MaPrimeR[ée]nov|Certificat.*?[ÉE]conomie|[ÉE]co-PTZ|TVA r[ée]duite|Prime CEE|[Éé]co-pr[êe]t).*?<\/p>/si',
        ];
        
        // Appliquer chaque pattern
        foreach ($patterns as $index => $pattern) {
            $before = $cleanedContent;
            $cleanedContent = preg_replace($pattern, '', $cleanedContent);
            
            if ($before !== $cleanedContent) {
                echo "   ✂️  Pattern " . ($index + 1) . " : contenu supprimé\n";
            }
        }
        
        // Nettoyer les espaces multiples et lignes vides
        $cleanedContent = preg_replace('/\n\s*\n\s*\n/', "\n\n", $cleanedContent);
        $cleanedContent = trim($cleanedContent);
        
        // Mettre à jour si du contenu a été modifié
        if ($originalContent !== $cleanedContent) {
            try {
                DB::table('ad_templates')
                    ->where('id', $template->id)
                    ->update([
                        'content_html' => $cleanedContent,
                        'updated_at' => now()
                    ]);
                
                $removed = strlen($originalContent) - strlen($cleanedContent);
                echo "   ✅ Template nettoyé ! ($removed caractères supprimés)\n";
                $cleaned++;
            } catch (\Exception $e) {
                echo "   ❌ Erreur lors de la mise à jour : " . $e->getMessage() . "\n";
                $errors++;
            }
        } else {
            echo "   ℹ️  Aucun contenu de financement trouvé\n";
        }
        
        echo "\n";
    }
    
    // Résumé
    echo str_repeat("=", 70) . "\n";
    echo "📊 RÉSUMÉ DU NETTOYAGE\n";
    echo str_repeat("=", 70) . "\n";
    echo "✅ Templates nettoyés : $cleaned\n";
    echo "ℹ️  Templates inchangés : " . (count($templates) - $cleaned - $errors) . "\n";
    if ($errors > 0) {
        echo "❌ Erreurs : $errors\n";
    }
    echo "\n";
    
    if ($cleaned > 0) {
        echo "🎉 SUCCÈS ! Les templates ont été nettoyés.\n";
        echo "💡 Les nouvelles annonces créées à partir de ces templates n'auront plus de contenu de financement.\n";
    } else {
        echo "✨ Aucun nettoyage nécessaire.\n";
    }
    echo "\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERREUR FATALE :\n";
    echo $e->getMessage() . "\n";
    echo "\n" . $e->getTraceAsString() . "\n\n";
    exit(1);
}

echo "✅ Script terminé avec succès.\n\n";
exit(0);

