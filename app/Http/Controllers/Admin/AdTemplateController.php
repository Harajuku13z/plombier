<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdTemplate;
use App\Models\City;
use App\Models\Ad;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\AiService;

class AdTemplateController extends Controller
{
    /**
     * Afficher la liste des templates
     */
    public function index()
    {
        $templates = AdTemplate::withCount('ads')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Récupérer les services depuis les settings
        $servicesData = Setting::get('services', '[]');
        $services = is_string($servicesData) ? json_decode($servicesData, true) : ($servicesData ?? []);
        
        if (!is_array($services)) {
            $services = [];
        }

        return view('admin.ads.templates.index', compact('templates', 'services'));
    }

    /**
     * Générer tous les liens de tous les templates
     */
    public function generateAllLinks()
    {
        try {
            $baseUrl = setting('site_url', config('app.url'));
            if (!str_starts_with($baseUrl, 'http')) {
                $baseUrl = 'https://' . $baseUrl;
            }
            $baseUrl = rtrim($baseUrl, '/');
            
            // Récupérer tous les templates avec leurs annonces
            $templates = AdTemplate::with(['ads' => function($query) {
                $query->where('status', 'published')
                      ->with('city');
            }])->get();
            
            $allLinks = [];
            
            foreach ($templates as $template) {
                foreach ($template->ads as $ad) {
                    if ($ad->slug) {
                        $url = $baseUrl . '/annonces/' . $ad->slug;
                        $allLinks[] = [
                            'url' => $url,
                            'template_name' => $template->name,
                            'template_id' => $template->id,
                            'ad_id' => $ad->id,
                            'city' => $ad->city ? $ad->city->name : null,
                            'slug' => $ad->slug,
                        ];
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'links' => $allLinks,
                'total' => count($allLinks),
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur génération liens templates: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération des liens: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher un template spécifique
     */
    public function show(AdTemplate $template)
    {
        $template->load('ads.city');
        
        return view('admin.ads.templates.show', compact('template'));
    }

    /**
     * Afficher le formulaire d'édition pour personnaliser le template
     */
    public function edit(AdTemplate $template)
    {
        return view('admin.ads.templates.edit', compact('template'));
    }

    /**
     * Mettre à jour le template personnalisé
     */
    public function update(Request $request, AdTemplate $template)
    {
        $validated = $request->validate([
            'content_html' => 'required|string',
            'short_description' => 'required|string|max:500',
            'long_description' => 'required|string|max:2000',
            'meta_title' => 'required|string|max:160',
            'meta_description' => 'required|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'og_title' => 'nullable|string|max:160',
            'og_description' => 'nullable|string|max:500',
            'twitter_title' => 'nullable|string|max:160',
            'twitter_description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
        ]);

        $template->update($validated);

        return redirect()
            ->route('admin.ads.templates.show', $template->id)
            ->with('success', 'Template personnalisé avec succès ! Vous pouvez maintenant générer des annonces.');
    }

    /**
     * Créer un template manuellement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_name' => 'required|string|max:255',
            'service_slug' => 'required|string|max:255',
            'content_html' => 'required|string',
            'short_description' => 'required|string|max:500',
            'long_description' => 'required|string|max:2000',
            'meta_title' => 'required|string|max:160',
            'meta_description' => 'required|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'og_title' => 'nullable|string|max:160',
            'og_description' => 'nullable|string|max:500',
            'twitter_title' => 'nullable|string|max:160',
            'twitter_description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            // Gérer l'upload de l'image si fournie
            $featuredImagePath = null;
            if ($request->hasFile('featured_image')) {
                $file = $request->file('featured_image');
                $fileName = 'template_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                $uploadPath = public_path('uploads/templates');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                $file->move($uploadPath, $fileName);
                $featuredImagePath = 'uploads/templates/' . $fileName;
            }

            // Créer le template
            $template = AdTemplate::create([
                'name' => $validated['name'],
                'service_name' => $validated['service_name'],
                'service_slug' => $validated['service_slug'],
                'content_html' => $validated['content_html'],
                'short_description' => $validated['short_description'],
                'long_description' => $validated['long_description'],
                'icon' => $validated['icon'] ?? 'fas fa-tools',
                'featured_image' => $featuredImagePath,
                'meta_title' => $validated['meta_title'],
                'meta_description' => $validated['meta_description'],
                'meta_keywords' => $validated['meta_keywords'] ?? '',
                'og_title' => $validated['og_title'] ?? $validated['meta_title'],
                'og_description' => $validated['og_description'] ?? $validated['meta_description'],
                'twitter_title' => $validated['twitter_title'] ?? $validated['meta_title'],
                'twitter_description' => $validated['twitter_description'] ?? $validated['meta_description'],
            ]);

            return redirect()
                ->route('admin.ads.templates.show', $template->id)
                ->with('success', 'Template créé avec succès ! Vous pouvez maintenant générer des annonces.');

        } catch (\Exception $e) {
            Log::error('Erreur création template manuel', [
                'error' => $e->getMessage()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création du template: ' . $e->getMessage());
        }
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        // Récupérer les services depuis les settings pour le select
        $servicesData = Setting::get('services', '[]');
        $services = is_string($servicesData) ? json_decode($servicesData, true) : ($servicesData ?? []);
        
        if (!is_array($services)) {
            $services = [];
        }

        return view('admin.ads.templates.create', compact('services'));
    }

    /**
     * Créer un template à partir d'un service (DÉSACTIVÉ - Utiliser store() à la place)
     * @deprecated
     */
    public function createFromService(Request $request)
    {
        $request->validate([
            'service_slug' => 'required|string',
            'ai_prompt' => 'nullable|string|max:5000',
        ]);

        $serviceSlug = $request->input('service_slug');
        
        // Récupérer les services depuis les settings
        $servicesData = Setting::get('services', '[]');
        $services = is_string($servicesData) ? json_decode($servicesData, true) : ($servicesData ?? []);
        
        if (!is_array($services)) {
            $services = [];
        }
        
        $service = collect($services)->firstWhere('slug', $serviceSlug);
        
        // Log pour vérifier la structure du service récupéré
        if ($service) {
            Log::info('Service récupéré pour création template', [
                'service_name' => $service['name'] ?? 'N/A',
                'service_slug' => $serviceSlug,
                'has_featured_image' => isset($service['featured_image']),
                'featured_image_value' => $service['featured_image'] ?? 'null',
                'has_og_image' => isset($service['og_image']),
                'og_image_value' => $service['og_image'] ?? 'null',
                'service_keys' => array_keys($service)
            ]);
        }
        
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service non trouvé'
            ], 404);
        }

        try {
            // Vérifier si des templates existent déjà pour ce service
            $existingTemplates = AdTemplate::where('service_slug', $serviceSlug)->get();
            
            if ($existingTemplates->count() > 0 && !$request->input('force_create', false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Des templates existent déjà pour ce service',
                    'existing_templates' => $existingTemplates->map(function($template) {
                        return [
                            'id' => $template->id,
                            'name' => $template->name,
                            'is_active' => $template->is_active,
                            'ads_count' => $template->ads()->count(),
                            'created_at' => $template->created_at->format('d/m/Y H:i')
                        ];
                    })
                ], 400);
            }

            // Utiliser generateCompleteTemplateContent inspiré de ServicesController
            $companyInfo = $this->getCompanyInfo();
            
            try {
            $aiContent = $this->generateCompleteTemplateContent(
                $service['name'], 
                $service['short_description'] ?? '',
                $companyInfo,
                $request->input('ai_prompt')
            );
            } catch (\Exception $e) {
                Log::error('Erreur lors de la génération du contenu IA dans createFromService', [
                    'service_name' => $service['name'],
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création du template via IA: ' . $e->getMessage() . '. Vérifiez vos clés API ChatGPT ou Groq.'
                ], 500);
            }
            
            // Vérifier que aiContent contient les champs requis
            if (!isset($aiContent['description']) || empty($aiContent['description'])) {
                Log::error('aiContent ne contient pas description', [
                    'service_name' => $service['name'],
                    'aiContent_keys' => array_keys($aiContent ?? [])
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur: Le contenu généré par l\'IA est incomplet. Veuillez réessayer.'
                ], 500);
            }
            
            // Copier l'image du service vers le template
            $featuredImage = $service['featured_image'] ?? $service['og_image'] ?? null;
            
            // Log pour debugging
            Log::info('Copie image service vers template', [
                'service_name' => $service['name'],
                'featured_image' => $featuredImage,
                'service_keys' => array_keys($service),
                'has_featured_image' => isset($service['featured_image']),
                'has_og_image' => isset($service['og_image'])
            ]);
            
            // Créer le template avec valeurs par défaut pour éviter les erreurs de validation
            try {
            $template = AdTemplate::create([
                    'name' => $service['name'] ?? 'Template sans nom',
                    'service_name' => $service['name'] ?? '',
                    'service_slug' => $service['slug'] ?? '',
                    'content_html' => $aiContent['description'] ?? '',
                    'short_description' => $aiContent['short_description'] ?? ($service['short_description'] ?? ''),
                    'long_description' => $aiContent['long_description'] ?? '',
                    'icon' => $aiContent['icon'] ?? 'fas fa-tools',
                'featured_image' => $featuredImage,
                    'meta_title' => $aiContent['meta_title'] ?? ($service['name'] . ' à [VILLE] - Expert professionnel'),
                    'meta_description' => $aiContent['meta_description'] ?? ('Service professionnel de ' . ($service['name'] ?? '') . ' à [VILLE]'),
                    'meta_keywords' => $aiContent['meta_keywords'] ?? '',
                    'og_title' => $aiContent['og_title'] ?? ($service['name'] . ' à [VILLE]'),
                    'og_description' => $aiContent['og_description'] ?? ($aiContent['meta_description'] ?? ''),
                    'twitter_title' => $aiContent['twitter_title'] ?? ($aiContent['og_title'] ?? ''),
                    'twitter_description' => $aiContent['twitter_description'] ?? ($aiContent['og_description'] ?? ''),
                    'ai_prompt_used' => $request->input('ai_prompt') ? ['prompt' => $request->input('ai_prompt')] : null,
                'ai_response_data' => $aiContent,
                    'is_active' => true,
                    'usage_count' => 0,
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                Log::error('Erreur lors de la création du template (QueryException)', [
                    'service_name' => $service['name'],
                    'error' => $e->getMessage(),
                    'sql_state' => $e->getCode(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création du template : ' . $e->getMessage()
                ], 500);
            } catch (\Exception $e) {
                Log::error('Erreur lors de la création du template (Exception générale)', [
                    'service_name' => $service['name'],
                    'error' => $e->getMessage(),
                    'error_type' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création du template : ' . $e->getMessage()
                ], 500);
            }

            // Retourner une réponse JSON pour les appels AJAX
            return response()->json([
                'success' => true,
                'message' => 'Template créé avec succès. Vous pouvez maintenant le personnaliser avant de générer les annonces.',
                'template_id' => $template->id,
                'redirect_url' => route('admin.ads.templates.edit', $template->id)
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur création template', [
                'service' => $service['name'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'success' => false,
                'message' => 'Erreur lors de la création du template via IA: ' . $e->getMessage() . '. Vérifiez vos clés API ChatGPT ou Groq.'
                ], 500);
        }
    }

    /**
     * Générer des annonces à partir d'un template pour plusieurs villes
     */
    public function generateAdsFromTemplate(Request $request)
    {
        try {
            $request->validate([
                'template_id' => 'required|exists:ad_templates,id',
                'city_ids' => 'required|array|min:1',
                'city_ids.*' => 'required|integer|exists:cities,id',
            ]);

            $template = AdTemplate::findOrFail($request->input('template_id'));
            $cityIds = $request->input('city_ids');
            $cities = City::whereIn('id', $cityIds)->get();

            $createdAds = 0;
            $skippedAds = 0;
            $errors = [];

            foreach ($cities as $city) {
                try {
                    // Vérifier si une annonce existe déjà pour cette combinaison
                    $existingAd = \App\Models\Ad::where('template_id', $template->id)
                        ->where('city_id', $city->id)
                        ->first();

                    if ($existingAd) {
                        $skippedAds++;
                        continue;
                    }

                    // Obtenir le contenu et les métadonnées pour cette ville
                    $contentForCity = $template->getContentForCity($city);
                    $metaForCity = $template->getMetaForCity($city);

                    // Créer l'annonce
                    $ad = \App\Models\Ad::create([
                        'title' => $template->service_name . ' à ' . $city->name,
                        'keyword' => $template->service_name,
                        'city_id' => $city->id,
                        'template_id' => $template->id,
                        'slug' => $this->generateUniqueSlug(Str::slug($template->service_name . '-' . $city->name)),
                        'status' => 'published',
                        'published_at' => now(),
                        'meta_title' => $metaForCity['meta_title'],
                        'meta_description' => $metaForCity['meta_description'],
                        'meta_keywords' => $metaForCity['meta_keywords'],
                        'content_html' => $contentForCity,
                        'content_json' => json_encode([
                            'template_id' => $template->id,
                            'city' => $city->toArray(),
                            'generated_at' => now()->toISOString()
                        ])
                    ]);

                    $createdAds++;
                    
                    // Incrémenter le compteur d'utilisation du template
                    $template->incrementUsage();

                } catch (\Exception $e) {
                    $errors[] = [
                        'city' => $city->name,
                        'error' => $e->getMessage()
                    ];
                    Log::error('Erreur création annonce depuis template', [
                        'template_id' => $template->id,
                        'city' => $city->name,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'created' => $createdAds,
                'skipped' => $skippedAds,
                'errors' => $errors,
                'message' => "Génération terminée : {$createdAds} annonces créées, {$skippedAds} ignorées"
            ]);
        } catch (\Throwable $e) {
            Log::error('Erreur globale génération annonces', [
                'template_id' => $request->input('template_id'),
                'city_ids' => $request->input('city_ids'),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération des annonces (global): ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Générer le contenu du template via IA
     */
    private function generateTemplateContent($service, $aiPrompt = null)
    {
        // Construire le prompt pour le template (sans ville spécifique)
        $prompt = $this->buildTemplatePrompt($service['name'], $aiPrompt);
        
        Log::info('=== DÉBUT GÉNÉRATION TEMPLATE ===', [
            'service_name' => $service['name'],
            'chatgpt_enabled' => setting('chatgpt_enabled', true),
            'chatgpt_api_key_exists' => !empty(setting('chatgpt_api_key')),
            'groq_api_key_exists' => !empty(setting('groq_api_key', ''))
        ]);
        
        // Message système pour forcer la personnalisation
        $systemMessage = "Tu es un expert technique en {$service['name']} avec une connaissance approfondie du domaine. CRITIQUE ABSOLUE: Chaque contenu DOIT être UNIQUE, TECHNIQUE et SPÉCIFIQUE à {$service['name']}. INTERDIT d'utiliser des prestations génériques ou du contenu copié. Adapte TOUT spécifiquement au service {$service['name']}.";
        
        $result = AiService::callAI($prompt, $systemMessage, [
            'max_tokens' => 4000,
            'temperature' => 0.9,  // Augmenté pour plus de créativité et personnalisation
            'timeout' => 120
        ]);

        if (!$result || !isset($result['content'])) {
            Log::error('Échec génération template - Aucune réponse de l\'IA', [
                'service_name' => $service['name'],
                'result' => $result
            ]);
            throw new \Exception('Erreur API IA: Impossible de générer le contenu. ChatGPT et Groq ont tous deux échoué.');
        }

        $provider = $result['provider'] ?? 'unknown';
        $aiContent = $result['content'];
        
        Log::info('Réponse IA reçue pour template', [
            'service_name' => $service['name'],
            'provider' => $provider,
            'content_length' => strlen($aiContent),
            'content_preview' => substr($aiContent, 0, 200)
        ]);
        
        // Valider et nettoyer le contenu
        return $this->validateAndCleanAIData($aiContent, $service['name']);
    }

    /**
     * Construire le prompt pour un template (sans ville spécifique)
     */
    private function buildTemplatePrompt($serviceName, $aiPrompt = null)
    {
        $basePrompt = "Tu es un expert technique en {$serviceName} avec une connaissance PROFONDE des prestations, techniques et matériaux spécifiques à ce domaine. Crée un template d'annonce TOTALEMENT personnalisé pour {$serviceName}.

⚠️⚠️⚠️ SERVICE À PERSONNALISER: {$serviceName} ⚠️⚠️⚠️

🚫 INTERDICTIONS ABSOLUES:
- INTERDIT d'utiliser des prestations génériques comme 'Diagnostic', 'Conseil', 'Maintenance générale', 'Installation professionnelle'
- INTERDIT de copier du contenu générique applicable à tous les services
- INTERDIT d'utiliser un vocabulaire vague ou général

✅ OBLIGATIONS ABSOLUES POUR {$serviceName}:
- Chaque prestation DOIT être TECHNIQUE et SPÉCIFIQUE UNIQUEMENT à {$serviceName}
- Utilise le vocabulaire PROFESSIONNEL du métier de {$serviceName}
- Les prestations doivent mentionner des techniques, matériaux ou méthodes PRÉCISES liés à {$serviceName}
- Chaque description doit expliquer QUOI, COMMENT et POURQUOI spécifiquement pour {$serviceName}

EXEMPLES DE PRESTATIONS SPÉCIFIQUES:
- Pour 'Rénovation de plomberie': 'Diagnostic et inspection de plomberie', 'Nettoyage et démoussage', 'Réparation partielle de plomberie', 'Réfection complète de plomberie', 'Isolation de plomberie', 'Étanchéité et traitement hydrofuge', 'Réparation de zinguerie', 'Pose de charpente', 'Installation de fenêtres de toit', 'Entretien annuel et maintenance préventive'
- Pour 'Plomberie': 'Installation de chauffe-eau', 'Réparation de fuites', 'Débouchage de canalisations', 'Pose de robinetterie', 'Installation de WC', 'Rénovation de salle de bain', 'Détection de fuites', 'Installation de radiateurs', 'Raccordement gaz', 'Maintenance préventive'

GÉNÈRE UN JSON AVEC CES CHAMPS:

{
  \"description\": \"<div class='grid md:grid-cols-2 gap-8'><div class='space-y-6'><div class='space-y-4'><p class='text-lg leading-relaxed'>Service professionnel de {$serviceName} à [VILLE], une expertise reconnue dans [RÉGION].</p><p class='text-lg leading-relaxed'>Spécialistes en travaux de {$serviceName} pour une qualité supérieure. Nous maîtrisons les techniques modernes garantissant des résultats durables.</p></div><div class='bg-blue-50 p-6 rounded-lg'><h3 class='text-xl font-bold text-gray-900 mb-3'>Notre Engagement Qualité</h3><p class='leading-relaxed mb-3'>Nous garantissons la satisfaction totale de nos clients à [VILLE] et dans toute la région de [RÉGION].</p><p class='leading-relaxed'>Chaque intervention de {$serviceName} est réalisée selon les normes professionnelles les plus strictes.</p></div><h3 class='text-2xl font-bold text-gray-900 mb-4'>Nos Prestations {$serviceName}</h3><ul class='space-y-3'>[GÉNÈRE 10 PRESTATIONS SPÉCIFIQUES À {$serviceName} AVEC DES DESCRIPTIONS DÉTAILLÉES]</ul><div class='bg-gray-50 p-6 rounded-lg mt-6'><h4 class='text-xl font-bold text-gray-900 mb-3'>FAQ</h4><div class='space-y-2'><p><strong>Q1: Combien coûte un service de {$serviceName} à [VILLE]?</strong></p><p>A: Le prix dépend de la complexité et de l'ampleur des travaux. Nous proposons des devis gratuits et personnalisés.</p><p><strong>Q2: Quel est le délai d'intervention pour {$serviceName}?</strong></p><p>A: Nous nous engageons à intervenir rapidement, généralement sous 24-48h selon l'urgence de votre demande.</p><p><strong>Q3: Proposez-vous une garantie sur vos services de {$serviceName}?</strong></p><p>A: Oui, tous nos travaux sont garantis selon les normes professionnelles en vigueur.</p></div></div></div><div class='space-y-6'><div class='bg-green-50 p-6 rounded-lg'><h3 class='text-xl font-bold text-gray-900 mb-3'>Pourquoi choisir ce service</h3><p class='leading-relaxed'>Notre expertise locale à [VILLE] nous permet de comprendre les spécificités de votre région et d'adapter nos services en conséquence.</p></div><h3 class='text-2xl font-bold text-gray-900 mb-4'>Notre Expertise Locale</h3><p class='leading-relaxed'>Depuis plusieurs années, nous intervenons sur [VILLE] et sa région, développant une connaissance approfondie des besoins locaux en {$serviceName}.</p><div class='bg-yellow-50 p-6 rounded-lg border-l-4 border-yellow-600'><h4 class='text-xl font-bold text-gray-900 mb-3'>Financement et aides</h4><p>Nous vous accompagnons dans vos démarches pour bénéficier des aides financières disponibles pour vos travaux de {$serviceName}.</p></div><div class='bg-gradient-to-r from-blue-50 to-green-50 p-6 rounded-lg border-l-4 border-blue-600'><h4 class='text-xl font-bold text-gray-900 mb-3'>Besoin d'un devis?</h4><p class='mb-4'>Contactez-nous pour un devis gratuit pour {$serviceName} à [VILLE].</p><a href='[FORM_URL]' class='inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300'>Demande de devis</a></div><div class='bg-gray-50 p-6 rounded-lg'><h4 class='text-lg font-bold text-gray-900 mb-3'>Informations Pratiques</h4><ul class='space-y-2 text-sm'><li class='flex items-center'><i class='fas fa-check text-green-600 mr-3 flex-shrink-0'></i><span>Devis gratuit et sans engagement</span></li><li class='flex items-center'><i class='fas fa-check text-green-600 mr-3 flex-shrink-0'></i><span>Intervention rapide sur [VILLE]</span></li><li class='flex items-center'><i class='fas fa-check text-green-600 mr-3 flex-shrink-0'></i><span>Garantie sur tous nos travaux</span></li></ul></div><div class='mt-8 pt-6 border-t border-gray-200'><div class='text-center'><h4 class='text-lg font-semibold text-gray-800 mb-4'>Partager ce service</h4><div class='flex justify-center items-center space-x-4'><a href='https://www.facebook.com/sharer/sharer.php?u=[URL]&quote=[TITRE]' target='_blank' rel='noopener noreferrer' class='bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1'><i class='fab fa-facebook-f text-lg'></i><span class='font-medium'>Facebook</span></a><a href='https://wa.me/?text=[TITRE] - [URL]' target='_blank' rel='noopener noreferrer' class='bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-full transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1'><i class='fab fa-whatsapp text-lg'></i><span class='font-medium'>WhatsApp</span></a><a href='mailto:?subject=[TITRE]&body=Je vous partage ce service intéressant : [URL]' class='bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-full transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1'><i class='fas fa-envelope text-lg'></i><span class='font-medium'>Email</span></a></div></div></div></div>\",
  \"short_description\": \"Service professionnel de {$serviceName} à [VILLE] - Devis gratuit et intervention rapide\",
  \"long_description\": \"Notre entreprise spécialisée en {$serviceName} intervient sur [VILLE] et dans toute la région de [RÉGION]. Nous proposons des services complets incluant diagnostic, réparation, installation et maintenance. Notre équipe d'experts maîtrise les techniques les plus modernes pour garantir des résultats durables et performants. Nous nous adaptons aux spécificités climatiques locales et respectons toutes les normes professionnelles en vigueur.\",
  \"icon\": \"fas fa-tools\",
  \"meta_title\": \"{$serviceName} à [VILLE] - Service professionnel\",
  \"meta_description\": \"Service professionnel de {$serviceName} à [VILLE]. Devis gratuit, intervention rapide, garantie sur tous nos travaux.\",
  \"og_title\": \"{$serviceName} à [VILLE] - Service professionnel\",
  \"og_description\": \"Service professionnel de {$serviceName} à [VILLE]. Devis gratuit, intervention rapide, garantie sur tous nos travaux.\",
  \"twitter_title\": \"{$serviceName} à [VILLE] - Service professionnel\",
  \"twitter_description\": \"Service professionnel de {$serviceName} à [VILLE]. Devis gratuit, intervention rapide, garantie sur tous nos travaux.\",
  \"meta_keywords\": \"{$serviceName}, [VILLE], [RÉGION], service professionnel, devis gratuit\"
}

⚠️⚠️⚠️ INSTRUCTIONS CRITIQUES - FORMAT JSON ⚠️⚠️⚠️:
- TU DOIS RÉPONDRE UNIQUEMENT AVEC UN JSON VALIDE
- COMMENCE DIRECTEMENT PAR { (accolade ouvrante)
- TERMINE DIRECTEMENT PAR } (accolade fermante)
- PAS de texte avant le JSON
- PAS de texte après le JSON
- PAS de ```json ou ``` autour du JSON
- PAS de commentaires ou explications
- JUSTE le JSON brut

⚠️⚠️⚠️ INSTRUCTIONS CRITIQUES - CONTENU ⚠️⚠️⚠️:
- REMPLACE TOUT le contenu par du contenu VRAIMENT spécifique à {$serviceName}
- REMPLACE [GÉNÈRE 10 PRESTATIONS SPÉCIFIQUES À {$serviceName}] par 10 prestations TECHNIQUES RÉELLES pour {$serviceName}
- Chaque prestation doit avoir un NOM TECHNIQUE précis et une DESCRIPTION détaillée avec techniques/matériaux pour {$serviceName}
- PERSONNALISE les descriptions, FAQ, et tous les textes pour {$serviceName} spécifiquement
- Utilise [VILLE], [RÉGION], [DÉPARTEMENT] comme placeholders pour les variables dynamiques
- Le contenu HTML doit être COMPLET et PERSONNALISÉ, pas un template copié-collé

EXEMPLES CONCRETS POUR {$serviceName}:
- Si {$serviceName} = 'Désamiantage' → prestations: 'Dépollution amiante', 'Retrait amiante sous confinement', 'Gestion déchets amiante'
- Si {$serviceName} = 'Traitement humidité' → prestations: 'Diagnostic humidité par imagerie thermique', 'Injection résine anti-humidité', 'Installation VMC double flux'
- Si {$serviceName} = 'Rénovation plomberie' → prestations: 'Diagnostic plomberie par drone', 'Réfection tuiles ardoise', 'Installation écran de sous-plomberie'
";

        if ($aiPrompt) {
            $basePrompt .= "\n\nINSTRUCTIONS PERSONNALISÉES SUPPLÉMENTAIRES:\n" . $aiPrompt;
        }

        return $basePrompt;
    }

    /**
     * Valider et nettoyer les données IA
     */
    private function validateAndCleanAIData($aiContent, $serviceName)
    {
        try {
            // Nettoyer le contenu
            $cleanContent = $this->cleanHtmlContent($aiContent);
            
            Log::info('Contenu nettoyé pour validation', [
                'service' => $serviceName,
                'content_length' => strlen($cleanContent),
                'content_preview' => substr($cleanContent, 0, 300)
            ]);
            
            // Extraire le JSON
            $jsonContent = $this->extractJsonFromContent($cleanContent);
            
            // Si jsonContent est null, c'est que c'est du HTML direct
            if ($jsonContent === null) {
                Log::info('Contenu HTML direct détecté, création structure JSON');
                $plainText = strip_tags($cleanContent);
                return [
                    'description' => $cleanContent,
                    'short_description' => Str::limit($plainText, 140),
                    'long_description' => Str::limit($plainText, 500),
                    'icon' => 'fas fa-tools',
                    'meta_title' => $serviceName . ' à [VILLE] - Service professionnel',
                    'meta_description' => Str::limit($plainText, 160),
                    'og_title' => $serviceName . ' à [VILLE] - Service professionnel',
                    'og_description' => Str::limit($plainText, 160),
                    'twitter_title' => $serviceName . ' à [VILLE] - Service professionnel',
                    'twitter_description' => Str::limit($plainText, 160),
                    'meta_keywords' => $serviceName . ', ' . $serviceName . ' [VILLE], ' . $serviceName . ' [RÉGION], expert ' . $serviceName . ', ' . $serviceName . ' professionnel, entreprise ' . $serviceName . ', artisan ' . $serviceName . ', ' . $serviceName . ' certifié, rénovation, réparation, installation, intervention rapide, devis gratuit, qualité garantie, intervention [VILLE], service [VILLE], professionnel [VILLE]'
                ];
            }
            
            if (empty($jsonContent)) {
                // Dernière tentative : chercher du JSON malformé mais récupérable
                Log::warning('Aucun JSON valide trouvé, tentative extraction manuelle');
                
                // Si le contenu contient du HTML avec des balises, essayer d'extraire
                if (preg_match('/"description"\s*:\s*"([^"]*(?:\\.[^"]*)*)"/s', $cleanContent, $matches)) {
                    Log::info('Extraction description HTML depuis JSON malformé');
                    $htmlContent = str_replace(['\\"', '\\n'], ['"', "\n"], $matches[1]);
                    $plainText = strip_tags($htmlContent);
                    
                    return [
                        'description' => $htmlContent,
                        'short_description' => Str::limit($plainText, 140),
                        'long_description' => Str::limit($plainText, 500),
                        'icon' => 'fas fa-tools',
                        'meta_title' => $serviceName . ' à [VILLE] - Service professionnel',
                        'meta_description' => Str::limit($plainText, 160),
                        'og_title' => $serviceName . ' à [VILLE] - Service professionnel',
                        'og_description' => Str::limit($plainText, 160),
                        'twitter_title' => $serviceName . ' à [VILLE] - Service professionnel',
                        'twitter_description' => Str::limit($plainText, 160),
                        'meta_keywords' => $serviceName . ', ' . $serviceName . ' [VILLE], ' . $serviceName . ' [RÉGION], expert ' . $serviceName . ', ' . $serviceName . ' professionnel, entreprise ' . $serviceName . ', artisan ' . $serviceName . ', ' . $serviceName . ' certifié, rénovation, réparation, installation, intervention rapide, devis gratuit, qualité garantie, intervention [VILLE], service [VILLE], professionnel [VILLE]'
                    ];
                }
                
                throw new \Exception('Aucun JSON valide trouvé dans le contenu. Contenu reçu: ' . substr($cleanContent, 0, 500));
            }
            
            // Parser le JSON
            $aiData = json_decode($jsonContent, true);
            
            if (!$aiData || !is_array($aiData)) {
                // Tentative de correction
                $correctedContent = $this->attemptJsonCorrection($jsonContent);
                $aiData = json_decode($correctedContent, true);
                
                if (!$aiData || !is_array($aiData)) {
                    Log::error('JSON invalide même après correction', [
                        'json_error' => json_last_error_msg(),
                        'json_preview' => substr($jsonContent, 0, 500)
                    ]);
                    throw new \Exception('JSON invalide après correction: ' . json_last_error_msg());
                }
            }
            
            if (!isset($aiData['description'])) {
                throw new \Exception('Champ description manquant dans les données IA');
            }
            
            // Vérifier que le contenu est personnalisé et non générique
            $description = $aiData['description'] ?? '';
            $isGeneric = $this->isContentGeneric($description, $serviceName);
            
            if ($isGeneric) {
                Log::warning('Contenu template détecté comme générique', [
                    'service' => $serviceName,
                    'description_preview' => substr(strip_tags($description), 0, 200)
                ]);
                // On laisse passer mais on log pour information
            }
            
            Log::info('Données IA template validées avec succès', [
                'service' => $serviceName,
                'has_description' => isset($aiData['description']),
                'description_length' => strlen($aiData['description'] ?? '')
            ]);
            
            return $aiData;
            
        } catch (\Exception $e) {
            Log::error('Erreur validation données IA template', [
                'service' => $serviceName,
                'error' => $e->getMessage(),
                'content_preview' => substr($aiContent ?? '', 0, 500)
            ]);
            throw $e;
        }
    }

    /**
     * Nettoyer le contenu HTML généré par l'IA
     */
    private function cleanHtmlContent($content)
    {
        // Supprimer les balises markdown
        $content = preg_replace('/```json\s*/', '', $content);
        $content = preg_replace('/```\s*$/', '', $content);
        $content = preg_replace('/```html\s*/', '', $content);
        
        // Nettoyer les caractères d'échappement
        $content = str_replace(['\"', '\\n', '\\t'], ['"', "\n", "\t"], $content);
        
        return trim($content);
    }

    /**
     * Extraire le JSON du contenu (amélioré pour gérer différents formats)
     */
    private function extractJsonFromContent($content)
    {
        $content = trim($content);
        
        // Si le contenu semble être directement du HTML (pas de JSON)
        if (strpos($content, '<div') !== false && strpos($content, '{') === false) {
            Log::info('Contenu HTML direct détecté dans template, pas de JSON');
            return null; // Retourner null pour indiquer qu'on doit créer une structure JSON
        }
        
        // Pattern 1: JSON dans code block markdown
        $patterns = [
            '/```json\s*(\{[\s\S]*?\})\s*```/s',
            '/```\s*(\{[\s\S]*?\})\s*```/s',
            '/\{[\s\S]*"description"[\s\S]*\}/s',  // JSON avec description
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                $jsonString = $matches[1] ?? $matches[0];
                $jsonString = trim($jsonString);
                
                // Essayer de parser
                $data = json_decode($jsonString, true);
                if ($data && is_array($data)) {
                    Log::info('JSON extrait avec succès via pattern');
                    return $jsonString;
                }
            }
        }
        
        // Pattern 2: Chercher directement le JSON brut
        $firstBrace = strpos($content, '{');
        $lastBrace = strrpos($content, '}');
        
        if ($firstBrace !== false && $lastBrace !== false && $firstBrace < $lastBrace) {
        $jsonContent = substr($content, $firstBrace, $lastBrace - $firstBrace + 1);
        
            // Essayer de parser directement
            $data = json_decode($jsonContent, true);
            if ($data && is_array($data)) {
                Log::info('JSON extrait directement');
            return $jsonContent;
        }
            
            // Essayer après correction
            $corrected = $this->attemptJsonCorrection($jsonContent);
            $data = json_decode($corrected, true);
            if ($data && is_array($data)) {
                Log::info('JSON extrait après correction');
                return $corrected;
            }
        }
        
        Log::warning('Impossible d\'extraire JSON du contenu', [
            'content_preview' => substr($content, 0, 500)
        ]);
        
        return '';
    }

    /**
     * Tenter de corriger un JSON malformé
     */
    private function attemptJsonCorrection($content)
    {
        // Supprimer les caractères de contrôle
        $content = preg_replace('/[\x00-\x1F\x7F]/', '', $content);
        
        // Corriger les apostrophes non échappées
        $content = preg_replace_callback('/"([^"]*\'[^"]*)"/', function($matches) {
            $string = $matches[1];
            $string = str_replace("'", "\\'", $string);
            return '"' . $string . '"';
        }, $content);
        
        // Supprimer les virgules en trop
        $content = preg_replace('/,(\s*[}\]])/', '$1', $content);
        
        return $content;
    }

    /**
     * Vérifier si le contenu est générique
     */
    private function isContentGeneric($description, $serviceName)
    {
        $descriptionLower = mb_strtolower($description);
        $serviceNameLower = mb_strtolower($serviceName);
        
        // Prestations génériques interdites
        $genericTerms = [
            'réparation et maintenance',
            'installation professionnelle',
            'conseils personnalisés',
            'diagnostic précis et traitement adapté',
            'remplacement intégral avec matériaux de qualité',
            'pose selon les normes en vigueur',
            'accompagnement dans vos choix'
        ];
        
        // Vérifier la présence de termes génériques
        $hasGenericTerms = false;
        foreach ($genericTerms as $term) {
            if (stripos($descriptionLower, $term) !== false) {
                $hasGenericTerms = true;
                break;
            }
        }
        
        // Vérifier si le nom du service est présent dans le contenu
        $containsServiceName = stripos($descriptionLower, $serviceNameLower) !== false;
        
        // Vérifier si le contenu est trop court (probablement générique)
        $plainText = strip_tags($description);
        $isTooShort = strlen($plainText) < 1000;
        
        // Le contenu est générique si :
        // - Il contient des termes génériques OU
        // - Le nom du service n'est pas présent ET le contenu est trop court
        return $hasGenericTerms || (!$containsServiceName && $isTooShort);
    }

    /**
     * Générer un contenu de fallback pour un template
     */
    private function generateFallbackTemplateContent($service)
    {
        $serviceName = $service['name'];
        $serviceSlug = $service['slug'];
        
        // Contenu HTML de fallback avec la même structure que l'IA
        $contentHtml = '<div class="grid md:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div class="space-y-4">
                    <p class="text-lg leading-relaxed">Service professionnel de ' . $serviceName . ' à [VILLE], une expertise reconnue dans [RÉGION].</p>
                    <p class="text-lg leading-relaxed">Spécialistes en travaux de ' . $serviceName . ' pour une qualité supérieure. Nous maîtrisons les techniques modernes garantissant des résultats durables.</p>
                </div>
                <div class="bg-blue-50 p-6 rounded-lg">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Notre Engagement Qualité</h3>
                    <p class="leading-relaxed mb-3">Nous garantissons la satisfaction totale de nos clients à [VILLE] et dans toute la région de [RÉGION].</p>
                    <p class="leading-relaxed">Chaque intervention de ' . $serviceName . ' est réalisée selon les normes professionnelles les plus strictes.</p>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Nos Prestations ' . $serviceName . '</h3>
                <ul class="space-y-3"><!-- PRESTATIONS SUPPRIMÉES - FONCTION DÉPRÉCIÉE --></ul>
                <div class="bg-gray-50 p-6 rounded-lg mt-6">
                    <h4 class="text-xl font-bold text-gray-900 mb-3">FAQ</h4>
                    <div class="space-y-2">
                        <p><strong>Q1: Combien coûte un service de ' . $serviceName . ' à [VILLE]?</strong></p>
                        <p>A: Le prix dépend de la complexité et de l\'ampleur des travaux. Nous proposons des devis gratuits et personnalisés.</p>
                        <p><strong>Q2: Quel est le délai d\'intervention pour ' . $serviceName . '?</strong></p>
                        <p>A: Nous nous engageons à intervenir rapidement, généralement sous 24-48h selon l\'urgence de votre demande.</p>
                        <p><strong>Q3: Proposez-vous une garantie sur vos services de ' . $serviceName . '?</strong></p>
                        <p>A: Oui, tous nos travaux sont garantis selon les normes professionnelles en vigueur.</p>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-green-50 p-6 rounded-lg">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Pourquoi choisir ce service</h3>
                    <p class="leading-relaxed">Notre expertise locale à [VILLE] nous permet de comprendre les spécificités de votre région et d\'adapter nos services en conséquence.</p>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Notre Expertise Locale</h3>
                <p class="leading-relaxed">Depuis plusieurs années, nous intervenons sur [VILLE] et sa région, développant une connaissance approfondie des besoins locaux en ' . $serviceName . '.</p>
                <div class="bg-gradient-to-r from-blue-50 to-green-50 p-6 rounded-lg border-l-4 border-blue-600">
                    <h4 class="text-xl font-bold text-gray-900 mb-3">Besoin d\'un devis?</h4>
                    <p class="mb-4">Contactez-nous pour un devis gratuit pour ' . $serviceName . ' à [VILLE].</p>
                    <a href="[FORM_URL]" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300">Demande de devis</a>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h4 class="text-lg font-bold text-gray-900 mb-3">Informations Pratiques</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center"><i class="fas fa-check text-green-600 mr-3 flex-shrink-0"></i><span>Devis gratuit et sans engagement</span></li>
                        <li class="flex items-center"><i class="fas fa-check text-green-600 mr-3 flex-shrink-0"></i><span>Intervention rapide sur [VILLE]</span></li>
                        <li class="flex items-center"><i class="fas fa-check text-green-600 mr-3 flex-shrink-0"></i><span>Garantie sur tous nos travaux</span></li>
                    </ul>
                </div>
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="text-center">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Partager ce service</h4>
                        <div class="flex justify-center items-center space-x-4">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=[URL]&quote=[TITRE]" target="_blank" rel="noopener noreferrer" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                <i class="fab fa-facebook-f text-lg"></i>
                                <span class="font-medium">Facebook</span>
                            </a>
                            <a href="https://wa.me/?text=[TITRE] - [URL]" target="_blank" rel="noopener noreferrer" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-full transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                <i class="fab fa-whatsapp text-lg"></i>
                                <span class="font-medium">WhatsApp</span>
                            </a>
                            <a href="mailto:?subject=[TITRE]&body=Je vous partage ce service intéressant : [URL]" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-full transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                <i class="fas fa-envelope text-lg"></i>
                                <span class="font-medium">Email</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        return [
            'description' => $contentHtml,
            'short_description' => 'Service professionnel de ' . $serviceName . ' à [VILLE] - Devis gratuit et intervention rapide',
            'long_description' => 'Notre entreprise spécialisée en ' . $serviceName . ' intervient sur [VILLE] et dans toute la région de [RÉGION]. Nous proposons des services complets incluant diagnostic, réparation, installation et maintenance. Notre équipe d\'experts maîtrise les techniques les plus modernes pour garantir des résultats durables et performants. Nous nous adaptons aux spécificités climatiques locales et respectons toutes les normes professionnelles en vigueur.',
            'icon' => 'fas fa-tools',
            'meta_title' => $serviceName . ' à [VILLE] - Service professionnel',
            'meta_description' => 'Service professionnel de ' . $serviceName . ' à [VILLE]. Devis gratuit, intervention rapide, garantie sur tous nos travaux.',
            'meta_keywords' => $serviceName . ', ' . $serviceName . ' [VILLE], ' . $serviceName . ' [RÉGION], expert ' . $serviceName . ', ' . $serviceName . ' professionnel, entreprise ' . $serviceName . ', artisan ' . $serviceName . ', ' . $serviceName . ' certifié, rénovation, réparation, installation, intervention rapide, devis gratuit, qualité garantie, satisfaction garantie, intervention [VILLE], service [VILLE], professionnel [VILLE]',
            'og_title' => $serviceName . ' à [VILLE] - Service professionnel',
            'og_description' => 'Service professionnel de ' . $serviceName . ' à [VILLE]. Devis gratuit, intervention rapide, garantie sur tous nos travaux.',
            'twitter_title' => $serviceName . ' à [VILLE] - Service professionnel',
            'twitter_description' => 'Service professionnel de ' . $serviceName . ' à [VILLE]. Devis gratuit, intervention rapide, garantie sur tous nos travaux.',
        ];
    }

    /**
     * SUPPRIMÉ - Cette fonction n'est plus utilisée car on force l'IA uniquement
     * @deprecated
     */
    private function generateSpecificPrestations_DELETED($serviceName)
    {
        $prestations = [];
        
        // Détecter le type de service et générer des prestations spécifiques
        $serviceLower = strtolower($serviceName);
        
        if (strpos($serviceLower, 'plomberie') !== false || strpos($serviceLower, 'plomberie') !== false || strpos($serviceLower, 'rénovation') !== false) {
            $prestations = [
                'Diagnostic et inspection de plomberie - Évaluation complète de l\'état de la plomberie et détection des fuites',
                'Nettoyage et démoussage - Nettoyage haute pression et application d\'antimousse professionnel',
                'Réparation partielle de plomberie - Remplacement d\'ardoises, tuiles et réparation des joints',
                'Réfection complète de plomberie - Dépose et pose d\'une nouvelle plomberie selon les normes',
                'Isolation de plomberie - Pose d\'isolants thermiques sous plomberie ou par sarking',
                'Étanchéité et traitement hydrofuge - Protection contre les infiltrations et l\'humidité',
                'Réparation de zinguerie - Pose et entretien des gouttières, chéneaux et descentes d\'eau',
                'Pose de charpente - Réparation ou installation de charpentes en bois traitées',
                'Installation de fenêtres de toit - Pose de Velux et étanchéification des ouvertures',
                'Entretien annuel et maintenance - Inspection périodique et nettoyage saisonnier'
            ];
        } elseif (strpos($serviceLower, 'plomberie') !== false) {
            $prestations = [
                'Installation de chauffe-eau - Pose de chauffe-eau électrique, gaz ou thermodynamique',
                'Réparation de fuites - Détection et réparation de fuites sur canalisations et robinetterie',
                'Débouchage de canalisations - Intervention rapide pour déboucher éviers, WC et canalisations',
                'Pose de robinetterie - Installation de robinets, mitigeurs et accessoires de salle de bain',
                'Installation de WC - Pose, remplacement et raccordement de toilettes et accessoires',
                'Rénovation de salle de bain - Aménagement complet avec carrelage et sanitaires',
                'Détection de fuites - Recherche de fuites cachées avec matériel professionnel',
                'Installation de radiateurs - Pose et raccordement de radiateurs et planchers chauffants',
                'Raccordement gaz - Installation et mise en conformité des installations gaz',
                'Maintenance préventive - Entretien régulier des installations et prévention des pannes'
            ];
        } elseif (strpos($serviceLower, 'électricité') !== false || strpos($serviceLower, 'électrique') !== false) {
            $prestations = [
                'Installation électrique - Mise en conformité et installation de tableaux électriques',
                'Rénovation de tableau électrique - Remplacement et mise aux normes NF C 15-100',
                'Pose de prises et interrupteurs - Installation et remplacement de matériel électrique',
                'Installation d\'éclairage - Pose de spots, lustres et éclairage LED',
                'Installation de volets roulants - Motorisation et automatisation des volets',
                'Installation de climatisation - Pose de climatiseurs et pompes à chaleur',
                'Mise en sécurité - Installation de disjoncteurs différentiels et parafoudres',
                'Installation de domotique - Automatisation et contrôle à distance',
                'Dépannage électrique - Intervention d\'urgence pour pannes et dysfonctionnements',
                'Vérification d\'installation - Contrôle et mise en conformité des installations existantes'
            ];
        } elseif (strpos($serviceLower, 'peinture') !== false) {
            $prestations = [
                'Préparation des surfaces - Ponçage, rebouchage et traitement des murs',
                'Peinture intérieure - Rénovation complète des pièces avec peintures écologiques',
                'Peinture extérieure - Ravalement de façade et protection contre les intempéries',
                'Pose de papier peint - Installation et pose de revêtements muraux',
                'Peinture de plafond - Rénovation et traitement des plafonds',
                'Peinture de menuiseries - Rénovation des portes, fenêtres et volets',
                'Traitement des murs humides - Diagnostic et traitement de l\'humidité',
                'Peinture de cuisine et salle de bain - Revêtements adaptés aux pièces humides',
                'Finitions décoratives - Effets spéciaux, patines et techniques artistiques',
                'Nettoyage et protection - Entretien et protection des surfaces peintes'
            ];
        } elseif (strpos($serviceLower, 'isolation') !== false) {
            $prestations = [
                'Isolation des combles - Pose d\'isolants thermiques sous plomberie',
                'Isolation des murs - Isolation intérieure ou extérieure des parois',
                'Isolation des sols - Pose d\'isolants sous plancher et dalle',
                'Isolation phonique - Réduction des bruits et amélioration acoustique',
                'Isolation des fenêtres - Pose de double vitrage et calfeutrage',
                'Isolation des portes - Pose de joints et amélioration de l\'étanchéité',
                'Isolation des tuyaux - Protection des canalisations contre le gel',
                'Isolation des plomberies terrasses - Pose de membranes isolantes',
                'Isolation des caves - Traitement de l\'humidité et isolation thermique',
                'Audit énergétique - Diagnostic et recommandations d\'amélioration'
            ];
        } else {
            // Prestations génériques pour les autres services
            $prestations = [
                'Diagnostic et évaluation - Analyse complète de vos besoins en ' . $serviceName,
                'Intervention d\'urgence - Service rapide 24h/7j pour ' . $serviceName,
                'Maintenance préventive - Entretien régulier pour éviter les problèmes',
                'Réparation spécialisée - Correction des dysfonctionnements',
                'Installation complète - Pose selon les normes en vigueur',
                'Rénovation totale - Remplacement intégral avec matériaux de qualité',
                'Conseil personnalisé - Recommandations adaptées à votre situation',
                'Suivi post-intervention - Accompagnement après travaux',
                'Formation utilisateur - Apprentissage des bonnes pratiques',
                'Garantie étendue - Protection supplémentaire sur nos interventions'
            ];
        }
        
        // Générer le HTML des prestations
        $html = '';
        foreach ($prestations as $prestation) {
            $html .= '<li class="flex items-start"><i class="fas fa-check text-green-600 mr-3 mt-1 flex-shrink-0"></i><span><strong>' . $prestation . '</strong></span></li>';
        }
        
        return $html;
    }

    /**
     * Générer du contenu de template pour un mot-clé via IA
     */
    private function generateKeywordTemplateContent($keyword, $aiPrompt = null)
    {
        // Construire le prompt pour le mot-clé
        $prompt = $this->buildKeywordTemplatePrompt($keyword, $aiPrompt);
        
        Log::info('=== DÉBUT GÉNÉRATION TEMPLATE MOT-CLÉ ===', [
            'keyword' => $keyword,
            'chatgpt_enabled' => setting('chatgpt_enabled', true),
            'chatgpt_api_key_exists' => !empty(setting('chatgpt_api_key')),
            'groq_api_key_exists' => !empty(setting('groq_api_key', ''))
        ]);
        
        // Message système pour forcer la personnalisation
        $systemMessage = "Tu es un expert technique en {$keyword} avec une connaissance approfondie du domaine. CRITIQUE ABSOLUE: Chaque contenu DOIT être UNIQUE, TECHNIQUE et SPÉCIFIQUE à {$keyword}. INTERDIT d'utiliser des prestations génériques ou du contenu copié. Adapte TOUT spécifiquement au mot-clé {$keyword}.";
        
        // Utiliser AiService avec fallback automatique vers Groq
        $result = AiService::callAI($prompt, $systemMessage, [
            'max_tokens' => 4000,
            'temperature' => 0.9,  // Augmenté pour plus de créativité et personnalisation
            'timeout' => 120
        ]);

        if (!$result || !isset($result['content'])) {
            Log::error('Échec génération template mot-clé - Aucune réponse de l\'IA', [
                'keyword' => $keyword,
                'result' => $result
            ]);
            throw new \Exception('Erreur API IA: Impossible de générer le contenu. ChatGPT et Groq ont tous deux échoué.');
        }

        $provider = $result['provider'] ?? 'unknown';
        $aiContent = $result['content'];
        
        Log::info('Réponse IA reçue pour template mot-clé', [
            'keyword' => $keyword,
            'provider' => $provider,
            'content_length' => strlen($aiContent),
            'content_preview' => substr($aiContent, 0, 200)
        ]);
        
        // Valider et nettoyer le contenu
        return $this->validateAndCleanAIData($aiContent, $keyword);
    }

    /**
     * Construire le prompt pour un template de mot-clé
     */
    private function buildKeywordTemplatePrompt($keyword, $aiPrompt = null)
    {
        $basePrompt = "Tu es un expert technique en {$keyword} avec une connaissance PROFONDE des prestations, techniques et matériaux spécifiques à ce domaine. Crée un template d'annonce TOTALEMENT personnalisé pour {$keyword}.

⚠️⚠️⚠️ MOT-CLÉ À PERSONNALISER: {$keyword} ⚠️⚠️⚠️

🚫 INTERDICTIONS ABSOLUES:
- INTERDIT d'utiliser des prestations génériques comme 'Diagnostic', 'Conseil', 'Maintenance générale', 'Installation professionnelle'
- INTERDIT de copier du contenu générique applicable à tous les services
- INTERDIT d'utiliser un vocabulaire vague ou général

✅ OBLIGATIONS ABSOLUES POUR {$keyword}:
- Chaque prestation DOIT être TECHNIQUE et SPÉCIFIQUE UNIQUEMENT à {$keyword}
- Utilise le vocabulaire PROFESSIONNEL du métier de {$keyword}
- Les prestations doivent mentionner des techniques, matériaux ou méthodes PRÉCISES liés à {$keyword}
- Chaque description doit expliquer QUOI, COMMENT et POURQUOI spécifiquement pour {$keyword}

GÉNÈRE UN JSON AVEC CES CHAMPS:

{
  \"description\": \"<div class='grid md:grid-cols-2 gap-8'><div class='space-y-6'><div class='space-y-4'><p class='text-lg leading-relaxed'>Service professionnel de {$keyword} à [VILLE], une expertise reconnue dans [RÉGION].</p><p class='text-lg leading-relaxed'>Spécialistes en travaux de {$keyword} pour une qualité supérieure. Nous maîtrisons les techniques modernes garantissant des résultats durables.</p></div><div class='bg-blue-50 p-6 rounded-lg'><h3 class='text-xl font-bold text-gray-900 mb-3'>Notre Engagement Qualité</h3><p class='leading-relaxed mb-3'>Nous garantissons la satisfaction totale de nos clients à [VILLE] et dans toute la région de [RÉGION].</p><p class='leading-relaxed'>Chaque intervention de {$keyword} est réalisée selon les normes professionnelles les plus strictes.</p></div><h3 class='text-2xl font-bold text-gray-900 mb-4'>Nos Prestations {$keyword}</h3><ul class='space-y-3'>[GÉNÈRE 10 PRESTATIONS SPÉCIFIQUES À {$keyword} AVEC DES DESCRIPTIONS DÉTAILLÉES]</ul><div class='bg-gray-50 p-6 rounded-lg mt-6'><h4 class='text-xl font-bold text-gray-900 mb-3'>FAQ</h4><div class='space-y-2'><p><strong>Q1: Combien coûte un service de {$keyword} à [VILLE]?</strong></p><p>A: Le prix dépend de la complexité et de l'ampleur des travaux. Nous proposons des devis gratuits et personnalisés.</p><p><strong>Q2: Quel est le délai d'intervention pour {$keyword}?</strong></p><p>A: Nous nous engageons à intervenir rapidement, généralement sous 24-48h selon l'urgence de votre demande.</p><p><strong>Q3: Proposez-vous une garantie sur vos services de {$keyword}?</strong></p><p>A: Oui, tous nos travaux sont garantis selon les normes professionnelles en vigueur.</p></div></div></div><div class='space-y-6'><div class='bg-green-50 p-6 rounded-lg'><h3 class='text-xl font-bold text-gray-900 mb-3'>Pourquoi choisir ce service</h3><p class='leading-relaxed'>Notre expertise locale à [VILLE] nous permet de comprendre les spécificités de votre région et d'adapter nos services en conséquence.</p></div><h3 class='text-2xl font-bold text-gray-900 mb-4'>Notre Expertise Locale</h3><p class='leading-relaxed'>Depuis plusieurs années, nous intervenons sur [VILLE] et sa région, développant une connaissance approfondie des besoins locaux en {$keyword}.</p><div class='bg-yellow-50 p-6 rounded-lg border-l-4 border-yellow-600'><h4 class='text-xl font-bold text-gray-900 mb-3'>Financement et aides</h4><p>Nous vous accompagnons dans vos démarches pour bénéficier des aides financières disponibles pour vos travaux de {$keyword}.</p></div><div class='bg-gradient-to-r from-blue-50 to-green-50 p-6 rounded-lg border-l-4 border-blue-600'><h4 class='text-xl font-bold text-gray-900 mb-3'>Besoin d'un devis?</h4><p class='mb-4'>Contactez-nous pour un devis gratuit pour {$keyword} à [VILLE].</p><a href='[FORM_URL]' class='inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300'>Demande de devis</a></div><div class='bg-gray-50 p-6 rounded-lg'><h4 class='text-lg font-bold text-gray-900 mb-3'>Informations Pratiques</h4><ul class='space-y-2 text-sm'><li class='flex items-center'><i class='fas fa-check text-green-600 mr-3 flex-shrink-0'></i><span>Devis gratuit et sans engagement</span></li><li class='flex items-center'><i class='fas fa-check text-green-600 mr-3 flex-shrink-0'></i><span>Intervention rapide sur [VILLE]</span></li><li class='flex items-center'><i class='fas fa-check text-green-600 mr-3 flex-shrink-0'></i><span>Garantie sur tous nos travaux</span></li></ul></div><div class='mt-8 pt-6 border-t border-gray-200'><div class='text-center'><h4 class='text-lg font-semibold text-gray-800 mb-4'>Partager ce service</h4><div class='flex justify-center items-center space-x-4'><a href='https://www.facebook.com/sharer/sharer.php?u=[URL]&quote=[TITRE]' target='_blank' rel='noopener noreferrer' class='bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1'><i class='fab fa-facebook-f text-lg'></i><span class='font-medium'>Facebook</span></a><a href='https://wa.me/?text=[TITRE] - [URL]' target='_blank' rel='noopener noreferrer' class='bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-full transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1'><i class='fab fa-whatsapp text-lg'></i><span class='font-medium'>WhatsApp</span></a><a href='mailto:?subject=[TITRE]&body=Je vous partage ce service intéressant : [URL]' class='bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-full transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1'><i class='fas fa-envelope text-lg'></i><span class='font-medium'>Email</span></a></div></div></div></div>\",
  \"short_description\": \"Service professionnel de {$keyword} à [VILLE] - Devis gratuit et intervention rapide\",
  \"long_description\": \"Notre entreprise spécialisée en {$keyword} intervient sur [VILLE] et dans toute la région de [RÉGION]. Nous proposons des services complets incluant diagnostic, réparation, installation et maintenance. Notre équipe d'experts maîtrise les techniques les plus modernes pour garantir des résultats durables et performants. Nous nous adaptons aux spécificités climatiques locales et respectons toutes les normes professionnelles en vigueur.\",
  \"icon\": \"fas fa-tools\",
  \"meta_title\": \"{$keyword} à [VILLE] - Service professionnel\",
  \"meta_description\": \"Service professionnel de {$keyword} à [VILLE]. Devis gratuit, intervention rapide, garantie sur tous nos travaux.\",
  \"og_title\": \"{$keyword} à [VILLE] - Service professionnel\",
  \"og_description\": \"Service professionnel de {$keyword} à [VILLE]. Devis gratuit, intervention rapide, garantie sur tous nos travaux.\",
  \"twitter_title\": \"{$keyword} à [VILLE] - Service professionnel\",
  \"twitter_description\": \"Service professionnel de {$keyword} à [VILLE]. Devis gratuit, intervention rapide, garantie sur tous nos travaux.\",
  \"meta_keywords\": \"{$keyword}, [VILLE], [RÉGION], service professionnel, devis gratuit\"
}

⚠️⚠️⚠️ INSTRUCTIONS CRITIQUES - FORMAT JSON ⚠️⚠️⚠️:
- TU DOIS RÉPONDRE UNIQUEMENT AVEC UN JSON VALIDE
- COMMENCE DIRECTEMENT PAR { (accolade ouvrante)
- TERMINE DIRECTEMENT PAR } (accolade fermante)
- PAS de texte avant le JSON
- PAS de texte après le JSON
- PAS de ```json ou ``` autour du JSON
- PAS de commentaires ou explications
- JUSTE le JSON brut

⚠️⚠️⚠️ INSTRUCTIONS CRITIQUES - CONTENU ⚠️⚠️⚠️:
- REMPLACE TOUT le contenu par du contenu VRAIMENT spécifique à {$keyword}
- REMPLACE [GÉNÈRE 10 PRESTATIONS SPÉCIFIQUES À {$keyword}] par 10 prestations TECHNIQUES RÉELLES pour {$keyword}
- Chaque prestation doit avoir un NOM TECHNIQUE précis et une DESCRIPTION détaillée avec techniques/matériaux pour {$keyword}
- PERSONNALISE les descriptions, FAQ, et tous les textes pour {$keyword} spécifiquement
- Utilise [VILLE], [RÉGION], [DÉPARTEMENT] comme placeholders pour les variables dynamiques
- Le contenu HTML doit être COMPLET et PERSONNALISÉ, pas un template copié-collé

EXEMPLES CONCRETS POUR {$keyword}:
- Si {$keyword} = 'Désamiantage' → prestations: 'Dépollution amiante', 'Retrait amiante sous confinement', 'Gestion déchets amiante'
- Si {$keyword} = 'Traitement humidité' → prestations: 'Diagnostic humidité par imagerie thermique', 'Injection résine anti-humidité', 'Installation VMC double flux'
- Si {$keyword} = 'Rénovation plomberie' → prestations: 'Diagnostic plomberie par drone', 'Réfection tuiles ardoise', 'Installation écran de sous-plomberie'
";

        if ($aiPrompt) {
            $basePrompt .= "\n\nINSTRUCTIONS PERSONNALISÉES SUPPLÉMENTAIRES:\n" . $aiPrompt;
        }

        return $basePrompt;
    }

    /**
     * SUPPRIMÉ - Cette fonction n'est plus utilisée car on force l'IA uniquement
     * @deprecated
     */
    private function generateFallbackKeywordTemplateContent_DELETED($keyword)
    {
        // Contenu HTML de fallback avec la même structure que l'IA
        $contentHtml = '<div class="grid md:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div class="space-y-4">
                    <p class="text-lg leading-relaxed">Service professionnel de ' . $keyword . ' à [VILLE], une expertise reconnue dans [RÉGION].</p>
                    <p class="text-lg leading-relaxed">Spécialistes en travaux de ' . $keyword . ' pour une qualité supérieure. Nous maîtrisons les techniques modernes garantissant des résultats durables.</p>
                </div>
                <div class="bg-blue-50 p-6 rounded-lg">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Notre Engagement Qualité</h3>
                    <p class="leading-relaxed mb-3">Nous garantissons la satisfaction totale de nos clients à [VILLE] et dans toute la région de [RÉGION].</p>
                    <p class="leading-relaxed">Chaque intervention de ' . $keyword . ' est réalisée selon les normes professionnelles les plus strictes.</p>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Nos Prestations ' . $keyword . '</h3>
                <ul class="space-y-3"><!-- PRESTATIONS SUPPRIMÉES - FONCTION DÉPRÉCIÉE --></ul>
                <div class="bg-gray-50 p-6 rounded-lg mt-6">
                    <h4 class="text-xl font-bold text-gray-900 mb-3">FAQ</h4>
                    <div class="space-y-2">
                        <p><strong>Q1: Combien coûte un service de ' . $keyword . ' à [VILLE]?</strong></p>
                        <p>A: Le prix dépend de la complexité et de l\'ampleur des travaux. Nous proposons des devis gratuits et personnalisés.</p>
                        <p><strong>Q2: Quel est le délai d\'intervention pour ' . $keyword . '?</strong></p>
                        <p>A: Nous nous engageons à intervenir rapidement, généralement sous 24-48h selon l\'urgence de votre demande.</p>
                        <p><strong>Q3: Proposez-vous une garantie sur vos services de ' . $keyword . '?</strong></p>
                        <p>A: Oui, tous nos travaux sont garantis selon les normes professionnelles en vigueur.</p>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-green-50 p-6 rounded-lg">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Pourquoi choisir ce service</h3>
                    <p class="leading-relaxed">Notre expertise locale à [VILLE] nous permet de comprendre les spécificités de votre région et d\'adapter nos services en conséquence.</p>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Notre Expertise Locale</h3>
                <p class="leading-relaxed">Depuis plusieurs années, nous intervenons sur [VILLE] et sa région, développant une connaissance approfondie des besoins locaux en ' . $keyword . '.</p>
                <div class="bg-gradient-to-r from-blue-50 to-green-50 p-6 rounded-lg border-l-4 border-blue-600">
                    <h4 class="text-xl font-bold text-gray-900 mb-3">Besoin d\'un devis?</h4>
                    <p class="mb-4">Contactez-nous pour un devis gratuit pour ' . $keyword . ' à [VILLE].</p>
                    <a href="[FORM_URL]" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300">Demande de devis</a>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h4 class="text-lg font-bold text-gray-900 mb-3">Informations Pratiques</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center"><i class="fas fa-check text-green-600 mr-3 flex-shrink-0"></i><span>Devis gratuit et sans engagement</span></li>
                        <li class="flex items-center"><i class="fas fa-check text-green-600 mr-3 flex-shrink-0"></i><span>Intervention rapide sur [VILLE]</span></li>
                        <li class="flex items-center"><i class="fas fa-check text-green-600 mr-3 flex-shrink-0"></i><span>Garantie sur tous nos travaux</span></li>
                    </ul>
                </div>
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="text-center">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Partager ce service</h4>
                        <div class="flex justify-center items-center space-x-4">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=[URL]&quote=[TITRE]" target="_blank" rel="noopener noreferrer" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                <i class="fab fa-facebook-f text-lg"></i>
                                <span class="font-medium">Facebook</span>
                            </a>
                            <a href="https://wa.me/?text=[TITRE] - [URL]" target="_blank" rel="noopener noreferrer" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-full transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                <i class="fab fa-whatsapp text-lg"></i>
                                <span class="font-medium">WhatsApp</span>
                            </a>
                            <a href="mailto:?subject=[TITRE]&body=Je vous partage ce service intéressant : [URL]" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-full transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                <i class="fas fa-envelope text-lg"></i>
                                <span class="font-medium">Email</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        return [
            'description' => $contentHtml,
            'short_description' => 'Service professionnel de ' . $keyword . ' à [VILLE] - Devis gratuit et intervention rapide',
            'long_description' => 'Notre entreprise spécialisée en ' . $keyword . ' intervient sur [VILLE] et dans toute la région de [RÉGION]. Nous proposons des services complets incluant diagnostic, réparation, installation et maintenance. Notre équipe d\'experts maîtrise les techniques les plus modernes pour garantir des résultats durables et performants. Nous nous adaptons aux spécificités climatiques locales et respectons toutes les normes professionnelles en vigueur.',
            'icon' => 'fas fa-tools',
            'meta_title' => $keyword . ' à [VILLE] - Service professionnel',
            'meta_description' => 'Service professionnel de ' . $keyword . ' à [VILLE]. Devis gratuit, intervention rapide, garantie sur tous nos travaux.',
            'meta_keywords' => $keyword . ', ' . $keyword . ' [VILLE], ' . $keyword . ' [RÉGION], expert ' . $keyword . ', ' . $keyword . ' professionnel, entreprise ' . $keyword . ', artisan ' . $keyword . ', ' . $keyword . ' certifié, rénovation, réparation, installation, intervention rapide, devis gratuit, qualité garantie, satisfaction garantie, intervention [VILLE], service [VILLE], professionnel [VILLE]',
            'og_title' => $keyword . ' à [VILLE] - Service professionnel',
            'og_description' => 'Service professionnel de ' . $keyword . ' à [VILLE]. Devis gratuit, intervention rapide, garantie sur tous nos travaux.',
            'twitter_title' => $keyword . ' à [VILLE] - Service professionnel',
            'twitter_description' => 'Service professionnel de ' . $keyword . ' à [VILLE]. Devis gratuit, intervention rapide, garantie sur tous nos travaux.',
        ];
    }

    /**
     * Générer un slug unique pour les annonces
     */
    private function generateUniqueSlug($baseSlug)
    {
        $slug = $baseSlug;
        $counter = 1;
        
        // Vérifier si le slug existe déjà
        while (\App\Models\Ad::where('slug', $slug)->exists()) {
            $suffixes = [
                'devis-gratuit',
                'prix-competitif',
                'service-professionnel',
                'expert-local',
                'qualite-garantie',
                'intervention-rapide',
                'devis-personnalise',
                'travaux-sur-mesure'
            ];
            
            if ($counter <= count($suffixes)) {
                $slug = $baseSlug . '-' . $suffixes[$counter - 1];
            } else {
                $slug = $baseSlug . '-' . $counter;
            }
            
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Obtenir la liste des villes pour la génération d'annonces
     */
    public function getCities()
    {
        $cities = City::select('id', 'name', 'region', 'department')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'cities' => $cities
        ]);
    }

    /**
     * Créer un template à partir d'un mot-clé
     */
    public function createFromKeyword(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|max:255',
            'ai_prompt' => 'nullable|string|max:5000',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $keyword = $request->input('keyword');
        
        // Gérer l'upload de l'image si fournie
        $featuredImagePath = null;
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $fileName = 'template_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Créer le dossier s'il n'existe pas
            $uploadPath = public_path('uploads/templates');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $file->move($uploadPath, $fileName);
            $featuredImagePath = 'uploads/templates/' . $fileName;
        }
        
        try {
            // Vérifier si des templates existent déjà pour ce mot-clé
            $existingTemplates = AdTemplate::where('service_slug', Str::slug($keyword))->get();
            
            if ($existingTemplates->count() > 0 && !$request->input('force_create', false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Des templates existent déjà pour ce mot-clé',
                    'existing_templates' => $existingTemplates->map(function($template) {
                        return [
                            'id' => $template->id,
                            'name' => $template->name,
                            'is_active' => $template->is_active,
                            'ads_count' => $template->ads()->count(),
                            'created_at' => $template->created_at->format('d/m/Y H:i')
                        ];
                    })
                ], 400);
            }

            // Utiliser generateCompleteTemplateContent pour le mot-clé
            $companyInfo = $this->getCompanyInfo();
            $aiContent = $this->generateCompleteTemplateContent(
                $keyword,
                '',
                $companyInfo,
                $request->input('ai_prompt')
            );
            
            // Créer le template
            $template = AdTemplate::create([
                'name' => $keyword,
                'service_name' => $keyword,
                'service_slug' => Str::slug($keyword),
                'content_html' => $aiContent['description'],
                'short_description' => $aiContent['short_description'],
                'long_description' => $aiContent['long_description'],
                'icon' => $aiContent['icon'],
                'featured_image' => $featuredImagePath,
                'meta_title' => $aiContent['meta_title'],
                'meta_description' => $aiContent['meta_description'],
                'meta_keywords' => $aiContent['meta_keywords'],
                'og_title' => $aiContent['og_title'],
                'og_description' => $aiContent['og_description'],
                'twitter_title' => $aiContent['twitter_title'],
                'twitter_description' => $aiContent['twitter_description'],
                'ai_prompt_used' => $request->input('ai_prompt'),
                'ai_response_data' => $aiContent
            ]);

            // Générer automatiquement une annonce pour une ville aléatoire
            $randomCity = null;
            $adCreated = false;
            
            try {
                // Récupérer une ville au hasard
                $randomCity = City::inRandomOrder()->first();
                
                if ($randomCity) {
                    // Vérifier qu'il n'existe pas déjà une annonce pour cette combinaison
                    $existingAd = Ad::where('template_id', $template->id)
                        ->where('city_id', $randomCity->id)
                        ->first();

                    if (!$existingAd) {
                        // Obtenir le contenu et les métadonnées personnalisées pour cette ville
                        $contentForCity = $template->getContentForCity($randomCity);
                        $metaForCity = $template->getMetaForCity($randomCity);

                        // Créer l'annonce avec personnalisation complète
                        Ad::create([
                            'title' => $template->service_name . ' à ' . $randomCity->name,
                            'keyword' => $template->service_name,
                            'city_id' => $randomCity->id,
                'template_id' => $template->id,
                            'slug' => $this->generateUniqueSlug(Str::slug($template->service_name . '-' . $randomCity->name)),
                            'status' => 'published',
                            'published_at' => now(),
                            'meta_title' => $metaForCity['meta_title'],
                            'meta_description' => $metaForCity['meta_description'],
                            'meta_keywords' => $metaForCity['meta_keywords'],
                            'content_html' => $contentForCity,
                            'content_json' => json_encode([
                                'template_id' => $template->id,
                                'city' => $randomCity->toArray(),
                                'generated_at' => now()->toISOString(),
                                'auto_generated' => true
                            ])
                        ]);

                        // Incrémenter le compteur d'utilisation du template
                        $template->incrementUsage();
                        $adCreated = true;
                        
                        Log::info('Annonce auto-générée pour template mot-clé', [
                            'template_id' => $template->id,
                            'city' => $randomCity->name,
                            'keyword' => $keyword
                        ]);
                    }
                }
        } catch (\Exception $e) {
                Log::warning('Impossible de créer automatiquement une annonce pour le template', [
                    'template_id' => $template->id,
                'error' => $e->getMessage()
            ]);
                // On continue même si l'annonce n'a pas pu être créée
            }

            // Message de succès avec information sur la ville
            $message = 'Template créé avec succès pour le mot-clé: ' . $keyword;
            if ($adCreated && $randomCity) {
                $message .= '. Une annonce a été automatiquement générée pour ' . $randomCity->name . '.';
            }

            // Retourner une réponse JSON pour les appels AJAX
                return response()->json([
                    'success' => true,
                'message' => $message,
                    'template_id' => $template->id,
                'ad_created' => $adCreated,
                'city_name' => $randomCity ? $randomCity->name : null,
                'redirect_url' => route('admin.ads.templates.edit', $template->id)
                ]);
                
        } catch (\Exception $e) {
            Log::error('Erreur création template mot-clé', [
                    'keyword' => $keyword,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'success' => false,
                'message' => 'Erreur lors de la création du template via IA: ' . $e->getMessage() . '. Vérifiez vos clés API ChatGPT ou Groq.'
                ], 500);
        }
    }

    /**
     * Supprimer un template
     */
    public function destroy(AdTemplate $template)
    {
        try {
            // Vérifier s'il y a des annonces associées
            $adsCount = $template->ads()->count();
            
            if ($adsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Impossible de supprimer ce template car {$adsCount} annonce(s) y sont associées."
                ], 400);
            }

            $template->delete();

            return response()->json([
                'success' => true,
                'message' => 'Template supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur suppression template', [
                'template_id' => $template->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du template'
            ], 500);
        }
    }

    /**
     * Basculer le statut d'un template
     */
    public function toggleStatus(Request $request, AdTemplate $template)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $template->update([
            'is_active' => $request->input('is_active')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Statut du template mis à jour'
        ]);
    }

    /**
     * Récupérer les informations de l'entreprise
     */
    private function getCompanyInfo()
    {
        return [
            'company_name' => setting('company_name', 'Notre Entreprise'),
            'company_city' => setting('company_city', ''),
            'company_region' => setting('company_region', ''),
            'company_phone' => setting('company_phone', ''),
            'company_email' => setting('company_email', ''),
            'company_address' => setting('company_address', ''),
        ];
    }

    /**
     * Générer un contenu complet de template via IA avec JSON simplifié
     */
    private function generateCompleteTemplateContent($serviceName, $shortDescription, $companyInfo, $aiPrompt = null)
    {
        try {
            $companyName = $companyInfo['company_name'] ?? setting('company_name', 'Notre Entreprise');
            $companyCity = $companyInfo['company_city'] ?? setting('company_city', '');
            $companyDept = $companyInfo['company_region'] ?? setting('company_region', '');
            
            // Récupérer les informations pratiques depuis les settings
            $companyAddress = setting('company_address', '');
            $companyPhone = setting('company_phone', '');
            $companyEmail = setting('company_email', '');
            $companyHours = setting('company_hours', '');
            
            // Template HTML exact fourni par l'utilisateur
            $template = '<div class="grid md:grid-cols-2 gap-8">
  <div class="space-y-6">
    <div class="space-y-4">
      <p class="text-lg leading-relaxed">[description_courte]</p>
      <p class="text-lg leading-relaxed">[description_longue]</p>
    </div>
    <div class="bg-blue-50 p-6 rounded-lg">
      <h3 class="text-xl font-bold text-gray-900 mb-3">[titre_garantie]</h3>
      <p class="leading-relaxed mb-3">[texte_garantie]</p>
    </div>
    <h3 class="text-2xl font-bold text-gray-900 mb-4">Nos Prestations [service]</h3>
    <ul class="space-y-3">[prestations_liste]</ul>
    <div class="bg-gray-50 p-6 rounded-lg mt-6">
      <h4 class="text-xl font-bold text-gray-900 mb-3">FAQ [service]</h4>
      <div class="space-y-2">[faq_liste]</div>
    </div>
  </div>
  <div class="space-y-6">
    <div class="bg-green-50 p-6 rounded-lg">
      <h3 class="text-xl font-bold text-gray-900 mb-3">Pourquoi choisir [service] avec [entreprise]</h3>
      <p class="leading-relaxed">[pourquoi_choisir]</p>
    </div>
    <div class="bg-gradient-to-r from-blue-50 to-green-50 p-6 rounded-lg border-l-4 border-blue-600">
      <h4 class="text-xl font-bold text-gray-900 mb-3">Besoin d\'un devis ?</h4>
      <p class="mb-4">Contactez-nous pour un devis gratuit pour [service].</p>
      <a href="[FORM_URL]" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300">Demande de devis</a>
    </div>
    <div class="bg-gray-50 p-6 rounded-lg">
      <h4 class="text-lg font-bold text-gray-900 mb-3">Informations Pratiques</h4>
      <ul class="space-y-2 text-sm">[infos_pratiques_liste]</ul>
    </div>
    <div class="mt-8 pt-6 border-t border-gray-200">
      <div class="text-center">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Partager ce service</h4>
        <div class="flex justify-center items-center space-x-4">
          <a href="https://www.facebook.com/sharer/sharer.php?u=[URL]&quote=[TITRE]" target="_blank" rel="noopener noreferrer" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
            <i class="fab fa-facebook-f text-lg"></i>
            <span class="font-medium">Facebook</span>
          </a>
          <a href="https://wa.me/?text=[TITRE] - [URL]" target="_blank" rel="noopener noreferrer" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-full transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
            <i class="fab fa-whatsapp text-lg"></i>
            <span class="font-medium">WhatsApp</span>
          </a>
          <a href="mailto:?subject=[TITRE]&body=Je vous partage ce service intéressant : [URL]" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-full transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
            <i class="fas fa-envelope text-lg"></i>
            <span class="font-medium">Email</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>';
            
            // Prompt simplifié pour générer un JSON structuré
            $systemMessage = "Tu es un expert en rédaction web pour services de rénovation/plomberie en France. Tu génères UNIQUEMENT du JSON valide. PAS de texte avant ou après le JSON. PAS de markdown. PAS de code blocks. JUSTE le JSON brut.

⚠️ CRITIQUE : Les valeurs entre [crochets] dans les instructions sont des EXEMPLES/INSTRUCTIONS à suivre, PAS du contenu à copier littéralement. Tu DOIS générer du VRAI contenu professionnel et spécifique, en remplaçant complètement ces instructions par du contenu réel.";
            
            // Construire les infos pratiques pour le prompt
            $infosPratiquesPrompt = "Informations pratiques à utiliser EXACTEMENT (ne pas inventer):\n";
            if ($companyAddress) {
                $infosPratiquesPrompt .= "- Adresse : {$companyAddress}\n";
            }
            if ($companyPhone) {
                $infosPratiquesPrompt .= "- Téléphone : {$companyPhone}\n";
            }
            if ($companyEmail) {
                $infosPratiquesPrompt .= "- Email : {$companyEmail}\n";
            }
            if ($companyHours) {
                $infosPratiquesPrompt .= "- Horaires de travail : {$companyHours}\n";
            }
            if ($companyName) {
                $infosPratiquesPrompt .= "- Société : {$companyName}\n";
            }
            
            // Déterminer les types de prestations selon le service
            $prestationsExamples = '';
            $serviceLower = mb_strtolower($serviceName);
            if (strpos($serviceLower, 'plomberie') !== false || strpos($serviceLower, 'plomberie') !== false) {
                $prestationsExamples = "Exemples pour {$serviceName}: Réparation plomberie, Hydrofuge plomberie, Remplacement tuiles, Zinguerie, Réfection charpente, etc.";
            } elseif (strpos($serviceLower, 'isolation') !== false || strpos($serviceLower, 'isol') !== false) {
                $prestationsExamples = "Exemples pour {$serviceName}: Isolation combles perdus, Isolation plomberie, Isolation murs, Isolation sols, Traitement ponts thermiques, etc.";
            } elseif (strpos($serviceLower, 'façade') !== false || strpos($serviceLower, 'ravalement') !== false) {
                $prestationsExamples = "Exemples pour {$serviceName}: Ravalement façade, Enduit façade, Peinture façade, Nettoyage façade, Réfection parement, etc.";
            } else {
                $prestationsExamples = "Génère 10 prestations techniques spécifiques au {$serviceName} avec le vocabulaire professionnel du métier.";
            }
            
            $userPrompt = ($aiPrompt ? ($aiPrompt . "\n\n") : '') . "Service: {$serviceName}
Description: {$shortDescription}
Entreprise: {$companyName}

⚠️⚠️⚠️ CRITIQUE - C'EST UN TEMPLATE ⚠️⚠️⚠️
- Ceci est un TEMPLATE qui sera utilisé pour créer des annonces pour différentes villes
- TU DOIS utiliser UNIQUEMENT [VILLE] et [DÉPARTEMENT] comme placeholders
- INTERDIT ABSOLU d'utiliser une vraie ville comme Paris, Lyon, Marseille, etc.
- INTERDIT ABSOLU d'utiliser un vrai département comme Paris, Seine-et-Marne, etc.
- Utilise SEULEMENT les placeholders [VILLE] et [DÉPARTEMENT] dans TOUS les textes
- Ces placeholders seront remplacés automatiquement par la vraie ville et département plus tard

{$infosPratiquesPrompt}

⚠️⚠️⚠️ INSTRUCTIONS CRITIQUES - NE PAS COPIER LES EXEMPLES ⚠️⚠️⚠️
Les valeurs JSON ci-dessous sont des EXEMPLES/INSTRUCTIONS. TU DOIS générer du VRAI contenu, PAS copier ces exemples !

Génère un JSON avec cette structure et remplis chaque champ avec du CONTENU RÉEL et PROFESSIONNEL :

{
  \"description_courte\": \"[Génère ici une description courte professionnelle de {$serviceName} à [VILLE] dans le département [DÉPARTEMENT]. 150-200 caractères, mentionnant les bénéfices principaux.]\",
  \"description_longue\": \"[Génère ici une description longue et détaillée du {$serviceName}. Intègre naturellement [VILLE] et [DÉPARTEMENT]. Parle des techniques utilisées, matériaux, bénéfices énergétiques, durabilité, qualité. 400-600 mots.]\",
  \"titre_garantie\": \"[Génère un titre de garantie attractif, ex: 'Garantie décennale et satisfaction' ou 'Nos engagements qualité']\",
  \"texte_garantie\": \"[Génère un texte détaillant les garanties offertes: garantie décennale, assurance, normes respectées, chantier propre, suivi post-intervention, etc.]\",
  \"prestations\": [
    {\"titre\": \"[Prestation technique 1 spécifique au {$serviceName}]\", \"description\": \"[Description détaillée technique et professionnelle]\"},
    {\"titre\": \"[Prestation technique 2 spécifique au {$serviceName}]\", \"description\": \"[Description détaillée technique et professionnelle]\"},
    {\"titre\": \"[Prestation technique 3 spécifique au {$serviceName}]\", \"description\": \"[Description détaillée technique et professionnelle]\"},
    {\"titre\": \"[Prestation technique 4 spécifique au {$serviceName}]\", \"description\": \"[Description détaillée technique et professionnelle]\"},
    {\"titre\": \"[Prestation technique 5 spécifique au {$serviceName}]\", \"description\": \"[Description détaillée technique et professionnelle]\"},
    {\"titre\": \"[Prestation technique 6 spécifique au {$serviceName}]\", \"description\": \"[Description détaillée technique et professionnelle]\"},
    {\"titre\": \"[Prestation technique 7 spécifique au {$serviceName}]\", \"description\": \"[Description détaillée technique et professionnelle]\"},
    {\"titre\": \"[Prestation technique 8 spécifique au {$serviceName}]\", \"description\": \"[Description détaillée technique et professionnelle]\"},
    {\"titre\": \"[Prestation technique 9 spécifique au {$serviceName}]\", \"description\": \"[Description détaillée technique et professionnelle]\"},
    {\"titre\": \"[Prestation technique 10 spécifique au {$serviceName}]\", \"description\": \"[Description détaillée technique et professionnelle]\"}
  ],
  \"faq\": [
    {\"question\": \"[Question fréquente réelle sur {$serviceName}]\", \"reponse\": \"[Réponse détaillée et professionnelle]\"},
    {\"question\": \"[Question fréquente réelle sur {$serviceName}]\", \"reponse\": \"[Réponse détaillée et professionnelle]\"},
    {\"question\": \"[Question fréquente réelle sur {$serviceName}]\", \"reponse\": \"[Réponse détaillée et professionnelle]\"},
    {\"question\": \"[Question fréquente réelle sur {$serviceName}]\", \"reponse\": \"[Réponse détaillée et professionnelle]\"}
  ],
  \"pourquoi_choisir\": \"[Génère un texte détaillant pourquoi choisir {$companyName} pour {$serviceName} à [VILLE] dans le département [DÉPARTEMENT]. Mentionne expertise, qualité, réactivité, garanties, savoir-faire local, etc.]\",
  \"infos_pratiques\": [
    \"[Utilise EXACTEMENT les informations pratiques fournies ci-dessus - ne pas inventer]\"
  ],
  \"meta_title\": \"{$serviceName} à [VILLE] - Expert professionnel | Devis gratuit\",
  \"meta_description\": \"Service professionnel de {$serviceName} à [VILLE] et dans le département [DÉPARTEMENT]. Devis gratuit, intervention rapide.\",
  \"meta_keywords\": \"{$serviceName}, {$serviceName} [VILLE], {$serviceName} [DÉPARTEMENT], expert {$serviceName}, {$serviceName} professionnel, entreprise {$serviceName}, artisan {$serviceName}, {$serviceName} certifié, rénovation, réparation, installation, intervention rapide, devis gratuit, qualité garantie, satisfaction garantie, matériaux performants, techniques modernes, normes professionnelles, intervention [VILLE], service [VILLE], professionnel [VILLE]\",
  \"og_title\": \"{$serviceName} à [VILLE] - Expert professionnel\",
  \"og_description\": \"Service professionnel de {$serviceName} à [VILLE] dans le département [DÉPARTEMENT]. Devis gratuit.\",
  \"twitter_title\": \"{$serviceName} à [VILLE] - Expert professionnel\",
  \"twitter_description\": \"Service professionnel de {$serviceName} à [VILLE] dans le département [DÉPARTEMENT]. Devis gratuit.\"
}

RÈGLES STRICTES:
1. Réponds UNIQUEMENT avec le JSON (commence par { et finit par })
2. PAS de texte avant le {
3. PAS de texte après le }
4. PAS de ```json ou ``` autour
5. ⚠️ CRITIQUE: Les valeurs entre [crochets] ci-dessus sont des INSTRUCTIONS, PAS du contenu à copier. Tu DOIS générer du VRAI contenu professionnel qui remplace ces instructions.
6. Les prestations DOIVENT être techniques et spécifiques au {$serviceName}. {$prestationsExamples}
7. Utilise le vocabulaire professionnel du métier de {$serviceName}
8. ⚠️ CRITIQUE TEMPLATE: Dans TOUS les textes (description_courte, description_longue, meta_title, meta_description, og_title, og_description, twitter_title, twitter_description, meta_keywords, pourquoi_choisir), utilise UNIQUEMENT [VILLE] et [DÉPARTEMENT] comme placeholders. JAMAIS de vraie ville comme Paris, Lyon, Marseille, Bordeaux, etc. JAMAIS de vrai département.
9. Exemple CORRECT: \"Service de {$serviceName} à [VILLE] dans le département [DÉPARTEMENT]\"
10. Exemple INCORRECT (INTERDIT): \"Service de {$serviceName} à Paris dans le département Paris\" ou toute autre ville réelle
11. Pour infos_pratiques, utilise EXACTEMENT les informations fournies ci-dessus (ne pas inventer)
12. Les guillemets dans les valeurs doivent être échappés avec \\
13. Assure-toi que le JSON est valide (vérifie les virgules, les accolades)
14. ⚠️ MOTS-CLÉS: Le champ meta_keywords DOIT contenir AU MINIMUM 15-20 mots-clés pertinents et variés, séparés par des virgules. Inclus:
    - Le nom du service et ses variations (avec et sans [VILLE])
    - Des termes techniques spécifiques au métier (ex: pour plomberie: zinguerie, charpente, étanchéité, isolation, etc.)
    - Des mots-clés d'action (rénovation, réparation, installation, entretien, etc.)
    - Des termes de qualité (professionnel, expert, certifié, qualifié, etc.)
    - Des termes géographiques avec [VILLE] et [DÉPARTEMENT]
    - Des termes commerciaux (devis gratuit, intervention rapide, garantie, etc.)
    - Des matériaux ou techniques spécifiques au service
15. VÉRIFIE avant d'envoyer: tous les textes contiennent [VILLE] et [DÉPARTEMENT], PAS de nom de ville réel
16. ⚠️ INTERDIT ABSOLU de copier les exemples entre [crochets]. Génère du contenu professionnel réel.";
            
            Log::info('Appel à AiService::callAI pour template', [
                'service_name' => $serviceName,
                'prompt_length' => strlen($userPrompt),
                'system_message_length' => strlen($systemMessage)
            ]);
            
            // Calculer max_tokens dynamiquement pour respecter la limite TPM Groq (6000)
            // Estimation: ~1 token = 4 caractères pour le texte
            $totalMessageLength = strlen($systemMessage) + strlen($userPrompt);
            $estimatedInputTokens = (int)($totalMessageLength / 4);
            // Laisser une marge de sécurité: limiter à 5500 tokens totaux
            // Pour un JSON volumineux (10 prestations, 4 FAQ, descriptions), besoin de plus de tokens
            $maxTokens = min(4000, max(2000, 5500 - $estimatedInputTokens));
            
            Log::info('Calcul tokens pour génération template', [
                'estimated_input_tokens' => $estimatedInputTokens,
                'adjusted_max_tokens' => $maxTokens
            ]);
            
            // Utiliser AiService directement (gère automatiquement ChatGPT et Groq)
            $result = \App\Services\AiService::callAI($userPrompt, $systemMessage, [
                'max_tokens' => $maxTokens,
                'temperature' => 0.7,
                'timeout' => 120
            ]);
            
            if (!$result || !isset($result['content'])) {
                Log::error('Échec génération template via AiService', [
                    'service_name' => $serviceName,
                    'result' => $result
                ]);
                throw new \Exception('Erreur API IA: Impossible de générer le contenu. Vérifiez vos clés API ChatGPT ou Groq.');
            }
            
            Log::info('Réponse IA reçue pour template', [
                'service_name' => $serviceName,
                'provider' => $result['provider'] ?? 'unknown',
                'content_length' => strlen($result['content']),
                'content_preview' => substr($result['content'], 0, 300)
            ]);
            
            // Parser le JSON de la réponse IA
            $jsonData = $this->parseJsonResponseForTemplate($result['content']);
            
            if (!$jsonData) {
                // Vérifier si le JSON est tronqué
                $content = $result['content'];
                $jsonStart = strpos($content, '{');
                $isTruncated = false;
                if ($jsonStart !== false) {
                    $potentialJson = substr($content, $jsonStart);
                    $openBraces = substr_count($potentialJson, '{');
                    $closeBraces = substr_count($potentialJson, '}');
                    $isTruncated = $openBraces > $closeBraces;
                }
                
                // Logger le contenu complet pour diagnostic
                Log::error('Impossible de parser le JSON pour le template', [
                                'service_name' => $serviceName,
                    'provider' => $result['provider'] ?? 'unknown',
                    'content_length' => strlen($content),
                    'content_full' => $content, // Contenu complet pour diagnostic
                    'content_preview' => substr($content, 0, 1000),
                    'content_end' => substr($content, -500),
                    'json_error' => json_last_error_msg(),
                    'is_truncated' => $isTruncated,
                    'open_braces' => $openBraces ?? 0,
                    'close_braces' => $closeBraces ?? 0
                ]);
                
                $errorMessage = 'Erreur: L\'IA n\'a pas retourné un JSON valide. ';
                if ($isTruncated) {
                    $errorMessage .= 'La réponse semble tronquée (accolades non fermées). Essayez d\'augmenter max_tokens ou réduisez la taille du prompt. ';
                }
                $errorMessage .= 'Contenu reçu: ' . substr($content, 0, 200) . '... Consultez les logs pour plus de détails.';
                
                throw new \Exception($errorMessage);
            }
            
            // Remplacer toute mention de vraie ville par [VILLE] dans tous les champs texte
            $textFields = ['description_courte', 'description_longue', 'pourquoi_choisir', 
                          'meta_title', 'meta_description', 'meta_keywords', 'og_title', 'og_description', 
                          'twitter_title', 'twitter_description', 'texte_garantie', 'titre_garantie'];
            
            foreach ($textFields as $field) {
                if (isset($jsonData[$field]) && is_string($jsonData[$field])) {
                    // Liste des villes françaises courantes à remplacer
                    $villes = ['Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nice', 'Nantes', 'Strasbourg', 'Montpellier', 'Bordeaux', 'Lille', 'Rennes', 'Reims', 'Le Havre', 'Saint-Étienne', 'Toulon', 'Grenoble', 'Dijon', 'Angers', 'Villeurbanne', 'Saint-Denis', 'Le Mans', 'Aix-en-Provence', 'Clermont-Ferrand', 'Brest', 'Limoges', 'Tours', 'Amiens', 'Perpignan', 'Metz', 'Besançon', 'Boulogne-Billancourt', 'Orléans', 'Mulhouse', 'Caen', 'Rouen', 'Nancy', 'Argenteuil', 'Saint-Denis', 'Montreuil', 'Roubaix', 'Tourcoing', 'Nanterre', 'Avignon', 'Créteil', 'Dunkirk', 'Poitiers', 'Asnières-sur-Seine', 'Versailles', 'Courbevoie', 'Vitry-sur-Seine', 'Colombes', 'Aulnay-sous-Bois', 'La Rochelle', 'Champigny-sur-Marne', 'Rueil-Malmaison', 'Antibes', 'Saint-Maur-des-Fossés', 'Cannes', 'Calais', 'Béziers', 'Drancy', 'Mérignac', 'Saint-Nazaire', 'Colmar', 'Issy-les-Moulineaux', 'Noisy-le-Grand', 'Évry', 'Villeneuve-d\'Ascq', 'Pau', 'Hyères', 'Cergy', 'La Seyne-sur-Mer', 'Pantin', 'Troyes', 'Clichy', 'Antony', 'Montauban', 'Neuilly-sur-Seine', 'Niort', 'Villejuif', 'Lorient', 'Sarcelles', 'Le Blanc-Mesnil', 'Thionville', 'Chambéry', 'Sète', 'Bayonne', 'Bobigny', 'Grasse', 'Châteauroux', 'Vincennes', 'Alès', 'Wattrelos', 'Laval', 'Valence', 'Meaux', 'Brive-la-Gaillarde', 'Épinay-sur-Seine', 'Montrouge', 'Sevran', 'Tarbes', 'Bourges', 'Massy', 'Sainte-Geneviève-des-Bois', 'Saint-Ouen', 'Bègles', 'Garges-lès-Gonesse', 'La Courneuve', 'Martigues', 'Lens', 'Évreux', 'Wittenheim', 'Charleville-Mézières', 'Blois', 'Douai', 'Mantes-la-Jolie', 'Gap', 'L\'Hay-les-Roses', 'Montbéliard', 'Bastia', 'Châteaubriant', 'Mamers', 'Angoulême', 'Thiers', 'Moulins', 'Aubagne', 'Annemasse', 'Annecy', 'Chalon-sur-Saône', 'Châlons-en-Champagne', 'Chaumont', 'Épinal', 'Mâcon', 'Nevers', 'Paray-le-Monial', 'Roanne', 'Sens', 'Tonnerre', 'Vesoul', 'Belfort', 'Montbéliard', 'Mulhouse', 'Altkirch', 'Colmar', 'Haguenau', 'Saverne', 'Sélestat', 'Strasbourg', 'Thann', 'Wissembourg', 'Bar-le-Duc', 'Commercy', 'Ligny-en-Barrois', 'Verdun', 'Bourges', 'Châteauroux', 'Issoudun', 'La Châtre', 'Le Blanc', 'Saint-Amand-Montrond', 'Vierzon', 'Guéret', 'Aubusson', 'Boussac', 'Dinan', 'Guingamp', 'Lannion', 'Loudéac', 'Paimpol', 'Saint-Brieuc', 'Tréguier', 'Ajaccio', 'Bastia', 'Calvi', 'Corte', 'Porto-Vecchio', 'Propriano', 'Sartène', 'Aurillac', 'Mauriac', 'Mauriac', 'Saint-Flour', 'Rodez', 'Espalion', 'Millau', 'Villefranche-de-Rouergue', 'Foix', 'Pamiers', 'Saint-Girons', 'Tarascon-sur-Ariège', 'Privas', 'La Voulte-sur-Rhône', 'Le Cheylard', 'Nyons', 'Aubenas', 'Largentière', 'Tournon-sur-Rhône', 'Valence', 'Montélimar', 'Romans-sur-Isère', 'Die', 'Gap', 'Embrun', 'Briançon', 'Sisteron', 'La Roche-sur-Yon', 'Fontenay-le-Comte', 'Les Sables-d\'Olonne', 'Luçon', 'Roche-sur-Yon', 'Challans', 'Les Herbiers', 'Noirmoutier-en-l\'Île', 'Château-d\'Olonne', 'Olonne-sur-Mer', 'Pouzauges', 'Saint-Gilles-Croix-de-Vie', 'Aix-en-Provence', 'Arles', 'Avignon', 'Carpentras', 'Cavaillon', 'Orange', 'Pertuis', 'Sault', 'Valréas', 'Béziers', 'Cahors', 'Figeac', 'Gourdon', 'Martel', 'Rocamadour', 'Saint-Céré', 'Souillac', 'Villefranche-de-Rouergue', 'Agen', 'Fumel', 'Marmande', 'Nérac', 'Tonneins', 'Villeneuve-sur-Lot', 'Auch', 'Condom', 'Lectoure', 'Mirande', 'Nogaro', 'Valence-sur-Baïse', 'Vic-Fezensac', 'Castelsarrasin', 'Lavardac', 'Moissac', 'Montauban', 'Villefranche-de-Rouergue', 'Albi', 'Castres', 'Gaillac', 'Lavaur', 'Mazamet', 'Puylaurens', 'Revel', 'Saint-Sulpice', 'Bourg-en-Bresse', 'Belley', 'Bourg-Saint-Christophe', 'Châtillon-sur-Chalaronne', 'Gex', 'Nantua', 'Oyonnax', 'Péronnas', 'Pont-d\'Ain', 'Saint-Genis-Pouilly', 'Thoissey', 'Trévoux', 'Dijon', 'Arnay-le-Duc', 'Auxonne', 'Beaune', 'Châtillon-sur-Seine', 'Châtillon-sur-Seine', 'Is-sur-Tille', 'Montbard', 'Nuits-Saint-Georges', 'Semur-en-Auxois', 'Seurre', 'La Roche-sur-Yon', 'Fontenay-le-Comte', 'Les Sables-d\'Olonne', 'Luçon', 'Challans', 'Les Herbiers', 'Noirmoutier-en-l\'Île', 'Château-d\'Olonne', 'Olonne-sur-Mer', 'Pouzauges', 'Saint-Gilles-Croix-de-Vie'];
                    
                    // Remplacer toute ville trouvée par [VILLE]
                    foreach ($villes as $ville) {
                        $jsonData[$field] = preg_replace('/\b' . preg_quote($ville, '/') . '\b/i', '[VILLE]', $jsonData[$field]);
                    }
                    
                    // Remplacer aussi les patterns comme "ville de X" ou "à X"
                    $jsonData[$field] = preg_replace('/\b(ville de|à|dans|sur) [A-Z][a-zéèêëàâäïîôöùûüç]+/', '$1 [VILLE]', $jsonData[$field]);
                    $jsonData[$field] = preg_replace('/\b(ville de|à|dans|sur) [A-Z][a-zéèêëàâäïîôöùûüç]+(-[A-Z][a-zéèêëàâäïîôöùûüç]+)?/', '$1 [VILLE]', $jsonData[$field]);
                    
                    // Remplacer les départements courants
                    $departements = ['Paris', 'Seine-et-Marne', 'Yvelines', 'Essonne', 'Hauts-de-Seine', 'Seine-Saint-Denis', 'Val-de-Marne', 'Val-d\'Oise', 'Loire-Atlantique', 'Maine-et-Loire', 'Mayenne', 'Sarthe', 'Vendée', 'Côtes-d\'Armor', 'Finistère', 'Ille-et-Vilaine', 'Morbihan', 'Calvados', 'Eure', 'Manche', 'Orne', 'Seine-Maritime', 'Ain', 'Aisne', 'Allier', 'Alpes-de-Haute-Provence', 'Hautes-Alpes', 'Alpes-Maritimes', 'Ardèche', 'Ardennes', 'Ariège', 'Aube', 'Aude', 'Aveyron', 'Bouches-du-Rhône', 'Calvados', 'Cantal', 'Charente', 'Charente-Maritime', 'Cher', 'Corrèze', 'Corse-du-Sud', 'Haute-Corse', 'Côte-d\'Or', 'Côtes-d\'Armor', 'Creuse', 'Dordogne', 'Doubs', 'Drôme', 'Eure', 'Eure-et-Loir', 'Finistère', 'Gard', 'Haute-Garonne', 'Gers', 'Gironde', 'Hérault', 'Ille-et-Vilaine', 'Indre', 'Indre-et-Loire', 'Isère', 'Jura', 'Landes', 'Loir-et-Cher', 'Loire', 'Haute-Loire', 'Loire-Atlantique', 'Loiret', 'Lot', 'Lot-et-Garonne', 'Lozère', 'Maine-et-Loire', 'Manche', 'Marne', 'Haute-Marne', 'Mayenne', 'Meurthe-et-Moselle', 'Meuse', 'Morbihan', 'Moselle', 'Nièvre', 'Nord', 'Oise', 'Orne', 'Pas-de-Calais', 'Puy-de-Dôme', 'Pyrénées-Atlantiques', 'Hautes-Pyrénées', 'Pyrénées-Orientales', 'Bas-Rhin', 'Haut-Rhin', 'Rhône', 'Haute-Saône', 'Saône-et-Loire', 'Sarthe', 'Savoie', 'Haute-Savoie', 'Paris', 'Seine-Maritime', 'Seine-et-Marne', 'Yvelines', 'Deux-Sèvres', 'Somme', 'Tarn', 'Tarn-et-Garonne', 'Var', 'Vaucluse', 'Vendée', 'Vienne', 'Haute-Vienne', 'Vosges', 'Yonne', 'Territoire de Belfort', 'Essonne', 'Hauts-de-Seine', 'Seine-Saint-Denis', 'Val-de-Marne', 'Val-d\'Oise'];
                    
                    foreach ($departements as $dept) {
                        $jsonData[$field] = preg_replace('/\b(département|département de|dans le département|du département) ' . preg_quote($dept, '/') . '\b/i', '$1 [DÉPARTEMENT]', $jsonData[$field]);
                        $jsonData[$field] = preg_replace('/\b' . preg_quote($dept, '/') . '\b/i', '[DÉPARTEMENT]', $jsonData[$field]);
                    }
                }
            }
            
            // Vérifier aussi dans les prestations et FAQ
            if (isset($jsonData['prestations']) && is_array($jsonData['prestations'])) {
                foreach ($jsonData['prestations'] as $key => $prestation) {
                    foreach (['titre', 'description'] as $subField) {
                        if (isset($prestation[$subField])) {
                            foreach ($villes as $ville) {
                                $jsonData['prestations'][$key][$subField] = preg_replace('/\b' . preg_quote($ville, '/') . '\b/i', '[VILLE]', $jsonData['prestations'][$key][$subField]);
                            }
                        }
                    }
                }
            }
            
            if (isset($jsonData['faq']) && is_array($jsonData['faq'])) {
                foreach ($jsonData['faq'] as $key => $faq) {
                    foreach (['question', 'reponse'] as $subField) {
                        if (isset($faq[$subField])) {
                            foreach ($villes as $ville) {
                                $jsonData['faq'][$key][$subField] = preg_replace('/\b' . preg_quote($ville, '/') . '\b/i', '[VILLE]', $jsonData['faq'][$key][$subField]);
                            }
                        }
                    }
                }
            }
            
            if (isset($jsonData['infos_pratiques']) && is_array($jsonData['infos_pratiques'])) {
                foreach ($jsonData['infos_pratiques'] as $key => $info) {
                    if (is_string($info)) {
                        foreach ($villes as $ville) {
                            $jsonData['infos_pratiques'][$key] = preg_replace('/\b' . preg_quote($ville, '/') . '\b/i', '[VILLE]', $jsonData['infos_pratiques'][$key]);
                        }
                    }
                }
            }
            
            Log::info('Placeholders [VILLE] et [DÉPARTEMENT] vérifiés et corrigés dans le JSON', [
                'service_name' => $serviceName
            ]);
            
            // Remplir le template HTML avec les données JSON
            $htmlContent = $this->fillTemplateForAds($template, $jsonData, $serviceName, $companyName, $companyInfo);
            
            if (!$htmlContent) {
                throw new \Exception('Erreur: Impossible de remplir le template HTML.');
            }
            
            // Retourner les données formatées pour le template (avec placeholders [VILLE] et [DÉPARTEMENT])
            return [
                'description' => $htmlContent,
                'short_description' => $jsonData['description_courte'] ?? $shortDescription,
                'long_description' => $jsonData['description_longue'] ?? '',
                'icon' => 'fas fa-tools',
                'meta_title' => $jsonData['meta_title'] ?? ($serviceName . ' à [VILLE] - Expert professionnel | Devis gratuit'),
                'meta_description' => $jsonData['meta_description'] ?? ('Service professionnel de ' . $serviceName . ' à [VILLE] et dans le département [DÉPARTEMENT]. Devis gratuit, intervention rapide.'),
                'og_title' => $jsonData['og_title'] ?? ($serviceName . ' à [VILLE] - Expert professionnel'),
                'og_description' => $jsonData['og_description'] ?? ('Service professionnel de ' . $serviceName . ' à [VILLE] dans le département [DÉPARTEMENT]. Devis gratuit.'),
                'twitter_title' => $jsonData['twitter_title'] ?? ($serviceName . ' à [VILLE] - Expert professionnel'),
                'twitter_description' => $jsonData['twitter_description'] ?? ('Service professionnel de ' . $serviceName . ' à [VILLE] dans le département [DÉPARTEMENT]. Devis gratuit.'),
                'meta_keywords' => $jsonData['meta_keywords'] ?? ($serviceName . ', ' . $serviceName . ' [VILLE], ' . $serviceName . ' [DÉPARTEMENT], expert ' . $serviceName . ', ' . $serviceName . ' professionnel, entreprise ' . $serviceName . ', artisan ' . $serviceName . ', ' . $serviceName . ' certifié, rénovation, réparation, installation, intervention rapide, devis gratuit, qualité garantie, satisfaction garantie, intervention [VILLE], service [VILLE], professionnel [VILLE]')
            ];
        } catch (\Exception $e) {
            Log::error('Erreur génération template: ' . $e->getMessage(), [
                    'service_name' => $serviceName,
                'error' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Parser le JSON de la réponse IA (robuste)
     */
    private function parseJsonResponseForTemplate($content)
    {
        $content = trim($content);
        
        Log::info('Tentative de parsing JSON pour template', [
            'content_length' => strlen($content),
            'content_preview' => substr($content, 0, 300),
            'has_braces' => strpos($content, '{') !== false,
            'last_chars' => substr($content, -50) // Voir comment ça se termine
        ]);
        
        // Vérifier si le JSON semble tronqué (ne se termine pas par })
        $jsonStart = strpos($content, '{');
        if ($jsonStart !== false) {
            $potentialJson = substr($content, $jsonStart);
            $openBraces = substr_count($potentialJson, '{');
            $closeBraces = substr_count($potentialJson, '}');
            
            if ($openBraces > $closeBraces) {
                Log::warning('JSON potentiellement tronqué (accolades non fermées)', [
                    'open_braces' => $openBraces,
                    'close_braces' => $closeBraces,
                    'last_200_chars' => substr($content, -200)
                ]);
            }
        }
        
        // Essayer plusieurs patterns pour extraire le JSON
        $jsonPatterns = [
            '/```json\s*(\{[\s\S]*?\})\s*```/s',  // JSON dans code block avec json
            '/```\s*(\{[\s\S]*?\})\s*```/s',      // JSON dans code block sans json
            '/\{[\s\S]*\"description_courte\"[\s\S]*\}/s',  // JSON contenant description_courte
            '/\{[\s\S]*\}/s',                      // N'importe quel JSON
        ];
        
        foreach ($jsonPatterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                $jsonString = $matches[1] ?? $matches[0];
                $jsonString = trim($jsonString);
                
                Log::info('Pattern JSON trouvé', [
                    'pattern_matched' => true,
                    'json_length' => strlen($jsonString),
                    'json_preview' => substr($jsonString, 0, 200),
                    'json_end' => substr($jsonString, -100) // Voir la fin
                ]);
                
                $data = json_decode($jsonString, true);
                
                if ($data && is_array($data) && !empty($data)) {
                    Log::info('JSON parsé avec succès pour template', [
                        'keys' => array_keys($data)
                    ]);
                    return $data;
                } else {
                    $jsonError = json_last_error();
                    Log::warning('JSON invalide après pattern match', [
                        'error' => json_last_error_msg(),
                        'error_code' => $jsonError,
                        'json_preview' => substr($jsonString, 0, 500),
                        'json_end' => substr($jsonString, -200),
                        'is_truncated' => $jsonError === JSON_ERROR_SYNTAX && !str_ends_with($jsonString, '}')
                    ]);
                    
                    // Si le JSON est tronqué, essayer de le compléter
                    if ($jsonError === JSON_ERROR_SYNTAX && !str_ends_with($jsonString, '}')) {
                        // Compter les accolades ouvertes/fermées
                        $openCount = substr_count($jsonString, '{');
                        $closeCount = substr_count($jsonString, '}');
                        $missingBraces = $openCount - $closeCount;
                        
                        // Essayer de fermer les accolades manquantes
                        if ($missingBraces > 0) {
                            $attemptedFix = $jsonString . str_repeat('}', $missingBraces);
                            $fixedData = json_decode($attemptedFix, true);
                            if ($fixedData && is_array($fixedData)) {
                                Log::info('JSON réparé en fermant les accolades manquantes', [
                                    'missing_braces' => $missingBraces
                                ]);
                                return $fixedData;
                            }
                        }
                    }
                }
            }
        }
        
        // Si aucun pattern ne fonctionne, essayer de trouver le JSON manuellement
        $jsonStart = strpos($content, '{');
        $jsonEnd = strrpos($content, '}');
        
        if ($jsonStart !== false && $jsonEnd !== false && $jsonEnd > $jsonStart) {
            $jsonString = substr($content, $jsonStart, $jsonEnd - $jsonStart + 1);
            $data = json_decode($jsonString, true);
            
            if ($data && is_array($data) && !empty($data)) {
                Log::info('JSON parsé avec extraction manuelle');
                return $data;
            }
        }
        
        // Dernière tentative : décoder directement
        $data = json_decode($content, true);
        if ($data && is_array($data) && !empty($data)) {
            Log::info('JSON parsé directement');
            return $data;
        }
        
        // Détecter si le problème est un JSON tronqué
        $isTruncated = false;
        if ($jsonStart !== false) {
            $potentialJson = substr($content, $jsonStart);
            $openBraces = substr_count($potentialJson, '{');
            $closeBraces = substr_count($potentialJson, '}');
            $isTruncated = $openBraces > $closeBraces;
        }
        
        Log::error('Impossible de parser le JSON pour template', [
            'content_preview' => substr($content, 0, 1000),
            'content_end' => substr($content, -500),
            'json_error' => json_last_error_msg(),
            'is_truncated' => $isTruncated,
            'open_braces' => $openBraces ?? 0,
            'close_braces' => $closeBraces ?? 0
        ]);
        
        return null;
    }
    
    /**
     * Remplir le template HTML avec les données JSON
     */
    private function fillTemplateForAds($template, $data, $serviceName, $companyName, $companyInfo)
    {
        $siteUrl = setting('site_url', config('app.url'));
        if (!str_starts_with($siteUrl, 'http')) {
            $siteUrl = 'https://' . $siteUrl;
        }
        $serviceUrl = $siteUrl . '/services/' . \Illuminate\Support\Str::slug($serviceName);
        $formUrl = setting('contact_form_url', '/contact');
        
        // Générer la liste des prestations
        $prestationsHtml = '';
        if (isset($data['prestations']) && is_array($data['prestations'])) {
            foreach ($data['prestations'] as $prestation) {
                $titre = htmlspecialchars($prestation['titre'] ?? '', ENT_QUOTES, 'UTF-8');
                $description = htmlspecialchars($prestation['description'] ?? '', ENT_QUOTES, 'UTF-8');
                $prestationsHtml .= '<li class="flex items-start">' .
                    '<i class="fas fa-check text-green-600 mr-3 mt-1 flex-shrink-0"></i>' .
                    '<span><strong>' . $titre . '</strong> - ' . $description . '</span>' .
                    '</li>';
            }
        }
        
        // Générer la liste FAQ
        $faqHtml = '';
        if (isset($data['faq']) && is_array($data['faq'])) {
            foreach ($data['faq'] as $faq) {
                $question = htmlspecialchars($faq['question'] ?? '', ENT_QUOTES, 'UTF-8');
                $reponse = htmlspecialchars($faq['reponse'] ?? '', ENT_QUOTES, 'UTF-8');
                $faqHtml .= '<p><strong>' . $question . '</strong></p>' .
                    '<p>' . $reponse . '</p>';
            }
        }
        
        // Générer la liste des infos pratiques
        $infosPratiquesHtml = '';
        if (isset($data['infos_pratiques']) && is_array($data['infos_pratiques'])) {
            foreach ($data['infos_pratiques'] as $info) {
                // Vérifier que $info est une chaîne (peut être un tableau si JSON mal formaté)
                if (is_array($info)) {
                    // Si c'est un tableau, essayer de le convertir en chaîne
                    $info = is_string($info[0] ?? null) ? $info[0] : json_encode($info);
                }
                if (!is_string($info)) {
                    $info = (string)$info;
                }
                $infoEscaped = htmlspecialchars($info, ENT_QUOTES, 'UTF-8');
                $infosPratiquesHtml .= '<li class="flex items-center">' .
                    '<i class="fas fa-check text-green-600 mr-3 flex-shrink-0"></i>' .
                    '<span>' . $infoEscaped . '</span>' .
                    '</li>';
            }
        }
        
        // Fonction helper pour convertir en string et échapper
        $escape = function($value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            if (!is_string($value)) {
                $value = (string)$value;
            }
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        };
        
        // Remplacer tous les placeholders dans le template
        $html = str_replace('[description_courte]', $escape($data['description_courte'] ?? ''), $template);
        $html = str_replace('[description_longue]', $escape($data['description_longue'] ?? ''), $html);
        $html = str_replace('[titre_garantie]', $escape($data['titre_garantie'] ?? 'Garantie de satisfaction'), $html);
        $html = str_replace('[texte_garantie]', $escape($data['texte_garantie'] ?? ''), $html);
        $html = str_replace('[prestations_liste]', $prestationsHtml, $html);
        $html = str_replace('[faq_liste]', $faqHtml, $html);
        $html = str_replace('[service]', $escape($serviceName), $html);
        $html = str_replace('[entreprise]', $escape($companyName), $html);
        $html = str_replace('[pourquoi_choisir]', $escape($data['pourquoi_choisir'] ?? ''), $html);
        $html = str_replace('[infos_pratiques_liste]', $infosPratiquesHtml, $html);
        $html = str_replace('[URL]', $escape($serviceUrl), $html);
        $html = str_replace('[TITRE]', $escape($serviceName), $html);
        $html = str_replace('[FORM_URL]', $escape($formUrl), $html);
        
        return $html;
    }

    /**
     * Construire le prompt pour un template de service
     */
    private function buildTemplatePromptForService($serviceName, $shortDescription, $companyInfo, $aiPrompt = null)
    {
        $basePrompt = "Tu es un expert technique en {$serviceName} avec une connaissance PROFONDE des prestations, techniques et matériaux spécifiques à ce domaine. Crée un template d'annonce TOTALEMENT personnalisé pour {$serviceName}.

⚠️⚠️⚠️ SERVICE À PERSONNALISER: {$serviceName} ⚠️⚠️⚠️

IMPORTANT: Ce template sera utilisé pour créer des annonces personnalisées par ville. Utilise les placeholders suivants:
- [VILLE] = sera remplacé par le nom de la ville
- [RÉGION] = sera remplacé par le nom de la région
- [DÉPARTEMENT] = sera remplacé par le nom du département
- [FORM_URL] = URL du formulaire de devis
- [URL] = URL de l'annonce finale
- [TITRE] = Titre de l'annonce avec ville

🚫🚫🚫 INTERDICTIONS ABSOLUES - PRESTATIONS GÉNÉRIQUES INTERDITES 🚫🚫🚫:
- INTERDIT ABSOLU: 'Diagnostic et évaluation', 'Intervention d'urgence', 'Maintenance préventive', 'Réparation spécialisée', 'Installation complète', 'Rénovation totale', 'Conseil personnalisé', 'Suivi post-intervention', 'Formation utilisateur', 'Garantie étendue'
- INTERDIT: Toute prestation qui pourrait s'appliquer à n'importe quel autre service (ex: 'Réparation et maintenance', 'Installation professionnelle', 'Conseils personnalisés')
- INTERDIT: Copier du contenu générique applicable à tous les services
- INTERDIT: Utiliser un vocabulaire vague ou général

✅✅✅ OBLIGATIONS ABSOLUES POUR {$serviceName} ✅✅✅:
- Chaque prestation DOIT avoir un NOM TECHNIQUE précis du domaine de {$serviceName} (ex: si {$serviceName} = 'Rénovation de façade' → 'Ravalement façade', 'Enduit façade', 'Peinture façade', 'Rénovation parement pierre', etc.)
- Chaque prestation DOIT expliquer la MÉTHODE et la TECHNIQUE utilisée spécifiquement pour {$serviceName}
- Les prestations doivent mentionner des techniques, matériaux ou méthodes PRÉCISES liés uniquement à {$serviceName}
- Chaque description doit expliquer QUOI (technique précise), COMMENT (méthode), POURQUOI (bénéfice spécifique pour {$serviceName})
- Utilise le vocabulaire PROFESSIONNEL du métier de {$serviceName} (ex: pour façade: ravalement, enduit, parement, bardage, crépis, etc.)
- Utilise [VILLE] et [RÉGION] dans le contenu pour personnalisation future

EXEMPLES CONCRETS SELON LE SERVICE:
";
        
        // Ajouter des exemples spécifiques selon le type de service
        $serviceNameLower = mb_strtolower($serviceName);
        if (strpos($serviceNameLower, 'façade') !== false || strpos($serviceNameLower, 'ravalement') !== false) {
            $basePrompt .= "- Si {$serviceName} = 'Rénovation de façade' → EXCELLENT: 'Ravalement façade complet', 'Réfection enduit façade', 'Peinture façade haute qualité', 'Rénovation parement pierre', 'Pose bardage façade', 'Crépis façade décoratif', 'Nettoyage façade haute pression', 'Isolation façade par l'extérieur (ITE)', 'Remplacement volets et menuiseries', 'Restauration éléments décoratifs façade'\n";
            $basePrompt .= "- MAUVAIS (INTERDIT): 'Diagnostic et inspection de plomberie', 'Réparation partielle de plomberie', 'Réfection complète de plomberie', 'Isolation de plomberie' (ce sont des prestations de PLOMBERIE, pas de FAÇADE)\n";
        } elseif (strpos($serviceNameLower, 'désamiantage') !== false || strpos($serviceNameLower, 'amiante') !== false) {
            $basePrompt .= "- Si {$serviceName} = 'Désamiantage' → EXCELLENT: 'Dépollution amiante', 'Diagnostic amiante avant travaux', 'Retrait amiante sous confinement', 'Gestion déchets amiante', 'Désamiantage flocage', 'Confinement amiante'\n";
            $basePrompt .= "- MAUVAIS (INTERDIT): 'Diagnostic et évaluation', 'Installation complète', 'Rénovation totale'\n";
        } elseif (strpos($serviceNameLower, 'élagage') !== false || strpos($serviceNameLower, 'élague') !== false) {
            $basePrompt .= "- Si {$serviceName} = 'Élagage' → EXCELLENT: 'Élagage raisonné', 'Taille de formation', 'Haubanage', 'Abattage sécurisé', 'Élagage fruitier', 'Taille ornementale', 'Démontage sécurisé', 'Rogne de souche', 'Élagage respectueux de la faune', 'Mise en sécurité arbres'\n";
            $basePrompt .= "- MAUVAIS (INTERDIT): 'Diagnostic et évaluation', 'Intervention d'urgence', 'Maintenance préventive'\n";
        } elseif (strpos($serviceNameLower, 'humidité') !== false || strpos($serviceNameLower, 'ventilation') !== false) {
            $basePrompt .= "- Si {$serviceName} = 'Traitement humidité' → EXCELLENT: 'Diagnostic humidité par imagerie thermique', 'Injection résine anti-humidité', 'Installation VMC double flux', 'Traitement remontées capillaires', 'Assèchement murs humides'\n";
            $basePrompt .= "- MAUVAIS (INTERDIT): 'Diagnostic et évaluation', 'Conseil personnalisé', 'Maintenance préventive'\n";
        } else {
            $basePrompt .= "- Pour {$serviceName}, RECHERCHE les termes techniques professionnels spécifiques à ce métier\n";
            $basePrompt .= "- Utilise le vocabulaire RÉEL du métier de {$serviceName} (ex: pour {$serviceName}, quels sont les termes techniques utilisés par les professionnels?)\n";
            $basePrompt .= "- Évite TOUT ce qui pourrait s'appliquer à n'importe quel autre service\n";
        }
        
        $basePrompt .= "
📋 STRUCTURE HTML OBLIGATOIRE DU CHAMP \"description\":

⚠️⚠️⚠️ IMPORTANT: REMPLACEZ TOUS LES PLACEHOLDERS [Paragraphe X] PAR DU VRAI CONTENU PERSONNALISÉ ⚠️⚠️⚠️

Le champ \"description\" DOIT contenir un HTML COMPLET avec cette structure exacte:

<div class=\"grid md:grid-cols-2 gap-8\">
  <div class=\"space-y-6\">
    <!-- SECTION 1: DESCRIPTION LONGUE PERSONNALISÉE -->
    <div class=\"space-y-4\">
      <p class=\"text-lg leading-relaxed\">ÉCRIRE ICI un paragraphe de 5-6 phrases sur {$serviceName} à [VILLE], mentionnant des techniques spécifiques, matériaux, normes (ex: RT 2012, DTU), certifications (ex: RGE, Qualit'ENR), et l'impact pour les clients locaux. NE PAS utiliser de texte générique.</p>
      <p class=\"text-lg leading-relaxed\">ÉCRIRE ICI un deuxième paragraphe avec des détails techniques précis sur {$serviceName}: types de matériaux utilisés (ex: laine de verre, polyuréthane, fibre de bois), méthodes de pose, épaisseurs, résistances thermiques, normes respectées. SOYEZ TRÈS SPÉCIFIQUE.</p>
      <p class=\"text-lg leading-relaxed\">ÉCRIRE ICI un troisième paragraphe expliquant pourquoi notre entreprise est experte en {$serviceName} dans [RÉGION]: nombre d'années d'expérience, nombre de chantiers réalisés, types de projets (particuliers, professionnels, collectivités), certifications obtenues, garanties proposées.</p>
    </div>
    
    <!-- SECTION 2: NOTRE ENGAGEMENT QUALITÉ (PERSONNALISÉ) -->
    <div class=\"bg-blue-50 p-6 rounded-lg border-l-4 border-blue-600\">
      <h3 class=\"text-xl font-bold text-gray-900 mb-3\">Notre Engagement Qualité pour {$serviceName}</h3>
      <p class=\"leading-relaxed mb-3\">ÉCRIRE ICI un paragraphe spécifique: pour {$serviceName}, nous garantissons [MENTIONNER DES GARANTIES SPÉCIFIQUES: ex: garantie décennale, garantie biennale, garantie de résultat d'économie d'énergie, etc.]. Nos équipes sont certifiées [MENTIONNER CERTIFICATIONS SPÉCIFIQUES au domaine {$serviceName}].</p>
      <p class=\"leading-relaxed mb-3\">ÉCRIRE ICI comment nous garantissons la qualité: pour chaque intervention de {$serviceName}, nous réalisons [MENTIONNER DES PROCESSUS SPÉCIFIQUES: ex: diagnostic thermique avant travaux, contrôle qualité en cours de chantier, test d'étanchéité à l'air, mesure de performance, etc.]. Un suivi post-intervention est assuré avec [PRÉCISER: ex: visite de contrôle, mesure de performance, assistance SAV].</p>
      <p class=\"leading-relaxed\">ÉCRIRE ICI notre engagement client: à [VILLE] et dans [RÉGION], nous nous engageons à [MENTIONNER DES ENGAGEMENTS SPÉCIFIQUES: ex: intervenir sous 48h en urgence, devis sous 24h, respect des délais annoncés, nettoyage complet après intervention, etc.].</p>
    </div>
    
    <!-- SECTION 3: 10 PRESTATIONS SPÉCIFIQUES -->
    <h3 class=\"text-2xl font-bold text-gray-900 mb-4\">Nos Prestations {$serviceName}</h3>
    <ul class=\"space-y-4\">
      <li class=\"flex items-start\">
        <i class=\"fas fa-check-circle text-green-600 mr-3 mt-1 flex-shrink-0\"></i>
        <div>
          <strong class=\"text-gray-900 block mb-1\">[NOM PRESTATION 1 TECHNIQUE SPÉCIFIQUE À {$serviceName} - DOIT être un terme technique du métier, pas générique]</strong>
          <p class=\"text-gray-700 text-sm\">[Description détaillée avec techniques, matériaux, méthodes précises pour cette prestation spécifique à {$serviceName}. Minimum 2-3 phrases. Explicite QUOI (technique précise), COMMENT (méthode), POURQUOI (bénéfice pour {$serviceName}).]</p>
        </div>
      </li>
      <li class=\"flex items-start\">
        <i class=\"fas fa-check-circle text-green-600 mr-3 mt-1 flex-shrink-0\"></i>
        <div>
          <strong class=\"text-gray-900 block mb-1\">[NOM PRESTATION 2 TECHNIQUE SPÉCIFIQUE À {$serviceName}]</strong>
          <p class=\"text-gray-700 text-sm\">[Description détaillée pour cette prestation spécifique à {$serviceName}]</p>
        </div>
      </li>
      <li class=\"flex items-start\">
        <i class=\"fas fa-check-circle text-green-600 mr-3 mt-1 flex-shrink-0\"></i>
        <div>
          <strong class=\"text-gray-900 block mb-1\">[NOM PRESTATION 3 TECHNIQUE SPÉCIFIQUE À {$serviceName}]</strong>
          <p class=\"text-gray-700 text-sm\">[Description détaillée pour cette prestation spécifique à {$serviceName}]</p>
        </div>
      </li>
      <li class=\"flex items-start\">
        <i class=\"fas fa-check-circle text-green-600 mr-3 mt-1 flex-shrink-0\"></i>
        <div>
          <strong class=\"text-gray-900 block mb-1\">[NOM PRESTATION 4 TECHNIQUE SPÉCIFIQUE À {$serviceName}]</strong>
          <p class=\"text-gray-700 text-sm\">[Description détaillée pour cette prestation spécifique à {$serviceName}]</p>
        </div>
      </li>
      <li class=\"flex items-start\">
        <i class=\"fas fa-check-circle text-green-600 mr-3 mt-1 flex-shrink-0\"></i>
        <div>
          <strong class=\"text-gray-900 block mb-1\">[NOM PRESTATION 5 TECHNIQUE SPÉCIFIQUE À {$serviceName}]</strong>
          <p class=\"text-gray-700 text-sm\">[Description détaillée pour cette prestation spécifique à {$serviceName}]</p>
        </div>
      </li>
      <li class=\"flex items-start\">
        <i class=\"fas fa-check-circle text-green-600 mr-3 mt-1 flex-shrink-0\"></i>
        <div>
          <strong class=\"text-gray-900 block mb-1\">[NOM PRESTATION 6 TECHNIQUE SPÉCIFIQUE À {$serviceName}]</strong>
          <p class=\"text-gray-700 text-sm\">[Description détaillée pour cette prestation spécifique à {$serviceName}]</p>
        </div>
      </li>
      <li class=\"flex items-start\">
        <i class=\"fas fa-check-circle text-green-600 mr-3 mt-1 flex-shrink-0\"></i>
        <div>
          <strong class=\"text-gray-900 block mb-1\">[NOM PRESTATION 7 TECHNIQUE SPÉCIFIQUE À {$serviceName}]</strong>
          <p class=\"text-gray-700 text-sm\">[Description détaillée pour cette prestation spécifique à {$serviceName}]</p>
        </div>
      </li>
      <li class=\"flex items-start\">
        <i class=\"fas fa-check-circle text-green-600 mr-3 mt-1 flex-shrink-0\"></i>
        <div>
          <strong class=\"text-gray-900 block mb-1\">[NOM PRESTATION 8 TECHNIQUE SPÉCIFIQUE À {$serviceName}]</strong>
          <p class=\"text-gray-700 text-sm\">[Description détaillée pour cette prestation spécifique à {$serviceName}]</p>
        </div>
      </li>
      <li class=\"flex items-start\">
        <i class=\"fas fa-check-circle text-green-600 mr-3 mt-1 flex-shrink-0\"></i>
        <div>
          <strong class=\"text-gray-900 block mb-1\">[NOM PRESTATION 9 TECHNIQUE SPÉCIFIQUE À {$serviceName}]</strong>
          <p class=\"text-gray-700 text-sm\">[Description détaillée pour cette prestation spécifique à {$serviceName}]</p>
        </div>
      </li>
      <li class=\"flex items-start\">
        <i class=\"fas fa-check-circle text-green-600 mr-3 mt-1 flex-shrink-0\"></i>
        <div>
          <strong class=\"text-gray-900 block mb-1\">[NOM PRESTATION 10 TECHNIQUE SPÉCIFIQUE À {$serviceName}]</strong>
          <p class=\"text-gray-700 text-sm\">[Description détaillée pour cette prestation spécifique à {$serviceName}]</p>
        </div>
      </li>
    </ul>
    
    <!-- SECTION 4: FAQ PERSONNALISÉE (MINIMUM 6 QUESTIONS) -->
    <div class=\"bg-gray-50 p-6 rounded-lg mt-6\">
      <h4 class=\"text-xl font-bold text-gray-900 mb-4\">Questions Fréquentes sur {$serviceName} à [VILLE]</h4>
      <div class=\"space-y-4\">
        <div>
          <p class=\"font-semibold text-gray-900 mb-2\"><strong>Q1:</strong> [Question spécifique sur {$serviceName} à [VILLE] - coûts, délais, ou processus technique]</p>
          <p class=\"text-gray-700 text-sm\"><strong>R:</strong> [Réponse détaillée et technique, incluant informations spécifiques sur {$serviceName}. Minimum 3-4 phrases.]</p>
        </div>
        <!-- MINIMUM 6 questions au total, toutes spécifiques à {$serviceName} -->
      </div>
    </div>
  </div>
  
  <div class=\"space-y-6\">
    <!-- SECTION 5: POURQUOI CHOISIR CE SERVICE -->
    <div class=\"bg-green-50 p-6 rounded-lg border-l-4 border-green-600\">
      <h3 class=\"text-xl font-bold text-gray-900 mb-3\">Pourquoi Choisir {$serviceName} avec Notre Entreprise</h3>
      <p class=\"leading-relaxed mb-3\">ÉCRIRE ICI les avantages concrets de notre approche pour {$serviceName}: [MENTIONNER 3-4 AVANTAGES SPÉCIFIQUES comme: matériaux premium sélectionnés, techniques avancées utilisées, certifications détenues, prix compétitifs, garanties étendues, etc.]. Chaque avantage doit être spécifique à {$serviceName}, pas générique.</p>
      <p class=\"leading-relaxed\">ÉCRIRE ICI notre expertise locale: nous connaissons parfaitement les spécificités climatiques de [RÉGION] (ex: humidité, gel, vent, exposition solaire) et nous adaptons nos solutions de {$serviceName} en conséquence. Notre présence locale à [VILLE] nous permet de [MENTIONNER AVANTAGES LOCAUX: ex: intervenir rapidement, connaître les réglementations locales, travailler avec des artisans locaux, etc.].</p>
    </div>
    
    <!-- SECTION 6: NOTRE EXPERTISE LOCALE -->
    <h3 class=\"text-2xl font-bold text-gray-900 mb-4\">Notre Expertise Locale en {$serviceName}</h3>
    <p class=\"leading-relaxed mb-4\">ÉCRIRE ICI notre expérience: depuis [NOMBRE] années, nous intervenons sur [VILLE] et dans [RÉGION] pour des projets de {$serviceName}. Nous avons réalisé [MENTIONNER TYPES DE PROJETS: ex: plus de 200 chantiers d'isolation de combles, 150 rénovations de plomberie, etc.]. Notre connaissance des spécificités régionales nous permet de proposer des solutions adaptées.</p>
    <p class=\"leading-relaxed\">ÉCRIRE ICI des exemples concrets: nous avons notamment [MENTIONNER 2-3 EXEMPLES CONCRETS de réalisations en {$serviceName} dans [RÉGION], avec détails techniques si possible]. Cette expérience locale nous permet de comprendre les besoins spécifiques des habitants de [VILLE] et de [RÉGION] en matière de {$serviceName}.</p>
    
    <!-- SECTION 7: BESOIN D'UN DEVIS -->
    <div class=\"bg-gradient-to-r from-blue-50 to-green-50 p-6 rounded-lg border-l-4 border-blue-600\">
      <h4 class=\"text-xl font-bold text-gray-900 mb-3\">Besoin d'un Devis pour {$serviceName} à [VILLE]?</h4>
      <p class=\"mb-4 text-gray-700\">Contactez-nous dès aujourd'hui pour obtenir un devis gratuit et personnalisé pour vos travaux de {$serviceName} à [VILLE].</p>
      <a href=\"[FORM_URL]\" class=\"inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300\">
        <i class=\"fas fa-calculator mr-2\"></i>Demander un Devis Gratuit
      </a>
    </div>
    
    <!-- SECTION 9: INFORMATIONS PRATIQUES -->
    <div class=\"bg-gray-50 p-6 rounded-lg\">
      <h4 class=\"text-lg font-bold text-gray-900 mb-3\">Informations Pratiques</h4>
      <ul class=\"space-y-2 text-sm\">
        <li class=\"flex items-center\">
          <i class=\"fas fa-check text-green-600 mr-3 flex-shrink-0\"></i>
          <span>Devis gratuit et sans engagement pour {$serviceName}</span>
        </li>
        <li class=\"flex items-center\">
          <i class=\"fas fa-check text-green-600 mr-3 flex-shrink-0\"></i>
          <span>Intervention rapide sur [VILLE] et [RÉGION]</span>
        </li>
        <li class=\"flex items-center\">
          <i class=\"fas fa-check text-green-600 mr-3 flex-shrink-0\"></i>
          <span>Garantie sur tous nos travaux de {$serviceName}</span>
        </li>
        <li class=\"flex items-center\">
          <i class=\"fas fa-check text-green-600 mr-3 flex-shrink-0\"></i>
          <span>Équipe d'experts certifiés en {$serviceName}</span>
        </li>
      </ul>
    </div>
  </div>
</div>

GÉNÈRE UN JSON AVEC CES CHAMPS:

{
  \"description\": \"[HTML complet suivant la structure ci-dessus, avec TOUTES les sections remplies et PERSONNALISÉES pour {$serviceName}]\",
  \"short_description\": \"Service professionnel de {$serviceName} à [VILLE] - Devis gratuit et intervention rapide\",
  \"long_description\": \"[Description longue de 4-5 phrases sur {$serviceName}, expliquant l'expertise, les techniques, les matériaux, et pourquoi choisir notre entreprise pour {$serviceName} à [VILLE] et dans la région de [RÉGION]. SOYEZ SPÉCIFIQUE et DÉTAILLÉ, minimum 400 mots.]\",
  \"icon\": \"fas fa-tools\",
  \"meta_title\": \"{$serviceName} à [VILLE] - Expert professionnel | Devis gratuit\",
  \"meta_description\": \"Service professionnel de {$serviceName} à [VILLE] et dans toute la région de [RÉGION]. Devis gratuit, intervention rapide, équipe experte certifiée. Garantie sur tous nos travaux.\",
  \"og_title\": \"{$serviceName} à [VILLE] - Expert professionnel\",
  \"og_description\": \"Service professionnel de {$serviceName} à [VILLE]. Devis gratuit, intervention rapide, garantie sur tous nos travaux.\",
  \"twitter_title\": \"{$serviceName} à [VILLE] - Expert professionnel\",
  \"twitter_description\": \"Service professionnel de {$serviceName} à [VILLE]. Devis gratuit, intervention rapide, garantie sur tous nos travaux.\",
  \"meta_keywords\": \"{$serviceName}, [VILLE], [RÉGION], expert {$serviceName}, devis gratuit {$serviceName}, professionnel {$serviceName}\"
}

⚠️⚠️⚠️ INSTRUCTIONS CRITIQUES - CONTENU PERSONNALISÉ ⚠️⚠️⚠️:

🔴 INTERDICTIONS STRICTES:
- INTERDIT de copier les placeholders [Paragraphe X] - TU DOIS LES REMPLACER PAR DU VRAI TEXTE
- INTERDIT d'utiliser des phrases génériques comme \"Nous garantissons la satisfaction\", \"Notre expertise locale\", \"Intervention rapide\" sans détails concrets
- INTERDIT d'utiliser \"Spécialistes en travaux de {$serviceName}\" ou phrases similaires génériques
- INTERDIT d'écrire \"Nous vous accompagnons dans vos démarches\" sans expliquer COMMENT concrètement

✅ OBLIGATIONS:
1. REMPLACER TOUS les \"[Paragraphe X]\" et \"ÉCRIRE ICI\" par du VRAI contenu écrit
2. Pour chaque section, écrire 3-4 phrases MINIMUM avec des détails CONCRETS
3. Mentionner des chiffres, techniques, matériaux, normes, certifications SPÉCIFIQUES à {$serviceName}
4. Utiliser un vocabulaire PROFESSIONNEL du métier de {$serviceName}
5. Donner des EXEMPLES CONCRETS (types de projets, techniques utilisées, matériaux)

📝 EXEMPLE POUR \"Isolation thermique\":

❌ MAUVAIS (GÉNÉRIQUE):
\"Nous garantissons la satisfaction totale de nos clients. Nous réalisons des travaux d'isolation selon les normes.\"

✅ BON (PERSONNALISÉ):
\"Pour l'isolation thermique, nous garantissons une performance énergétique conforme aux exigences RT 2012, avec une résistance thermique minimale R = 7 m².K/W pour les combles perdus. Nos équipes certifiées RGE Qualit'ENR utilisent exclusivement des isolants certifiés ACERMI (laine de verre, laine de roche, ouate de cellulose) adaptés aux spécificités climatiques de [RÉGION]. Chaque chantier fait l'objet d'un diagnostic thermique complet avant travaux, puis d'un contrôle qualité avec mesure de l'étanchéité à l'air selon la norme NF EN 13829, garantissant jusqu'à 30% d'économies d'énergie.\"

📋 INSTRUCTIONS TECHNIQUES JSON:
- TU DOIS RÉPONDRE UNIQUEMENT AVEC UN JSON VALIDE
- COMMENCE DIRECTEMENT PAR { (accolade ouvrante)
- TERMINE DIRECTEMENT PAR } (accolade fermante)
- PAS de texte avant le JSON
- PAS de texte après le JSON
- PAS de ```json ou ``` autour du JSON
- GÉNÈRE EXACTEMENT 10 PRESTATIONS TECHNIQUES SPÉCIFIQUES avec descriptions détaillées (minimum 2 phrases par prestation)
- GÉNÈRE MINIMUM 6 QUESTIONS FAQ spécifiques à {$serviceName} avec réponses détaillées (minimum 3 phrases par réponse)
- TOUT le contenu HTML dans \"description\" doit être COMPLET avec TOUS les \"ÉCRIRE ICI\" REMPLACÉS par du vrai texte
- La description longue (long_description) doit faire minimum 400 mots
- ⚠️⚠️⚠️ VALIDATION DES PRESTATIONS: Si les 10 prestations ne sont PAS spécifiques à {$serviceName} (ex: si {$serviceName} = 'Rénovation de façade' mais les prestations parlent de plomberie ou autre chose), ta réponse sera REJETÉE et tu devras recommencer
- Les 10 prestations DOIVENT être DIFFÉRENTES et COMPLÉMENTAIRES pour {$serviceName}
- Chaque prestation DOIT être UNIQUE et ne pas se répéter
";

        if ($aiPrompt) {
            $basePrompt .= "\n\nINSTRUCTIONS PERSONNALISÉES SUPPLÉMENTAIRES:\n" . $aiPrompt;
        }

        return $basePrompt;
    }

    /**
     * Parser la réponse IA pour template (inspiré de parseAIResponse)
     */
    private function parseAIResponseForTemplate($content)
    {
        $content = trim($content);
        
        // Si le contenu semble être directement du HTML (pas de JSON)
        if (strpos($content, '<div') !== false && strpos($content, '{') === false) {
            Log::info('Contenu HTML direct détecté dans template, création de structure JSON');
            $plainText = strip_tags($content);
            $shortDesc = Str::limit($plainText, 140);
            $metaDesc = Str::limit($plainText, 160);
            
            return [
                'description' => $content,
                'short_description' => $shortDesc,
                'long_description' => Str::limit($plainText, 500),
                'icon' => 'fas fa-tools',
                'meta_title' => '',
                'meta_description' => $metaDesc,
                'og_title' => '',
                'og_description' => $metaDesc,
                'twitter_title' => '',
                'twitter_description' => $metaDesc,
                'meta_keywords' => ''
            ];
        }
        
        $jsonPatterns = [
            '/```json\s*(\{[\s\S]*?\})\s*```/s',
            '/```\s*(\{[\s\S]*?\})\s*```/s',
            '/\{[\s\S]*\"description\"[\s\S]*\}/s',
            '/\{.*\}/s',
        ];
        
        foreach ($jsonPatterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                $jsonString = $matches[1] ?? $matches[0];
                $jsonString = trim($jsonString);
                $data = json_decode($jsonString, true);
                
                if ($data && is_array($data) && !empty($data)) {
                    Log::info('JSON parsé avec succès pour template');
                    return $data;
                }
            }
        }
        
        $data = json_decode($content, true);
        if ($data && is_array($data) && !empty($data)) {
            Log::info('JSON parsé directement pour template');
            return $data;
        }
        
        Log::warning('Impossible de parser la réponse IA pour template', [
            'content_preview' => substr($content, 0, 500)
        ]);
        
        return null;
    }

    /**
     * Valider et nettoyer les données IA pour template
     */
    private function validateAndCleanAIDataForTemplate($aiData, $serviceName, $shortDescription)
    {
        $description = $aiData['description'] ?? '';
        $cleanText = function($text, $maxLength = null) {
            $text = strip_tags($text);
            $text = trim($text);
            return $maxLength ? Str::limit($text, $maxLength) : $text;
        };
        
        return [
            'description' => $description,
            'short_description' => $cleanText($aiData['short_description'] ?? $shortDescription, 140),
            'long_description' => $cleanText($aiData['long_description'] ?? strip_tags($description), 500),
            'icon' => $aiData['icon'] ?? 'fas fa-tools',
            'meta_title' => $cleanText($aiData['meta_title'] ?? ($serviceName . ' à [VILLE] - Service professionnel'), 160),
            'meta_description' => $cleanText($aiData['meta_description'] ?? 'Service professionnel à [VILLE]', 500),
            'meta_keywords' => $aiData['meta_keywords'] ?? ($serviceName . ', ' . $serviceName . ' [VILLE], ' . $serviceName . ' [RÉGION], expert ' . $serviceName . ', ' . $serviceName . ' professionnel, entreprise ' . $serviceName . ', artisan ' . $serviceName . ', ' . $serviceName . ' certifié, rénovation, réparation, installation, intervention rapide, devis gratuit, qualité garantie, intervention [VILLE], service [VILLE], professionnel [VILLE]'),
            'og_title' => $cleanText($aiData['og_title'] ?? ($serviceName . ' à [VILLE] - Service professionnel'), 160),
            'og_description' => $cleanText($aiData['og_description'] ?? 'Service professionnel à [VILLE]', 500),
            'twitter_title' => $cleanText($aiData['twitter_title'] ?? ($serviceName . ' à [VILLE] - Service professionnel'), 160),
            'twitter_description' => $cleanText($aiData['twitter_description'] ?? 'Service professionnel à [VILLE]', 500),
        ];
    }

}