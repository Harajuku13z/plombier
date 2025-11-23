# Documentation Fonctionnelle - Système d'Annonces

## Vue d'ensemble

Le système d'annonces est un système de génération automatique et manuelle de pages de services géolocalisées. Il permet de créer des pages uniques pour chaque combinaison service/ville, optimisées pour le SEO local.

---

## Architecture du Système

### 1. Structure de Base de Données

#### Table `ad_templates`
**Rôle** : Stocke les modèles de contenu réutilisables pour chaque service.

**Champs principaux** :
- `id` : Identifiant unique
- `name` : Nom du template (ex: "Rénovation plomberie")
- `service_name` : Nom du service (ex: "Dépannage et réparation de fuites d'eau")
- `service_slug` : Slug unique du service (ex: "depannage-et-reparation-de-fuites-deau")
- `content_html` : Contenu HTML complet généré par l'IA ou créé manuellement
- `short_description` : Description courte du service
- `long_description` : Description longue du service
- `icon` : Icône Font Awesome (ex: "fas fa-tools")
- `featured_image` : Image mise en avant du service
- `meta_title` : Titre SEO (max 160 caractères)
- `meta_description` : Description SEO (max 500 caractères)
- `meta_keywords` : Mots-clés SEO
- `og_title`, `og_description` : Métadonnées Open Graph pour les réseaux sociaux
- `twitter_title`, `twitter_description` : Métadonnées Twitter
- `ai_prompt_used` : JSON du prompt utilisé pour générer le template
- `ai_response_data` : JSON de la réponse complète de l'IA
- `is_active` : Boolean - Template actif/inactif
- `usage_count` : Nombre d'annonces utilisant ce template
- `created_at`, `updated_at` : Timestamps

**Index** :
- `service_slug` + `is_active` (pour recherche rapide)
- `service_name` (pour recherche par nom)

#### Table `ads`
**Rôle** : Stocke les annonces individuelles générées pour chaque ville.

**Champs principaux** :
- `id` : Identifiant unique
- `title` : Titre de l'annonce (ex: "Dépannage et réparation de fuites d'eau à Versailles")
- `keyword` : Mot-clé principal (ex: "Dépannage et réparation de fuites d'eau")
- `city_id` : Foreign key vers `cities` (cascade delete)
- `template_id` : Foreign key vers `ad_templates` (set null on delete)
- `slug` : URL unique (ex: "depannage-et-reparation-de-fuites-deau-versailles")
- `status` : Enum ('draft', 'published', 'archived') - Par défaut 'draft'
- `meta_title` : Titre SEO personnalisé pour la ville
- `meta_description` : Description SEO personnalisée pour la ville
- `content_html` : Contenu HTML final avec variables remplacées
- `content_json` : JSON du contenu structuré (optionnel)
- `published_at` : Date de publication
- `created_at`, `updated_at` : Timestamps

**Index** :
- `slug` (unique)
- `status` (pour filtrage)
- `template_id` (pour recherche par template)
- `city_id` (pour recherche par ville)

#### Table `cities`
**Rôle** : Référentiel des villes couvertes.

**Champs utilisés** :
- `id` : Identifiant unique
- `name` : Nom de la ville
- `postal_code` : Code postal
- `department` : Département
- `region` : Région
- `population` : Population (optionnel)
- `is_favorite` : Boolean - Ville favorite pour génération rapide

---

## Flux de Fonctionnement

### 1. Création d'un Template

#### Méthode A : Génération Automatique par IA

**Workflow** :
1. L'administrateur sélectionne un service depuis la liste des services configurés
2. Optionnellement, il peut fournir un prompt personnalisé pour l'IA
3. Le système appelle l'API IA (ChatGPT/Groq) avec :
   - Le nom du service
   - Le prompt personnalisé (ou prompt par défaut)
   - Les instructions de génération de contenu SEO
4. L'IA génère :
   - Un contenu HTML complet avec sections (H2, H3, listes, FAQ, etc.)
   - Les métadonnées SEO (title, description, keywords)
   - Les métadonnées Open Graph et Twitter
5. Le template est sauvegardé dans `ad_templates`
6. Le compteur `usage_count` est initialisé à 0

**Fichiers impliqués** :
- `app/Http/Controllers/Admin/AdTemplateController.php` → `createFromService()`
- `app/Services/AiService.php` → Appel API IA

#### Méthode B : Création Manuelle

**Workflow** :
1. L'administrateur remplit un formulaire avec :
   - Nom du service
   - Slug du service
   - Contenu HTML (éditeur WYSIWYG ou code)
   - Descriptions courte et longue
   - Métadonnées SEO
   - Image mise en avant (optionnel)
2. Le template est sauvegardé directement

**Fichiers impliqués** :
- `app/Http/Controllers/Admin/AdTemplateController.php` → `store()`
- `resources/views/admin/ads/templates/create.blade.php`

### 2. Personnalisation du Contenu par Ville

Le système utilise deux méthodes de personnalisation :

#### Méthode 1 : Remplacement de Variables (Basique)

**Variables disponibles** :
- `[VILLE]` → Nom de la ville
- `[RÉGION]` → Région de la ville
- `[DÉPARTEMENT]` → Département de la ville
- `[CODE_POSTAL]` → Code postal
- `[FORM_URL]` → URL du formulaire de devis
- `[URL]` → URL de l'annonce
- `[TITRE]` → Titre de l'annonce
- `[PHONE]` → Numéro de téléphone de l'entreprise

**Exemple** :
```
Template : "Service de plomberie à [VILLE] dans le [DÉPARTEMENT]"
Résultat pour Versailles : "Service de plomberie à Versailles dans les Yvelines"
```

**Fichiers impliqués** :
- `app/Models/AdTemplate.php` → `getContentForCity()` (fallback)
- `app/Models/AdTemplate.php` → `getMetaForCity()` (fallback)

#### Méthode 2 : Personnalisation IA Avancée (Optionnel)

**Workflow** :
1. Le système vérifie si la personnalisation IA est activée dans les settings
2. Si activée, il appelle `CityContentPersonalizer`
3. Le service construit un contexte local de la ville :
   - Informations géographiques
   - Type de zone (grande ville, ville moyenne, petite ville, rurale)
   - Climat régional
   - Architecture typique
   - Défis spécifiques à la région
4. Un prompt est construit avec :
   - Le contenu du template
   - Le contexte de la ville
   - Les instructions de personnalisation
5. L'IA génère un contenu **100% UNIQUE** pour cette ville
6. Le contenu est mis en cache (30 jours) pour éviter les appels répétés
7. En cas d'erreur, fallback sur la méthode basique

**Fichiers impliqués** :
- `app/Services/CityContentPersonalizer.php`
- `app/Models/AdTemplate.php` → `getContentForCity()` (avec IA)
- `app/Models/AdTemplate.php` → `getMetaForCity()` (avec IA)

### 3. Génération d'Annonces

#### Méthode A : Génération en Masse (Bulk)

**Workflow** :
1. L'administrateur sélectionne :
   - Un service (ou un template existant)
   - Une ou plusieurs villes (checkboxes)
   - Optionnellement un prompt IA personnalisé
2. Le système vérifie si un template existe pour ce service
   - Si non, il crée le template automatiquement (voir section 1)
3. Pour chaque ville sélectionnée :
   - Vérifie si une annonce existe déjà (template_id + city_id)
   - Si oui, skip cette ville
   - Si non :
     - Génère le contenu personnalisé pour la ville
     - Génère les métadonnées personnalisées
     - Crée un slug unique (service-slug + ville-slug)
     - Crée l'annonce avec status 'published'
     - Incrémente `usage_count` du template
4. Retourne un résumé : X créées, Y ignorées, Z erreurs

**Fichiers impliqués** :
- `app/Http/Controllers/Admin/BulkAdsController.php` → `generateBulkAds()`
- `resources/views/admin/ads/bulk-ads.blade.php`

#### Méthode B : Génération depuis un Template

**Workflow** :
1. L'administrateur ouvre un template existant
2. Il sélectionne les villes pour lesquelles générer des annonces
3. Le système génère les annonces comme dans la méthode A

**Fichiers impliqués** :
- `app/Http/Controllers/Admin/AdTemplateController.php` → `generateAdsFromTemplate()`
- `resources/views/admin/ads/templates/show.blade.php`

#### Méthode C : Création Manuelle

**Workflow** :
1. L'administrateur remplit un formulaire avec :
   - Titre
   - Service/Ville
   - Contenu HTML
   - Métadonnées SEO
   - Status (draft/published)
2. L'annonce est créée directement

**Fichiers impliqués** :
- `app/Http/Controllers/Admin/AdController.php` (si existe)
- `resources/views/admin/ads/manual.blade.php`

### 4. Affichage Public

**Workflow** :
1. L'utilisateur accède à `/ads/{slug}`
2. Le système :
   - Récupère l'annonce par slug
   - Vérifie que le status est 'published'
   - Charge la ville associée
   - Charge le template associé (si existe)
3. Génération des métadonnées :
   - Si template existe : utilise `getMetaForCity()` pour métadonnées personnalisées
   - Sinon : utilise les métadonnées de l'annonce directement
4. Récupération du contenu :
   - Si template existe : utilise `getContentForCity()` pour contenu personnalisé
   - Sinon : utilise `content_html` de l'annonce
5. Récupération des données complémentaires :
   - Annonces similaires (même ville, autres services)
   - Réalisations/Portfolio (si configuré)
   - Avis clients (3 derniers)
6. Affichage dans le template Blade

**Fichiers impliqués** :
- `app/Http/Controllers/AdPublicController.php` → `show()`
- `resources/views/ads/show.blade.php`
- `routes/web.php` → Route `/ads/{slug}`

---

## Services et Classes Métier

### 1. `AdTemplate` (Model)

**Méthodes principales** :

- `getContentForCity($city, $useAi = null)` :
  - Retourne le contenu HTML personnalisé pour une ville
  - Si `$useAi` est true ou activé dans settings, utilise l'IA
  - Sinon, fait un simple remplacement de variables
  - Retourne le contenu final

- `getMetaForCity($city, $useAi = null)` :
  - Retourne un array avec toutes les métadonnées personnalisées
  - Format : `['meta_title', 'meta_description', 'meta_keywords', 'og_title', 'og_description', 'twitter_title', 'twitter_description']`
  - Même logique IA que `getContentForCity()`

- `incrementUsage()` / `decrementUsage()` :
  - Gère le compteur d'utilisation du template

**Relations** :
- `ads()` : HasMany vers `Ad`

### 2. `Ad` (Model)

**Relations** :
- `city()` : BelongsTo vers `City`
- `template()` : BelongsTo vers `AdTemplate`

**Méthodes** :
- `getPublicationDateAttribute()` : Retourne `published_at` ou `created_at`
- `getFormattedPublicationDateAttribute()` : Date formatée

### 3. `CityContentPersonalizer` (Service)

**Rôle** : Génère du contenu unique par ville avec l'IA

**Méthodes principales** :

- `generatePersonalizedContent($templateContent, $service, City $city)` :
  - Construit le contexte local de la ville
  - Crée un prompt personnalisé
  - Appelle l'IA pour générer du contenu unique
  - Post-traite le contenu (URLs, contacts)
  - Met en cache (30 jours)
  - Retourne le contenu personnalisé

- `generatePersonalizedMeta($serviceName, $city, $baseMeta)` :
  - Génère des métadonnées SEO uniques pour la ville
  - Utilise l'IA pour éviter la duplication
  - Retourne un array de métadonnées

- `buildCityContext($city)` :
  - Construit un contexte riche avec :
    - Informations géographiques
    - Type de zone
    - Climat régional
    - Architecture typique
    - Défis spécifiques

- `buildPersonalizationPrompt($templateContent, $service, $city, $cityContext)` :
  - Construit un prompt détaillé pour l'IA
  - Inclut toutes les informations contextuelles
  - Instructions de personnalisation

### 4. `AiService` (Service)

**Rôle** : Interface avec les APIs IA (ChatGPT, Groq)

**Méthodes** :
- `callAI($prompt, $systemMessage, $options)` :
  - Appelle l'API configurée
  - Gère les erreurs et timeouts
  - Retourne le résultat formaté

---

## Routes et URLs

### Routes Publiques

```
GET /ads
→ Liste toutes les annonces publiées
→ Controller: AdPublicController@index
→ View: ads.index

GET /ads/{slug}
→ Affiche une annonce spécifique
→ Controller: AdPublicController@show
→ View: ads.show
```

### Routes Admin

```
GET /admin/ads
→ Liste toutes les annonces
→ Controller: Admin\AdController@index

GET /admin/ads/templates
→ Liste tous les templates
→ Controller: Admin\AdTemplateController@index

GET /admin/ads/templates/{id}
→ Affiche un template et ses annonces
→ Controller: Admin\AdTemplateController@show

POST /admin/ads/templates/create-from-service
→ Crée un template depuis un service (avec IA)
→ Controller: Admin\AdTemplateController@createFromService

POST /admin/ads/templates/{id}/generate-ads
→ Génère des annonces depuis un template
→ Controller: Admin\AdTemplateController@generateAdsFromTemplate

POST /admin/ads/bulk-generate
→ Génération en masse d'annonces
→ Controller: Admin\BulkAdsController@generateBulkAds
```

---

## Structure des Templates Blade

### Template Public : `ads/show.blade.php`

**Sections** :
1. **Hero Section** :
   - Titre de l'annonce
   - Description courte
   - Boutons CTA (Simulateur de devis, Appeler)

2. **Contenu Principal** :
   - Contenu HTML de l'annonce (depuis template ou direct)
   - Sections H2, H3, listes, FAQ, etc.

3. **Section CTA** :
   - Appel à l'action pour devis
   - Boutons de contact

4. **Section Réalisations** (optionnel) :
   - Portfolio de réalisations
   - Images et descriptions

5. **Section Annonces Similaires** :
   - Autres services dans la même ville
   - Liens vers autres annonces

6. **Section Avis Clients** :
   - 3 derniers avis
   - Notes et commentaires

**Variables disponibles** :
- `$ad` : Objet Ad
- `$cityModel` : Objet City
- `$relatedAds` : Collection d'annonces similaires
- `$portfolioItems` : Array de réalisations (si configuré)
- `$featuredImage` : Image mise en avant (depuis template)
- `$pageTitle`, `$pageDescription`, etc. : Métadonnées SEO

---

## Système de Cache

### Cache du Contenu Personnalisé

**Clé de cache** :
```
'personalized_content_' . md5($serviceName . '_' . $cityId . '_' . substr($templateContent, 0, 100))
```

**Durée** : 30 jours (2592000 minutes)

**Objectif** : Éviter les appels répétés à l'IA pour le même contenu

**Invalidation** : Manuelle ou lors de la mise à jour du template

---

## Configuration et Settings

### Settings Utilisés

- `ad_template_ai_personalization` : Boolean - Active/désactive la personnalisation IA
- `services` : JSON - Liste des services disponibles
- `company_phone` : Numéro de téléphone de l'entreprise
- `company_phone_raw` : Numéro sans formatage
- `site_url` : URL de base du site

---

## Workflow Complet d'Exemple

### Scénario : Créer des annonces pour "Dépannage de fuites d'eau" dans 10 villes

1. **Création du Template** :
   - Admin va dans `/admin/ads/templates`
   - Clique sur "Créer depuis un service"
   - Sélectionne "Dépannage de fuites d'eau"
   - Optionnel : Ajoute un prompt personnalisé
   - Le système génère le template avec l'IA
   - Template sauvegardé avec `service_slug = "depannage-fuites-eau"`

2. **Génération en Masse** :
   - Admin va dans `/admin/ads/bulk-ads`
   - Sélectionne le service "Dépannage de fuites d'eau"
   - Sélectionne 10 villes (Versailles, Rambouillet, Trappes, etc.)
   - Clique sur "Générer les annonces"
   - Le système :
     - Vérifie que le template existe (oui, créé à l'étape 1)
     - Pour chaque ville :
       - Génère le contenu personnalisé (remplacement de variables ou IA)
       - Génère les métadonnées personnalisées
       - Crée le slug : "depannage-fuites-eau-versailles"
       - Crée l'annonce avec status 'published'
     - Retourne : "10 annonces créées avec succès"

3. **Résultat** :
   - 10 URLs publiques créées :
     - `/ads/depannage-fuites-eau-versailles`
     - `/ads/depannage-fuites-eau-rambouillet`
     - etc.
   - Chaque page a :
     - Un contenu unique (si IA activée) ou personnalisé (variables)
     - Des métadonnées SEO optimisées
     - Un titre unique : "Dépannage de fuites d'eau à [VILLE]"

---

## Adaptation pour WordPress

### 1. Structure de Base de Données

**Créer les tables personnalisées** :

```sql
-- Table des templates
CREATE TABLE wp_ad_templates (
    id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    service_name VARCHAR(255) NOT NULL,
    service_slug VARCHAR(255) NOT NULL,
    content_html LONGTEXT NOT NULL,
    short_description TEXT,
    long_description TEXT,
    icon VARCHAR(50) DEFAULT 'fas fa-tools',
    featured_image VARCHAR(255),
    meta_title VARCHAR(160),
    meta_description TEXT,
    meta_keywords TEXT,
    og_title VARCHAR(160),
    og_description TEXT,
    twitter_title VARCHAR(160),
    twitter_description TEXT,
    ai_prompt_used LONGTEXT,
    ai_response_data LONGTEXT,
    is_active TINYINT(1) DEFAULT 1,
    usage_count INT(11) DEFAULT 0,
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_service_slug_active (service_slug, is_active),
    INDEX idx_service_name (service_name)
);

-- Table des annonces
CREATE TABLE wp_ads (
    id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    keyword VARCHAR(255),
    city_id BIGINT(20) UNSIGNED,
    template_id BIGINT(20) UNSIGNED,
    slug VARCHAR(255) UNIQUE NOT NULL,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    meta_title VARCHAR(160),
    meta_description TEXT,
    content_html LONGTEXT,
    content_json LONGTEXT,
    published_at DATETIME,
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_template_id (template_id),
    INDEX idx_city_id (city_id),
    FOREIGN KEY (template_id) REFERENCES wp_ad_templates(id) ON DELETE SET NULL
);
```

### 2. Custom Post Types

**Alternative WordPress** : Utiliser des Custom Post Types au lieu de tables personnalisées

**Créer un CPT "ad_template"** :
```php
register_post_type('ad_template', [
    'public' => false,
    'show_ui' => true,
    'supports' => ['title', 'editor', 'custom-fields'],
]);
```

**Créer un CPT "ad"** :
```php
register_post_type('ad', [
    'public' => true,
    'rewrite' => ['slug' => 'ads'],
    'supports' => ['title', 'editor', 'custom-fields'],
]);
```

**Utiliser ACF (Advanced Custom Fields)** pour les champs personnalisés :
- Template : service_name, service_slug, meta_title, etc.
- Ad : city_id, template_id, keyword, etc.

### 3. Fonctions PHP WordPress

**Créer un fichier `includes/class-ad-template.php`** :

```php
class Ad_Template {
    private $post_id;
    
    public function __construct($post_id) {
        $this->post_id = $post_id;
    }
    
    public function get_content_for_city($city_id, $use_ai = null) {
        $template_content = get_post_field('post_content', $this->post_id);
        $city = get_post($city_id);
        
        // Remplacer les variables
        $replacements = [
            '[VILLE]' => get_field('name', $city_id),
            '[DÉPARTEMENT]' => get_field('department', $city_id),
            // etc.
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $template_content);
    }
    
    public function get_meta_for_city($city_id) {
        // Similaire à get_content_for_city mais pour les métadonnées
    }
}
```

### 4. Routes et Rewrite Rules

**Ajouter une rewrite rule** :
```php
add_rewrite_rule('^ads/([^/]+)/?$', 'index.php?ad_slug=$matches[1]', 'top');
```

**Créer un query var** :
```php
add_filter('query_vars', function($vars) {
    $vars[] = 'ad_slug';
    return $vars;
});
```

**Template loader** :
```php
add_filter('template_include', function($template) {
    if (get_query_var('ad_slug')) {
        return locate_template('single-ad.php');
    }
    return $template;
});
```

### 5. Interface Admin

**Créer des pages admin** :
```php
add_action('admin_menu', function() {
    add_menu_page(
        'Annonces',
        'Annonces',
        'manage_options',
        'ads',
        'ads_admin_page'
    );
    
    add_submenu_page(
        'ads',
        'Templates',
        'Templates',
        'manage_options',
        'ad-templates',
        'ad_templates_admin_page'
    );
    
    add_submenu_page(
        'ads',
        'Génération en Masse',
        'Génération en Masse',
        'manage_options',
        'bulk-ads',
        'bulk_ads_admin_page'
    );
});
```

### 6. Intégration IA

**Utiliser l'API WordPress HTTP** :
```php
function call_ai_api($prompt, $system_message) {
    $response = wp_remote_post('https://api.openai.com/v1/chat/completions', [
        'headers' => [
            'Authorization' => 'Bearer ' . get_option('openai_api_key'),
            'Content-Type' => 'application/json',
        ],
        'body' => json_encode([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => $system_message],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]),
    ]);
    
    return json_decode(wp_remote_retrieve_body($response), true);
}
```

### 7. Cache

**Utiliser Transients API** :
```php
function get_cached_personalized_content($cache_key) {
    return get_transient($cache_key);
}

function set_cached_personalized_content($cache_key, $content) {
    set_transient($cache_key, $content, 30 * DAY_IN_SECONDS);
}
```

### 8. Template Public

**Créer `single-ad.php`** :
```php
<?php
$ad_slug = get_query_var('ad_slug');
$ad = get_ad_by_slug($ad_slug);
$city = get_post($ad->city_id);
$template = get_post($ad->template_id);

// Générer le contenu personnalisé
$content = $template->get_content_for_city($city->ID);
$meta = $template->get_meta_for_city($city->ID);

get_header();
?>
<!-- Afficher le contenu -->
<?php echo $content; ?>
<?php
get_footer();
```

---

## Points Clés pour WordPress

1. **Utiliser Custom Post Types** au lieu de tables personnalisées (plus WordPress-friendly)
2. **ACF (Advanced Custom Fields)** pour les champs personnalisés
3. **Transients API** pour le cache
4. **WP_Query** pour les requêtes
5. **Rewrite Rules** pour les URLs propres
6. **Admin Pages** avec `add_menu_page()` et `add_submenu_page()`
7. **Template Hierarchy** pour l'affichage public
8. **Hooks et Filters** pour l'extensibilité

---

## Conclusion

Ce système permet de :
- ✅ Créer des templates réutilisables par service
- ✅ Générer automatiquement des pages uniques par ville
- ✅ Personnaliser le contenu avec l'IA (optionnel)
- ✅ Optimiser le SEO local
- ✅ Gérer facilement des centaines d'annonces
- ✅ Maintenir la cohérence du contenu

L'adaptation WordPress nécessite de transformer les concepts Laravel en équivalents WordPress, mais la logique métier reste identique.

