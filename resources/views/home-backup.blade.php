@extends('layouts.app')

@section('title', 'Plombier Versailles 78 | Artisan Expert en Plomberie Yvelines | Dépannage Urgence 24h/7')
@section('description', 'Plombier professionnel à Versailles (78) et Yvelines. Dépannage urgence 24h/24, installation sanitaire, réparation fuite, débouchage canalisation. Devis gratuit ☎️ ' . setting('company_phone', '07 86 48 65 39'))

@section('content')
<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px;">
    <div style="background: white; padding: 60px; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 800px; text-align: center;">
        <h1 style="font-size: 3em; color: #2d3748; margin-bottom: 20px;">🔧 Plombier Versailles 78</h1>
        <p style="font-size: 1.5em; color: #4a5568; margin: 20px 0;">Votre plombier professionnel dans les Yvelines</p>
        
        <div style="background: #48bb78; color: white; padding: 15px 30px; border-radius: 50px; display: inline-block; margin: 30px 0; font-weight: bold;">
            ✅ Site en Maintenance - Version Temporaire
        </div>
        
        <p style="color: #718096; margin: 30px 0;">
            Cette page temporaire confirme que votre serveur fonctionne correctement.<br>
            Le problème vient du cache des vues Blade qui n'a pas été vidé.
        </p>
        
        <div style="margin: 40px 0;">
            <a href="tel:{{ setting('company_phone', '07 86 48 65 39') }}" style="display: inline-block; background: #667eea; color: white; padding: 20px 40px; border-radius: 10px; text-decoration: none; font-size: 1.2em; margin: 10px;">
                📞 {{ setting('company_phone', '07 86 48 65 39') }}
            </a>
            <a href="/simulateur-plomberie" style="display: inline-block; background: #ed8936; color: white; padding: 20px 40px; border-radius: 10px; text-decoration: none; font-size: 1.2em; margin: 10px;">
                🧮 Devis Gratuit
            </a>
        </div>
        
        <div style="background: #ebf8ff; border-left: 4px solid #4299e1; padding: 20px; margin: 30px 0; text-align: left; border-radius: 5px;">
            <strong style="color: #2c5282;">💡 Pour l'administrateur :</strong>
            <ol style="margin: 15px 0 0 20px; color: #4a5568;">
                <li>Ce fichier est home-backup.blade.php (version simplifiée)</li>
                <li>Accédez à : <a href="/force-clear-cache.php">force-clear-cache.php</a></li>
                <li>Attendez 2-3 minutes pour expiration cache OPcache</li>
                <li>Ou redémarrez PHP-FPM si vous avez accès SSH</li>
            </ol>
        </div>
        
        <p style="color: #a0aec0; font-size: 0.9em; margin-top: 40px;">
            {{ date('Y-m-d H:i:s') }} - Version de secours active
        </p>
    </div>
</div>
@endsection

