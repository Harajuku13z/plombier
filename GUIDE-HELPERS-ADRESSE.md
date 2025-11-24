# Guide des Helpers d'Adresse

## 🎯 Objectif

Ce guide explique comment utiliser les nouvelles fonctions helper pour afficher l'adresse complète de l'entreprise de manière propre et cohérente dans toute l'application.

## ✅ Fonctions Disponibles

### 1. `full_address()`

Retourne l'adresse complète sur une ligne avec un séparateur personnalisable.

**Signature:**
```php
full_address(string $separator = ', ', bool $includeCountry = true): string
```

**Paramètres:**
- `$separator` : Séparateur entre les lignes (par défaut : `', '`)
- `$includeCountry` : Inclure le pays (par défaut : `true`)

**Exemples d'utilisation:**

```blade
<!-- Adresse complète avec virgules (défaut) -->
{{ full_address() }}
<!-- Résultat : "35 Rue des Chantiers, 78000 Versailles, France" -->

<!-- Adresse sans le pays -->
{{ full_address(', ', false) }}
<!-- Résultat : "35 Rue des Chantiers, 78000 Versailles" -->

<!-- Adresse avec tirets comme séparateur -->
{{ full_address(' - ', true) }}
<!-- Résultat : "35 Rue des Chantiers - 78000 Versailles - France" -->

<!-- Adresse avec saut de ligne -->
{{ full_address("\n", false) }}
<!-- Résultat (multilignes) :
35 Rue des Chantiers
78000 Versailles
-->
```

### 2. `full_address_html()`

Retourne l'adresse complète formatée en HTML avec des divs.

**Signature:**
```php
full_address_html(): string
```

**Exemple d'utilisation:**

```blade
{!! full_address_html() !!}

<!-- Résultat HTML :
<div class="address">
    <div>35 Rue des Chantiers</div>
    <div>78000 Versailles</div>
    <div>France</div>
</div>
-->
```

### 3. `address_for_maps()`

Retourne l'adresse encodée pour Google Maps ou autres liens de géolocalisation.

**Signature:**
```php
address_for_maps(): string
```

**Exemple d'utilisation:**

```blade
<!-- Lien Google Maps -->
<a href="https://www.google.com/maps/search/?api=1&query={{ address_for_maps() }}" 
   target="_blank">
    Voir sur Google Maps
</a>

<!-- Lien Waze -->
<a href="https://waze.com/ul?q={{ address_for_maps() }}" 
   target="_blank">
    Ouvrir dans Waze
</a>
```

### 4. `company_address_line()`

Retourne l'adresse sur une seule ligne sans le pays (raccourci pratique).

**Signature:**
```php
company_address_line(): string
```

**Exemple d'utilisation:**

```blade
<!-- Adresse simple sans pays -->
<p>{{ company_address_line() }}</p>
<!-- Résultat : "35 Rue des Chantiers, 78000 Versailles" -->
```

## 🔧 Méthodes Statiques du Modèle Setting

Si vous préférez utiliser directement le modèle `Setting` au lieu des helpers :

### `Setting::getFullAddress()`

```php
use App\Models\Setting;

$address = Setting::getFullAddress(', ', true);
// "35 Rue des Chantiers, 78000 Versailles, France"
```

### `Setting::getFullAddressHtml()`

```php
use App\Models\Setting;

$addressHtml = Setting::getFullAddressHtml();
// HTML formaté
```

### `Setting::getAddressForMaps()`

```php
use App\Models\Setting;

$encodedAddress = Setting::getAddressForMaps();
// URL-encoded address
```

## 📋 Cas d'Utilisation Pratiques

### 1. Dans le footer d'un site

```blade
<footer>
    <div class="company-info">
        <h3>{{ setting('company_name') }}</h3>
        {!! full_address_html() !!}
        <p>
            <a href="tel:{{ setting('company_phone_raw') }}">
                {{ setting('company_phone') }}
            </a>
        </p>
    </div>
</footer>
```

### 2. Dans un email

```blade
<!-- Email en texte simple -->
<p>
    Adresse : {{ full_address() }}
</p>

<!-- Email HTML -->
<div class="address-block">
    {!! full_address_html() !!}
</div>
```

### 3. Dans une page de contact

```blade
<div class="contact-info">
    <div class="info-item">
        <i class="fas fa-map-marker-alt"></i>
        <div>
            <h4>Notre adresse</h4>
            {!! full_address_html() !!}
            <a href="https://www.google.com/maps/search/?api=1&query={{ address_for_maps() }}" 
               class="btn-map">
                Voir sur la carte
            </a>
        </div>
    </div>
</div>
```

### 4. Dans les métadonnées Schema.org

```blade
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "{{ setting('company_name') }}",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "{{ setting('company_address') }}",
    "addressLocality": "{{ setting('company_city') }}",
    "postalCode": "{{ setting('company_postal_code') }}",
    "addressCountry": "{{ setting('company_country', 'FR') }}"
  }
}
</script>
```

### 5. Dans un template d'annonce généré par l'IA

```blade
<div class="bg-gray-50 p-6 rounded-lg">
    <h4 class="text-lg font-bold text-gray-900 mb-3">Informations Pratiques</h4>
    <ul class="space-y-2 text-sm">
        <li class="flex items-start">
            <i class="fas fa-map-marker-alt text-red-600 mr-3 mt-1"></i>
            <div>
                <strong>Adresse :</strong><br>
                {!! full_address_html() !!}
            </div>
        </li>
        <li class="flex items-center">
            <i class="fas fa-phone text-green-600 mr-3"></i>
            <strong>Téléphone :</strong>
            <a href="tel:{{ setting('company_phone_raw') }}" class="ml-2 text-blue-600">
                {{ setting('company_phone') }}
            </a>
        </li>
        <li class="flex items-center">
            <i class="fas fa-envelope text-blue-600 mr-3"></i>
            <strong>Email :</strong>
            <a href="mailto:{{ setting('company_email') }}" class="ml-2 text-blue-600">
                {{ setting('company_email') }}
            </a>
        </li>
    </ul>
</div>
```

## 🚀 Installation

Les helpers sont déjà configurés ! Après avoir mis à jour le code :

```bash
# 1. Recharger l'autoloader Composer
composer dump-autoload

# 2. Vider le cache Laravel (optionnel)
php artisan cache:clear
php artisan view:clear
```

## ✨ Avantages

✅ **Cohérence** : L'adresse est toujours formatée de la même manière  
✅ **Maintenabilité** : Si le format change, il suffit de modifier la méthode une seule fois  
✅ **Flexibilité** : Plusieurs formats disponibles selon le contexte  
✅ **Simplicité** : Fonctions helper courtes et faciles à utiliser  
✅ **DRY** : Ne répétez plus jamais le code de formatage d'adresse  

## 📞 Exemples Complets

### Carte de visite numérique

```blade
<div class="business-card">
    <h2>{{ setting('company_name') }}</h2>
    
    <div class="contact-details">
        <!-- Adresse -->
        <div class="detail">
            <i class="fas fa-location-dot"></i>
            <span>{{ company_address_line() }}</span>
        </div>
        
        <!-- Téléphone -->
        <div class="detail">
            <i class="fas fa-phone"></i>
            <a href="tel:{{ setting('company_phone_raw') }}">
                {{ setting('company_phone') }}
            </a>
        </div>
        
        <!-- Email -->
        <div class="detail">
            <i class="fas fa-envelope"></i>
            <a href="mailto:{{ setting('company_email') }}">
                {{ setting('company_email') }}
            </a>
        </div>
        
        <!-- Lien Maps -->
        <div class="detail">
            <i class="fas fa-map"></i>
            <a href="https://www.google.com/maps/search/?api=1&query={{ address_for_maps() }}" 
               target="_blank" rel="noopener">
                Voir sur Google Maps
            </a>
        </div>
    </div>
</div>
```

### Widget de contact avec QR Code

```blade
<div class="contact-widget">
    <h3>Contactez-nous</h3>
    
    <!-- Adresse complète -->
    <div class="address-block">
        {!! full_address_html() !!}
    </div>
    
    <!-- QR Code vers Google Maps -->
    <div class="qr-code">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode('https://www.google.com/maps/search/?api=1&query=' . address_for_maps()) }}" 
             alt="QR Code pour l'adresse">
        <p class="text-sm text-gray-600">Scannez pour ouvrir dans Maps</p>
    </div>
</div>
```

## 📝 Notes Importantes

- Les helpers utilisent les settings de l'entreprise stockés en base de données
- Les valeurs sont mises en cache pour de meilleures performances
- Si un setting n'existe pas, une valeur par défaut est retournée
- Les fonctions sont sécurisées contre les injections XSS (`e()` pour échapper le HTML)

