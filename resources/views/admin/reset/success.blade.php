<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation Réussie</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-green-600 via-green-700 to-emerald-900 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8 text-center">
                <div class="h-20 w-20 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-5xl text-green-600"></i>
                </div>
                
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Réinitialisation Réussie !</h1>
                <p class="text-gray-600 mb-6">Le mot de passe administrateur a été mis à jour avec succès.</p>

                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-600 mb-2">Vous pouvez maintenant vous connecter avec les nouveaux identifiants.</p>
                </div>

                <a 
                    href="{{ route('admin.login') }}" 
                    class="inline-block w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Aller à la page de connexion
                </a>
            </div>
        </div>
    </div>
</body>
</html>

