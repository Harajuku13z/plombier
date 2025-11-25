# 📧 Guide de Configuration Email

## 🚨 Problème : Emails en Spam

Si vos emails arrivent en **spam**, suivez ce guide pour corriger la configuration.

---

## ✅ Étape 1 : Vérifier les Settings

### 1. Activer les emails

Allez dans la base de données `settings` et vérifiez/ajoutez :

```sql
INSERT INTO settings (name, value) VALUES ('email_enabled', '1')
ON DUPLICATE KEY UPDATE value = '1';
```

### 2. Configurer l'email admin

```sql
-- Email qui reçoit les notifications
INSERT INTO settings (name, value) VALUES 
    ('admin_notification_email', 'votre-email@plombier-versailles78.fr')
ON DUPLICATE KEY UPDATE value = 'votre-email@plombier-versailles78.fr';

-- Email de l'entreprise
INSERT INTO settings (name, value) VALUES 
    ('company_email', 'contact@plombier-versailles78.fr')
ON DUPLICATE KEY UPDATE value = 'contact@plombier-versailles78.fr';
```

---

## 📮 Étape 2 : Configuration SMTP (dans Settings)

### Paramètres SMTP Hostinger

```sql
-- Configuration SMTP
INSERT INTO settings (name, value) VALUES 
    ('mail_host', 'smtp.hostinger.com'),
    ('mail_port', '587'),
    ('mail_username', 'contact@plombier-versailles78.fr'),
    ('mail_password', 'VOTRE_MOT_DE_PASSE_EMAIL'),
    ('mail_encryption', 'tls'),
    ('mail_from_address', 'contact@plombier-versailles78.fr'),
    ('mail_from_name', 'Plombier Versailles 78')
ON DUPLICATE KEY UPDATE 
    value = VALUES(value);
```

### ⚠️ Important

- Utilisez **TLS** (port 587) ou **SSL** (port 465)
- N'utilisez **PAS** de mot de passe avec caractères spéciaux non échappés
- Le `mail_username` doit être l'adresse email complète

---

## 🛡️ Étape 3 : Configuration DNS Anti-Spam

Pour **éviter que vos emails aillent en spam**, vous devez configurer les enregistrements DNS.

### 1. SPF (Sender Policy Framework)

Ajoutez un enregistrement **TXT** sur votre domaine :

```
Type: TXT
Nom: @
Valeur: v=spf1 include:_spf.hostinger.com ~all
```

### 2. DKIM (DomainKeys Identified Mail)

Hostinger génère automatiquement les clés DKIM. Activez-le dans votre panneau Hostinger :

1. Allez dans **Emails** > **Authentification Email**
2. Activez **DKIM**
3. Copiez l'enregistrement DNS fourni
4. Ajoutez-le dans votre zone DNS

Exemple :
```
Type: TXT
Nom: default._domainkey
Valeur: v=DKIM1; k=rsa; p=MIGfMA0GCSqGSIb3DQEBAQUAA4...
```

### 3. DMARC (Domain-based Message Authentication)

Ajoutez un enregistrement **TXT** :

```
Type: TXT
Nom: _dmarc
Valeur: v=DMARC1; p=quarantine; rua=mailto:contact@plombier-versailles78.fr
```

**Options DMARC :**
- `p=none` : Mode surveillance uniquement
- `p=quarantine` : Mettre en quarantaine les emails suspects
- `p=reject` : Rejeter les emails suspects (recommandé après test)

---

## 🔍 Étape 4 : Vérifier la Configuration

### 1. Vérifier les logs Laravel

```bash
tail -f storage/logs/laravel.log
```

Cherchez :
- ✅ `Email admin envoyé avec succès à`
- ✅ `Photo attached successfully`
- ⚠️ `Email désactivé, pas d'envoi`
- ❌ `Erreur envoi email admin`

### 2. Tester l'envoi

1. Allez sur **https://plombier-versailles78.fr/simulateur-plomberie**
2. Remplissez le formulaire avec une photo
3. Vérifiez les logs :

```bash
# Voir les 50 dernières lignes
tail -n 50 storage/logs/laravel.log | grep -i "email\|photo"
```

### 3. Vérifier la réception

- ✅ Email reçu dans **Boîte de réception**
- ✅ Photos en **pièces jointes**
- ✅ Photos affichées dans le **corps de l'email**

---

## 📊 Étape 5 : Tester les DNS

### 1. Vérifier SPF

```bash
nslookup -type=txt plombier-versailles78.fr
```

Doit contenir : `v=spf1 include:_spf.hostinger.com ~all`

### 2. Vérifier DKIM

```bash
nslookup -type=txt default._domainkey.plombier-versailles78.fr
```

Doit contenir : `v=DKIM1; k=rsa; p=...`

### 3. Vérifier DMARC

```bash
nslookup -type=txt _dmarc.plombier-versailles78.fr
```

Doit contenir : `v=DMARC1; p=quarantine;...`

### 4. Tester avec Mail-Tester

1. Allez sur **https://www.mail-tester.com/**
2. Copiez l'adresse email de test
3. Envoyez un email à cette adresse via votre formulaire
4. Vérifiez le score (objectif : **10/10**)

---

## 🐛 Dépannage

### Problème : Email désactivé

**Symptôme :** Dans les logs : `Email désactivé, pas d'envoi`

**Solution :**
```sql
UPDATE settings SET value = '1' WHERE name = 'email_enabled';
```

### Problème : Pas d'email admin configuré

**Symptôme :** Dans les logs : `Pas d'email admin configuré`

**Solution :**
```sql
INSERT INTO settings (name, value) VALUES 
    ('admin_notification_email', 'votre-email@plombier-versailles78.fr')
ON DUPLICATE KEY UPDATE value = 'votre-email@plombier-versailles78.fr';
```

### Problème : Photos non attachées

**Symptôme :** Email reçu mais sans pièces jointes

**Solution :**
1. Vérifiez que les photos sont bien enregistrées :
   ```bash
   ls -la storage/app/public/submissions/
   ```

2. Vérifiez les logs :
   ```bash
   grep "Photo attached\|Attachment error" storage/logs/laravel.log
   ```

3. Vérifiez les permissions :
   ```bash
   chmod -R 775 storage/app/public/submissions/
   chown -R www-data:www-data storage/app/public/submissions/
   ```

### Problème : Email en spam

**Causes possibles :**
- ❌ SPF/DKIM/DMARC non configurés
- ❌ Utilisation d'une IP blacklistée
- ❌ Contenu de l'email suspect (trop de liens, mots-clés spam)
- ❌ Domaine récent sans réputation

**Solutions :**
1. ✅ Configurer SPF/DKIM/DMARC (voir Étape 3)
2. ✅ Vérifier IP sur https://mxtoolbox.com/blacklists.aspx
3. ✅ Utiliser un domaine professionnel (pas Gmail perso)
4. ✅ Commencer avec volume faible d'emails
5. ✅ Demander aux destinataires de marquer "Pas spam"

### Problème : Erreur SMTP

**Symptôme :** `Erreur envoi email admin: Authentication failed`

**Solution :**
1. Vérifiez les identifiants SMTP
2. Vérifiez que le compte email existe sur Hostinger
3. Vérifiez que le mot de passe est correct
4. Testez la connexion manuellement :

```bash
telnet smtp.hostinger.com 587
EHLO plombier-versailles78.fr
STARTTLS
AUTH LOGIN
# Entrez username en base64
# Entrez password en base64
```

---

## 📝 Checklist Finale

Avant de considérer la configuration terminée :

- [ ] ✅ `email_enabled` = `1` dans settings
- [ ] ✅ `admin_notification_email` configuré
- [ ] ✅ Paramètres SMTP corrects (host, port, username, password)
- [ ] ✅ SPF configuré sur DNS
- [ ] ✅ DKIM activé et configuré
- [ ] ✅ DMARC configuré sur DNS
- [ ] ✅ Test d'envoi réussi
- [ ] ✅ Email reçu dans boîte de réception (pas spam)
- [ ] ✅ Photos en pièces jointes
- [ ] ✅ Photos affichées dans corps email
- [ ] ✅ Score Mail-Tester > 8/10
- [ ] ✅ Logs sans erreur

---

## 🆘 Support

Si après toutes ces étapes les emails vont toujours en spam :

1. **Contactez Hostinger** : Vérifiez que votre IP n'est pas blacklistée
2. **Vérifiez la réputation** : https://www.senderscore.org/
3. **Testez avec Gmail Postmaster** : https://postmaster.google.com/
4. **Considérez un service SMTP tiers** : SendGrid, Mailgun, Amazon SES

---

## 📚 Ressources

- **Mail-Tester** : https://www.mail-tester.com/
- **MXToolbox** : https://mxtoolbox.com/
- **SPF Record Generator** : https://www.spfwizard.net/
- **DMARC Generator** : https://www.kitterman.com/dmarc/assistant.html
- **Hostinger Documentation** : https://support.hostinger.com/

---

## ✅ Résumé

Pour que vos emails arrivent en **boîte de réception** avec les **photos en pièces jointes** :

1. ✅ Activez les emails : `email_enabled = 1`
2. ✅ Configurez SMTP correctement
3. ✅ Ajoutez SPF/DKIM/DMARC sur DNS
4. ✅ Testez avec Mail-Tester
5. ✅ Vérifiez les logs Laravel
6. ✅ Demandez aux destinataires de marquer "Pas spam" les premiers emails

🎉 **Bonne configuration !**

