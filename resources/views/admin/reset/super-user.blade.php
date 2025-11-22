<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification Super User - Réinitialisation Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-purple-600 via-purple-700 to-indigo-900 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <div class="text-center mb-6">
                    <div class="h-16 w-16 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-shield-alt text-3xl text-purple-600"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Accès Super User</h1>
                    <p class="text-gray-600 text-sm">Veuillez entrer le code super user pour accéder à la réinitialisation</p>
                </div>

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.reset.verify-super-user') }}">
                    @csrf
                    
                    <div class="mb-6">
                        <label for="super_user_code" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-key mr-2"></i>Code Super User
                        </label>
                        <input 
                            type="password" 
                            id="super_user_code" 
                            name="super_user_code" 
                            required
                            autofocus
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                            placeholder="Entrez le code super user"
                        >
                    </div>

                    <button 
                        type="submit" 
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                    >
                        <i class="fas fa-unlock mr-2"></i>
                        Vérifier
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('admin.login') }}" class="text-sm text-gray-600 hover:text-purple-600">
                        <i class="fas fa-arrow-left mr-1"></i> Retour à la connexion
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

