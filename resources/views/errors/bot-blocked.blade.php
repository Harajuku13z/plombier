<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès Refusé</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-red-50 to-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl mx-auto text-center">
        <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12">
            <div class="w-24 h-24 mx-auto mb-6 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-robot text-5xl text-red-600"></i>
            </div>
            
            <h1 class="text-4xl font-black text-gray-900 mb-4">
                Accès Refusé
            </h1>
            
            <p class="text-xl text-gray-600 mb-8">
                L'accès automatisé au simulateur n'est pas autorisé.
            </p>
            
            <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg mb-8 text-left">
                <p class="text-gray-800 font-semibold mb-2">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Vous êtes un utilisateur réel ?
                </p>
                <p class="text-gray-700 text-sm">
                    Si vous pensez qu'il s'agit d'une erreur, veuillez nous contacter directement par téléphone.
                </p>
            </div>
            
            <div class="space-y-4">
                <a href="tel:{{ setting('company_phone', '07 86 48 65 39') }}" 
                   class="inline-flex items-center gap-3 bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-full font-bold text-lg shadow-lg transition">
                    <i class="fas fa-phone-alt"></i>
                    <span>{{ setting('company_phone', '07 86 48 65 39') }}</span>
                </a>
                
                <div>
                    <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                        <i class="fas fa-home mr-2"></i>
                        Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

