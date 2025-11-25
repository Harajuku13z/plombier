<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test - Plombier Versailles</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 60px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            text-align: center;
        }
        h1 {
            font-size: 2.5em;
            color: #2d3748;
            margin-bottom: 20px;
        }
        .status {
            background: #48bb78;
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            display: inline-block;
            margin: 20px 0;
            font-weight: bold;
        }
        p {
            color: #4a5568;
            line-height: 1.8;
            margin: 15px 0;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 15px 40px;
            border-radius: 10px;
            text-decoration: none;
            margin: 10px;
            transition: all 0.3s;
            font-weight: bold;
        }
        .button:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .button.secondary {
            background: #ed8936;
        }
        .button.secondary:hover {
            background: #dd6b20;
        }
        .info-box {
            background: #ebf8ff;
            border-left: 4px solid #4299e1;
            padding: 20px;
            margin: 30px 0;
            text-align: left;
            border-radius: 5px;
        }
        .info-box strong {
            color: #2c5282;
        }
        ul {
            text-align: left;
            margin: 15px 0 15px 30px;
        }
        li {
            margin: 8px 0;
            color: #4a5568;
        }
        .emoji {
            font-size: 3em;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="emoji">🔧</div>
        <h1>Plombier Versailles</h1>
        <div class="status">✅ Site Fonctionnel</div>
        
        <p><strong>Cette page de test s'affiche correctement !</strong></p>
        
        <div class="info-box">
            <strong>📋 Diagnostic :</strong>
            <p>Si vous voyez cette page, cela signifie que :</p>
            <ul>
                <li>✅ PHP fonctionne correctement</li>
                <li>✅ Le serveur web répond</li>
                <li>❌ Le problème vient du <strong>cache des vues Blade</strong></li>
            </ul>
        </div>
        
        <p><strong>Solution :</strong> Videz les caches Laravel</p>
        
        <a href="/force-clear-cache.php" class="button">🧹 Vider les Caches</a>
        <a href="/" class="button secondary">🏠 Retour Accueil</a>
        
        <p style="margin-top: 30px; font-size: 0.9em; color: #a0aec0;">
            <?php echo date('Y-m-d H:i:s'); ?> - Test de connectivité
        </p>
    </div>
</body>
</html>

