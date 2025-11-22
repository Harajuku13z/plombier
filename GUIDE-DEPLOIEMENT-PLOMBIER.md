# 🔧 Guide de Déploiement - Plombier Versailles

## ✅ Modifications Effectuées

### 1. Erreur de Connexion Admin (RÉSOLU)
- ✅ Correction de l'erreur Bcrypt
- ✅ Support des mots de passe en clair et hashés
- ✅ Identifiants : `contact@plombier-versailles78.fr` / `Harajuku1993@`

### 2. Transformation Couvreur → Plombier (COMPLET)
- ✅ 315 remplacements dans 65 fichiers
- ✅ Toutes les références "couvreur" remplacées par "plombier"
- ✅ Documentation, code, prompts, templates mis à jour

### 3. Nouveau Simulateur de Plomberie
- ✅ Simulateur dédié à la plomberie (plus de calcul m²)
- ✅ Services adaptés : débouchage, fuite, sanitaires, chauffe-eau, salle de bain, chauffage, canalisations
- ✅ 4 étapes simples :
  1. Type de travaux
  2. Niveau d'urgence
  3. Type de bien
  4. Coordonnées
- ✅ Email automatique avec récapitulatif

### 4. Page SOS URGENCE 24/7
- ✅ Page dédiée `/urgence`
- ✅ Formulaire d'urgence avec upload photos
- ✅ Affichage dynamique ville + département
- ✅ Email urgent avec alerte rouge
- ✅ Services d'urgence listés

### 5. Page d'Accueil Redesignée
- ✅ Section "Urgence Plombier" avec animation
- ✅ Affichage ville + département dynamique
- ✅ Section "Comment Ça Marche" (4 étapes)
- ✅ Design moderne avec gradients
- ✅ CTA optimisés

### 6. Formulaire de Contact
- ✅ Upload de photos ajouté
- ✅ Validation 5MB par image

### 7. Page Secrète Réinitialisation Admin
- ✅ URL : `/admin/reset/super-user`
- ✅ Code super user : `elizo`
- ✅ Modification email + mot de passe

---

## 📦 Déploiement sur le Serveur

### Étape 1 : Connexion SSH

```bash
ssh utilisateur@votre-serveur.com
```

### Étape 2 : Aller dans le dossier du projet

```bash
cd /var/www/plombier
# ou
cd /chemin/vers/votre/projet
```

### Étape 3 : Récupérer les modifications

```bash
# Stash les modifications locales si nécessaire
git stash

# Pull les modifications
git pull origin main

# Réappliquer les modifications locales si nécessaire
git stash pop
```

### Étape 4 : Vider les caches

```bash
# Vider tous les caches Laravel
php artisan optimize:clear

# Ou individuellement
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Étape 5 : Vérifier les permissions

```bash
# Permissions storage et bootstrap/cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Étape 6 : Test

Ouvrir dans le navigateur :
- Page d'accueil : `https://plombier-versailles78.fr/`
- Simulateur : `https://plombier-versailles78.fr/simulateur-plomberie`
- SOS Urgence : `https://plombier-versailles78.fr/urgence`
- Connexion admin : `https://plombier-versailles78.fr/admin/login`

---

## 🔗 Nouvelles URLs

| Page | URL | Description |
|------|-----|-------------|
| **Accueil** | `/` | Page d'accueil redesignée avec urgence |
| **Simulateur** | `/simulateur-plomberie` | Nouveau simulateur de plomberie |
| **Ancien formulaire** | `/form/propertyType` | Redirige vers nouveau simulateur |
| **SOS Urgence** | `/urgence` | Page urgence 24/7 |
| **Admin Login** | `/admin/login` | Connexion admin |
| **Reset Admin** | `/admin/reset/super-user` | Réinitialisation mot de passe (code: elizo) |

---

## 🔑 Identifiants Admin

### Connexion Normale
- URL : `https://plombier-versailles78.fr/admin/login`
- Email : `contact@plombier-versailles78.fr`
- Mot de passe : `Harajuku1993@`

### Réinitialisation (Page Secrète)
- URL : `https://plombier-versailles78.fr/admin/reset/super-user`
- Code super user : `elizo`
- Permet de changer email et mot de passe

---

## 📧 Configuration Email (si nécessaire)

Si les emails ne partent pas, vérifier dans `/admin/config` :
- Paramètres SMTP
- Clés API (si configurées)

---

## ⚡ Script de Déploiement Rapide

```bash
#!/bin/bash
# Script de déploiement complet

cd /var/www/plombier  # ⚠️ Adapter le chemin

echo "🔄 Récupération des modifications..."
git pull origin main

echo "🧹 Nettoyage des caches..."
php artisan optimize:clear

echo "🔐 Vérification des permissions..."
chmod -R 775 storage bootstrap/cache

echo "✅ Déploiement terminé !"
echo "🌐 Testez : https://plombier-versailles78.fr/"
```

Sauvegarder ce script dans `deploy.sh` et exécuter :
```bash
chmod +x deploy.sh
./deploy.sh
```

---

## 🎯 Fonctionnalités Principales

### 1. Simulateur de Plomberie
- Pas de calcul m² complexe
- Description simple des besoins
- Types de travaux spécifiques plomberie
- Email automatique au plombier

### 2. Page SOS URGENCE
- Formulaire urgence avec photos
- Email urgent (rouge) avec alerte
- Affichage ville + département automatique
- Liste des services d'urgence

### 3. Page d'Accueil
- Section urgence animée
- "Comment Ça Marche" (4 étapes)
- Design moderne et responsive
- Trust indicators

---

## 🆘 Support

En cas de problème :

1. **Vérifier les logs** :
```bash
tail -f storage/logs/laravel.log
```

2. **Problème de connexion admin** :
   - Utiliser `/admin/reset/super-user` avec code `elizo`

3. **Problème d'email** :
   - Vérifier configuration SMTP dans `/admin/config`

---

## 📝 Notes Importantes

- ✅ Toutes les modifications sont sur GitHub
- ✅ Tous les TODOs complétés
- ✅ Compatibilité avec l'ancien système maintenue
- ✅ Ancien formulaire redirige automatiquement vers nouveau simulateur
- ✅ Backup de l'ancienne homepage : `resources/views/home-old-backup.blade.php`

---

**Dernière mise à jour** : {{ date('d/m/Y H:i') }}  
**Dépôt GitHub** : https://github.com/Harajuku13z/plombier.git

