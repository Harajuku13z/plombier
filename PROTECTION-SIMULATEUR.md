# 🛡️ Protection du Simulateur

## ✅ Protections Activées

Le simulateur de plomberie est maintenant protégé contre :

### 1. 🤖 Bots et Accès Automatisés

**Bloqués automatiquement** :
- Scrapers et crawlers
- Bots malveillants
- Scripts automatisés (curl, wget, python, etc.)
- Navigateurs headless (Selenium, Phantom, etc.)
- User agents suspects ou vides

**Autorisés** :
- Visiteurs humains avec navigateur normal
- Googlebot et autres bots SEO légitimes (pour indexation)

### 2. 🌍 Accès Géographique (France Uniquement)

**Pays Autorisés** :
- 🇫🇷 France métropolitaine
- 🇨🇭 Suisse
- 🏝️ DOM-TOM :
  - Réunion, Guadeloupe, Martinique
  - Guyane, Mayotte
  - Nouvelle-Calédonie, Polynésie française
  - Saint-Pierre-et-Miquelon, Saint-Barthélemy, Saint-Martin
  - Wallis-et-Futuna

**Bloqués** :
- Tous les autres pays

---

## 📋 Pages Protégées

✅ **Simulateur complet** :
- `/simulateur-plomberie`
- Toutes les étapes (work-type, urgency, property-type, photos, contact)
- Page de succès

---

## 🚫 Pages d'Erreur

### Bot Détecté
**URL** : Affichage automatique
**Message** : "Accès Refusé - L'accès automatisé au simulateur n'est pas autorisé"
**Action** : Bouton pour appeler directement

### Pays Non Autorisé
**URL** : Affichage automatique
**Message** : "Service Non Disponible - Notre simulateur est réservé aux clients basés en France"
**Action** : Bouton pour appeler + retour accueil

---

## 🔍 Détection des Bots

Le middleware détecte :

1. **User Agent** contenant :
   - bot, crawl, spider, scraper
   - curl, wget, python, java
   - headless, phantom, selenium
   - etc.

2. **User Agent vide** (très suspect)

3. **Headers suspects** :
   - Requêtes AJAX sans Referer
   - Absence de headers standards

---

## 🌐 Détection Géographique

Utilise le service `IpGeolocationService` existant :
- Détection précise par IP
- Fallback sur ip-api.com
- Logs de toutes les tentatives

---

## 📊 Logs

Tous les blocages sont loggés dans `storage/logs/laravel.log` :

```bash
# Voir les blocages
grep "blocked\|Bot detected\|Non-France" storage/logs/laravel.log | tail -20
```

**Logs enregistrés** :
- IP bloquée
- User agent
- Pays détecté
- URL tentée
- Timestamp

---

## ⚙️ Configuration

### Activer/Désactiver le Blocage Géographique

```bash
php artisan tinker
```

```php
// Activer le blocage France uniquement
\App\Models\Setting::set('block_non_france', true);

// Désactiver (autoriser tous les pays)
\App\Models\Setting::set('block_non_france', false);

// Vérifier
\App\Models\Setting::get('block_non_france');

exit
```

### Autoriser des IPs Spécifiques

Modifiez `app/Http/Middleware/BlockNonFranceAndBots.php` :

```php
// IPs locales toujours autorisées
if (in_array($ipAddress, ['127.0.0.1', '::1', 'localhost', 'VOTRE_IP'])) {
    return true;
}
```

---

## 🧪 Tester la Protection

### Test 1 : Bot Detection

```bash
# Depuis un terminal
curl https://plombier-versailles78.fr/simulateur-plomberie

# Devrait afficher: "Accès Refusé"
```

### Test 2 : Accès Normal

Ouvrir dans un navigateur :
```
https://plombier-versailles78.fr/simulateur-plomberie
```

✅ Devrait fonctionner normalement

---

## 📈 Statistiques

Pour voir les tentatives bloquées :

```bash
# Nombre de bots bloqués aujourd'hui
grep "Bot detected" storage/logs/laravel.log | grep "$(date +%Y-%m-%d)" | wc -l

# Nombre de blocages géographiques
grep "Non-France access blocked" storage/logs/laravel.log | grep "$(date +%Y-%m-%d)" | wc -l
```

---

## 🔒 Sécurité Renforcée

Le simulateur est maintenant :
- ✅ Protégé contre les bots
- ✅ Réservé à la France et DOM-TOM
- ✅ Logs de toutes les tentatives
- ✅ Pages d'erreur professionnelles
- ✅ Fail-safe (en cas d'erreur de détection, autorise)

---

## 📞 Bypass (Urgences)

Les pages d'erreur affichent toujours :
- Le numéro de téléphone pour appeler directement
- Un bouton "Retour à l'accueil"
- Un message explicatif professionnel

---

**Protection active dès maintenant !** 🛡️

