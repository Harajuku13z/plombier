# 🧹 Guide Complet : Vider les Caches Laravel

## Méthode 1 : Script Web Automatique (RECOMMANDÉ)

**URL :** https://plombier-versailles78.fr/clear-all-cache.php

✅ Vide tous les caches en un clic
✅ S'auto-supprime après exécution
✅ Aucune commande requise

---

## Méthode 2 : Commandes Artisan (Via SSH)

Connectez-vous en SSH à votre serveur, puis :

```bash
cd /path/to/your/project

# Vider TOUS les caches (recommandé)
php artisan optimize:clear

# OU vider cache par cache individuellement :

# 1. Cache des vues Blade compilées
php artisan view:clear

# 2. Cache de configuration
php artisan config:clear

# 3. Cache des routes
php artisan route:clear

# 4. Cache de l'application
php artisan cache:clear

# 5. Recompiler les fichiers de configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Méthode 3 : Manuellement via FTP/Gestionnaire Fichiers

Si vous n'avez pas SSH, supprimez ces dossiers via FTP :

```
storage/framework/views/*
storage/framework/cache/data/*
bootstrap/cache/*.php (SAUF .gitignore)
```

⚠️ **Ne supprimez PAS les dossiers eux-mêmes, seulement leur contenu !**

---

## Méthode 4 : Redémarrer PHP-FPM (Si accès root)

Pour vider OPcache PHP complètement :

```bash
# Selon votre système :
sudo systemctl restart php8.2-fpm
# OU
sudo service php-fpm restart
# OU
sudo /etc/init.d/php-fpm restart
```

---

## 🔧 Commandes Utiles Supplémentaires

```bash
# Voir l'état des caches
php artisan about

# Recompiler les classes Laravel
php artisan optimize

# Vider le cache et recompiler
php artisan optimize:clear && php artisan optimize

# Recréer le fichier autoload
composer dump-autoload
```

---

## 🐛 En cas de problème persistant

Si le site reste en erreur après avoir vidé les caches :

```bash
# 1. Vérifier les permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache

# 2. Regénérer la clé d'application (si nécessaire)
php artisan key:generate

# 3. Vérifier les logs
tail -f storage/logs/laravel.log
```

---

## 📱 Pour Votre Cas Spécifique

**Problème actuel :** ParseError sur home.blade.php

**Solution immédiate :**
1. Accédez à : https://plombier-versailles78.fr/clear-all-cache.php
2. Attendez le message "All caches cleared successfully!"
3. Rechargez : https://plombier-versailles78.fr

**Si ça ne marche pas, en SSH :**
```bash
cd /var/www/plombier-versailles78.fr  # (ou votre chemin)
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
chmod -R 775 storage
```

---

## ⚡ Cache OPcache (Important !)

Le cache OPcache PHP garde les fichiers PHP compilés en mémoire.

**Option 1 : Via le script web**
- Déjà inclus dans clear-all-cache.php ✅

**Option 2 : Redémarrer PHP-FPM**
```bash
sudo systemctl restart php8.2-fpm
```

**Option 3 : Attendre 60 secondes**
- OPcache expire automatiquement après un certain temps

---

## 📋 Checklist de Dépannage

- [ ] Accéder à clear-all-cache.php
- [ ] Vérifier le message de succès
- [ ] Recharger la page d'accueil
- [ ] Si erreur persiste : `php artisan view:clear` en SSH
- [ ] Si toujours erreur : Redémarrer PHP-FPM
- [ ] Vérifier les logs : `storage/logs/laravel.log`

---

## 🎯 Commande Ultime (Tout Vider)

```bash
# La commande magique qui vide TOUT
php artisan optimize:clear && \
php artisan view:clear && \
php artisan cache:clear && \
php artisan config:clear && \
php artisan route:clear && \
composer dump-autoload && \
chmod -R 775 storage bootstrap/cache && \
echo "✅ Tous les caches vidés !"
```

---

## 🔐 Pour Production (Important)

Après avoir vidé les caches, recompilez pour la performance :

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

⚠️ **Ne JAMAIS faire `config:cache` en développement avec .env !**

---

**Maintenant, allez vider ces caches ! 🚀**
