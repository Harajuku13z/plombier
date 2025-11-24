# Guide - Formulaire de Soumissions et Renvoi d'Email Admin

## 📋 Type de Formulaire

Le formulaire accessible via `https://plombier-versailles78.fr/admin/submissions` est un **SIMULATEUR DE PRIX** (et non un simple formulaire de contact).

### Informations Collectées

Le simulateur collecte les informations suivantes :

#### 1. **Informations sur le Projet**
- Type de bien (maison, appartement, etc.)
- Surface du bien (en m²)
- Statut de propriété (propriétaire/locataire)
- Types de travaux :
  - Travaux de plomberie (`roof_work_types`)
  - Travaux de façade (`facade_work_types`)
  - Travaux d'isolation (`isolation_work_types`)

#### 2. **Informations Client**
- Civilité (M./Mme)
- Prénom et Nom
- Email
- Téléphone
- Code postal / Adresse
- Localisation (ville, pays)

#### 3. **Informations Urgence** (si applicable)
- Type d'urgence
- Niveau d'urgence
- Description du problème
- Photos de l'urgence

#### 4. **Données de Tracking**
- Adresse IP
- User Agent
- URL de référence
- Score reCAPTCHA
- Photos du projet

---

## 🚀 Nouvelle Fonctionnalité : Renvoi d'Email à l'Admin

### Ce qui a été ajouté

Un bouton **"Renvoyer email à l'admin"** a été ajouté dans la page de détails de chaque soumission.

### Emplacement du Bouton

Le bouton se trouve dans la section **"Actions rapides"** (sidebar à droite) de la page de détails :

```
/admin/submissions/{id}
```

### Fonctionnement

1. **Cliquez sur le bouton** : "Renvoyer email à l'admin"
2. **Email automatique** : Le système renvoie automatiquement l'email de notification avec tous les détails de la soumission
3. **Message de confirmation** : Un message vert apparaît en haut de la page pour confirmer l'envoi

### Configuration de l'Email Admin

L'email administrateur est récupéré selon cet ordre de priorité :

1. **Settings de la base de données** : `company_email` dans la table `settings`
2. **Fallback** : Email configuré dans `.env` (`MAIL_FROM_ADDRESS`)
3. **Par défaut** : `contact@plombier-versailles78.fr`

### Comment Configurer l'Email Admin

#### Option 1 : Via les Settings (Base de données)

Si vous avez un panneau de configuration dans votre admin :

```sql
INSERT INTO settings (key, value, type, `group`, description) 
VALUES ('company_email', 'votre-email@exemple.com', 'string', 'company', 'Email de contact de l\'entreprise')
ON DUPLICATE KEY UPDATE value = 'votre-email@exemple.com';
```

#### Option 2 : Via le fichier .env

Modifiez votre fichier `.env` :

```env
MAIL_FROM_ADDRESS=contact@plombier-versailles78.fr
MAIL_FROM_NAME="Plombier Versailles"
```

---

## 📧 Email de Notification

L'email envoyé contient :

### Informations Client
- Nom complet
- Téléphone (cliquable)
- Email (cliquable)
- Localisation
- Statut (propriétaire/locataire)

### Détails du Projet
- Type de bien
- Surface
- Types de travaux demandés
- Travaux de plomberie spécifiques
- Travaux de façade (si applicable)
- Travaux d'isolation (si applicable)

### Actions Rapides
- Bouton : **"Voir les Détails Complets"** (lien vers l'admin)
- Bouton : **"Appeler le Client"** (lien tel:)

### Recommandation
Un encadré jaune avec la recommandation : 
> ⚡ **Action recommandée :** Contactez le client dans les 2 heures pour maximiser vos chances de conversion !

---

## 🔧 Fichiers Modifiés

### 1. Controller
**Fichier** : `app/Http/Controllers/AdminController.php`

Nouvelle méthode ajoutée :
```php
public function resendSubmissionEmail($id)
```

Cette méthode :
- Récupère la soumission par son ID
- Récupère l'email admin depuis les settings ou .env
- Envoie l'email de notification
- Retourne un message de succès ou d'erreur

### 2. Routes
**Fichier** : `routes/web.php`

Nouvelle route ajoutée :
```php
Route::post('/submissions/{id}/resend-email', [AdminController::class, 'resendSubmissionEmail'])
    ->name('admin.submission.resend-email');
```

### 3. Vue Détails
**Fichier** : `resources/views/admin/submission-detail.blade.php`

Ajouts :
- **Messages flash** (succès/erreur) en haut de page
- **Bouton "Renvoyer email à l'admin"** dans la section Actions rapides

---

## 🎨 Interface Utilisateur

### Messages de Confirmation

#### Succès
```
✅ Email renvoyé avec succès à contact@plombier-versailles78.fr
```
- Fond vert
- Bordure verte
- Icône de fermeture

#### Erreur
```
❌ Erreur lors de l'envoi de l'email : [détails de l'erreur]
```
- Fond rouge
- Bordure rouge
- Icône de fermeture

### Bouton

Le bouton a le style suivant :
- **Couleur** : Violet (purple-600)
- **Hover** : Violet foncé (purple-700)
- **Icône** : Paper plane (✈️)
- **Texte** : "Renvoyer email à l'admin"
- **Largeur** : 100% de la sidebar

---

## 🧪 Tests Recommandés

### Test 1 : Email Configuré
1. Configurez l'email admin dans les settings ou .env
2. Allez sur une soumission : `/admin/submissions/1`
3. Cliquez sur "Renvoyer email à l'admin"
4. Vérifiez que l'email est bien reçu

### Test 2 : Email Non Configuré
1. Supprimez ou laissez vide l'email admin
2. Cliquez sur "Renvoyer email à l'admin"
3. Vérifiez le message d'erreur approprié

### Test 3 : Erreur d'Envoi
1. Configurez mal les paramètres SMTP
2. Tentez d'envoyer un email
3. Vérifiez que l'erreur est bien loggée et affichée

---

## 📊 Logs

En cas d'erreur, les informations suivantes sont loggées dans `storage/logs/laravel.log` :

```php
\Log::error('Erreur lors du renvoi de l\'email de soumission', [
    'error' => $e->getMessage(),
    'submission_id' => $id,
    'trace' => $e->getTraceAsString()
]);
```

---

## 🔒 Sécurité

- ✅ La route est protégée par le middleware admin
- ✅ Utilisation de `@csrf` dans le formulaire
- ✅ Validation de l'ID de soumission avec `findOrFail()`
- ✅ Gestion des erreurs avec try-catch
- ✅ Messages d'erreur sécurisés (pas de détails sensibles exposés au client)

---

## 🎯 Résumé

### Type de Formulaire
**SIMULATEUR DE PRIX** pour des travaux de plomberie, façade et isolation.

### Nouvelle Fonctionnalité
Bouton **"Renvoyer email à l'admin"** dans la page de détails des soumissions (`/admin/submissions/{id}`)

### Bénéfices
- Permet de renvoyer facilement l'email de notification si l'email initial a été perdu
- Utile pour faire un suivi ou partager les informations avec d'autres membres de l'équipe
- Interface simple avec feedback visuel immédiat

---

## 📞 Support

En cas de problème, vérifiez :

1. **Configuration Email** : `.env` avec `MAIL_MAILER`, `MAIL_HOST`, etc.
2. **Email Admin** : Settings database ou `.env`
3. **Logs** : `storage/logs/laravel.log`
4. **Permissions** : Le serveur peut-il envoyer des emails ?

---

*Dernière mise à jour : 24 novembre 2025*

