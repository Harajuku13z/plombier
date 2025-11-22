# 📧 Diagnostic - Emails Non Reçus

## 🔍 Étape 1 : Vérifier les Logs

```bash
ssh utilisateur@serveur
cd /var/www/plombier

# Voir les 50 dernières lignes des logs
tail -50 storage/logs/laravel.log

# Filtrer les logs d'email
grep -i "email\|mail" storage/logs/laravel.log | tail -30

# Voir si l'email est configuré
grep "company_email\|Email configuration" storage/logs/laravel.log | tail -10
```

### Ce que vous devriez voir :

✅ **Si tout fonctionne** :
```
Email configuration check
company_email: contact@plombier-versailles78.fr
Sending email to company
✅ Email sent successfully to contact@plombier-versailles78.fr
Sending confirmation email to client
✅ Confirmation email sent to client
```

❌ **Si problème** :
```
⚠️ No company email configured - Skipping email
```

---

## 🔧 Étape 2 : Vérifier la Configuration Email

### Via Tinker :

```bash
php artisan tinker
```

Puis testez :
```php
// 1. Vérifier l'email de l'entreprise
\App\Models\Setting::get('company_email');
// Devrait retourner: "contact@plombier-versailles78.fr"

// 2. Vérifier toutes les config mail
\App\Models\Setting::where('key', 'LIKE', '%mail%')->get();

// 3. Tester l'envoi d'un email simple
\Mail::raw('Test depuis le simulateur', function($m) {
    $m->to('contact@plombier-versailles78.fr')
      ->subject('Test Email');
});

// 4. Vérifier si l'email est parti
echo "Email envoyé !";

exit
```

---

## ⚙️ Étape 3 : Vérifier la Configuration .env

```bash
# Voir la config mail dans .env
cat .env | grep MAIL
```

Devrait contenir :
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.votre-serveur.com
MAIL_PORT=587
MAIL_USERNAME=votre@email.com
MAIL_PASSWORD=votre_mot_de_passe
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="contact@plombier-versailles78.fr"
MAIL_FROM_NAME="Plombier Versailles"
```

---

## 🔧 Étape 4 : Configurer l'Email via l'Admin

1. Aller sur : `https://plombier-versailles78.fr/admin/login`
2. Connexion : `contact@plombier-versailles78.fr` / `Harajuku1993@`
3. Aller dans : **Configuration** (ou `/admin/config`)
4. Section **Email** :
   - Remplir `company_email` : `contact@plombier-versailles78.fr`
   - Configurer SMTP si nécessaire

---

## 📨 Étape 5 : Solutions Alternatives

### Solution 1 : Utiliser un Service d'Email

Si vous n'avez pas de serveur SMTP, utilisez :

**Mailtrap (gratuit pour test)** :
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre_username
MAIL_PASSWORD=votre_password
MAIL_ENCRYPTION=tls
```

**Gmail** :
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre@gmail.com
MAIL_PASSWORD=mot_de_passe_application
MAIL_ENCRYPTION=tls
```

### Solution 2 : Vérifier les Spams

Les emails peuvent être dans les **spams** :
- Vérifiez le dossier spam de `contact@plombier-versailles78.fr`
- Ajoutez l'expéditeur à votre liste blanche

---

## 🧪 Étape 6 : Test Rapide

Créez un fichier `test-email.php` à la racine :

```php
<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Mail;

try {
    $to = 'contact@plombier-versailles78.fr';
    
    Mail::raw('Test depuis le serveur - ' . date('Y-m-d H:i:s'), function($m) use ($to) {
        $m->to($to)->subject('Test Email Plombier');
    });
    
    echo "✅ Email envoyé avec succès à $to\n";
    echo "Vérifiez votre boîte mail (et les spams)\n";
} catch (\Exception $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
    echo "Vérifiez la configuration SMTP dans .env\n";
}
```

Puis exécutez :
```bash
php test-email.php
```

---

## 📝 Résumé

**Problèmes fréquents** :
1. ❌ `company_email` non configuré dans les settings
2. ❌ Configuration SMTP incorrecte dans `.env`
3. ❌ Emails bloqués par le firewall/serveur
4. ❌ Emails dans les spams

**Solutions** :
1. ✅ Configurer `company_email` via l'admin
2. ✅ Vérifier/configurer SMTP dans `.env`
3. ✅ Utiliser un service d'email (Gmail, Mailtrap, etc.)
4. ✅ Vérifier les spams

---

## 🆘 Support

Si rien ne fonctionne, envoyez-moi :
```bash
# Les logs
tail -100 storage/logs/laravel.log | grep -i "email\|mail"

# La config
cat .env | grep MAIL

# Le test tinker
php artisan tinker
\App\Models\Setting::get('company_email');
```

Et je vous aiderai à diagnostiquer ! 🔍

