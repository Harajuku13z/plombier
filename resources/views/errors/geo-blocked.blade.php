<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Non Disponible</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl mx-auto text-center">
        <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12">
            <div class="w-24 h-24 mx-auto mb-6 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-globe-europe text-5xl text-blue-600"></i>
            </div>
            
            <h1 class="text-4xl font-black text-gray-900 mb-4">
                Service Non Disponible
            </h1>
            
            <p class="text-xl text-gray-600 mb-8">
                Notre simulateur de devis est réservé aux clients basés en France.
            </p>
            
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-lg mb-8 text-left">
                <p class="text-gray-800 font-semibold mb-2">
                    <i class="fas fa-map-marked-alt text-yellow-600 mr-2"></i>
                    Zone de Service
                </p>
                <p class="text-gray-700 text-sm">
                    Nous intervenons uniquement en France métropolitaine et dans les DOM-TOM. 
                    Si vous êtes en France et voyez ce message, contactez-nous directement.
                </p>
            </div>
            
            <div class="space-y-4">
                <a href="tel:{{ setting('company_phone', '07 86 48 65 39') }}" 
                   class="inline-flex items-center gap-3 bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-full font-bold text-lg shadow-lg transition">
                    <i class="fas fa-phone-alt"></i>
                    <span>Nous Appeler</span>
                </a>
                
                <div>
                    <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                        <i class="fas fa-home mr-2"></i>
                        Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
        
        <p class="text-gray-500 text-sm mt-8">
            Code: GEO-403 • {{ request()->ip() }}
        </p>
    </div>
</body>
</html>

