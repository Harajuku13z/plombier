@extends('layouts.admin')

@section('title', 'Détails de la soumission #' . $submission->id)

@section('content')
<div class="p-6">
    <!-- Messages Flash -->
    @if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{!! session('success') !!}</span>
        <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{!! session('error') !!}</span>
        <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('admin.submissions') }}" class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-block">
                <i class="fas fa-arrow-left mr-1"></i> Retour à la liste
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Soumission #{{ $submission->id }}</h1>
            <p class="text-gray-600 mt-1">Créée le {{ $submission->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <div>
            <span class="px-4 py-2 rounded-lg font-semibold text-sm
                {{ $submission->status === 'COMPLETED' ? 'bg-green-100 text-green-800' : 
                   ($submission->status === 'ABANDONED' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                {{ $submission->status === 'COMPLETED' ? 'Complétée' : 
                   ($submission->status === 'ABANDONED' ? 'Abandonnée' : 'En cours') }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations personnelles -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-user mr-2 text-blue-500"></i>Informations de la Soumission
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- ID et Type -->
                        @if($submission->id)
                        <div>
                            <label class="text-sm font-medium text-gray-500">ID Soumission</label>
                            <p class="mt-1 text-lg font-medium text-gray-900">#{{ $submission->id }}</p>
                        </div>
                        @endif
                        
                        @if($submission->is_emergency || $submission->work_type)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Type de formulaire</label>
                            <p class="mt-1 text-lg font-medium text-gray-900">
                                @if($submission->is_emergency)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-sm font-medium bg-red-100 text-red-800">
                                        🚨 URGENCE
                                    </span>
                                @else
                                    {{ $submission->work_type ?? 'Simulateur' }}
                                @endif
                            </p>
                        </div>
                        @endif

                        <!-- Contact -->
                        @if($submission->gender)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Civilité</label>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $submission->gender }}</p>
                        </div>
                        @endif
                        
                        @if($submission->name || $submission->first_name || $submission->last_name)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Nom complet</label>
                            <p class="mt-1 text-lg font-medium text-gray-900">
                                {{ $submission->name ?? ($submission->first_name . ' ' . $submission->last_name) }}
                            </p>
                        </div>
                        @endif
                        
                        @if($submission->email)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="mt-1 text-lg text-gray-900">
                                <a href="mailto:{{ $submission->email }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-envelope mr-1"></i>{{ $submission->email }}
                                </a>
                            </p>
                        </div>
                        @endif
                        
                        @if($submission->phone)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Téléphone</label>
                            <p class="mt-1 text-lg text-gray-900">
                                <a href="tel:{{ $submission->phone }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-phone mr-1"></i>{{ $submission->phone }}
                                </a>
                            </p>
                        </div>
                        @endif

                        <!-- Localisation -->
                        @if($submission->postal_code)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Code postal</label>
                            <p class="mt-1 text-lg text-gray-900">{{ $submission->postal_code }}</p>
                        </div>
                        @endif
                        
                        @if($submission->city)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Ville</label>
                            <p class="mt-1 text-lg text-gray-900">{{ $submission->city }}</p>
                        </div>
                        @endif
                        
                        @if($submission->address)
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-500">Adresse complète</label>
                            <p class="mt-1 text-lg text-gray-900">{{ $submission->address }}</p>
                        </div>
                        @endif
                        
                        @if($submission->country)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Pays</label>
                            <p class="mt-1 text-lg text-gray-900">
                                {{ $submission->country }}
                                @if($submission->country_code)
                                    <span class="text-sm text-gray-500">({{ $submission->country_code }})</span>
                                @endif
                            </p>
                        </div>
                        @endif

                        <!-- Projet -->
                        @if($submission->property_type)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Type de bien</label>
                            <p class="mt-1 text-lg font-medium text-gray-900">
                                @php
                                    $propertyTypeLabels = [
                                        'HOUSE' => 'Maison',
                                        'APARTMENT' => 'Appartement',
                                        'COMMERCIAL' => 'Local commercial',
                                        'house' => 'Maison',
                                        'apartment' => 'Appartement',
                                        'commercial' => 'Local commercial',
                                        'other' => 'Autre',
                                    ];
                                    $propertyTypeDisplay = $propertyTypeLabels[$submission->property_type] ?? ucfirst(strtolower($submission->property_type));
                                @endphp
                                {{ $propertyTypeDisplay }}
                            </p>
                        </div>
                        @endif
                        
                        @if($submission->surface)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Surface</label>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $submission->surface }} m²</p>
                        </div>
                        @endif
                        
                        @if($submission->ownership_status)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Statut propriétaire</label>
                            <p class="mt-1 text-lg font-medium text-gray-900">
                                {{ ucfirst(strtolower($submission->ownership_status)) }}
                            </p>
                        </div>
                        @endif

                        <!-- Message/Description -->
                        @if($submission->message)
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-500">Message / Description</label>
                            <div class="mt-1 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $submission->message }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Urgence -->
                        @if($submission->emergency_type)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Type d'urgence</label>
                            <p class="mt-1 text-lg font-medium text-red-900">
                                {{ ucfirst(str_replace('-', ' ', $submission->emergency_type)) }}
                            </p>
                        </div>
                        @endif
                        
                        @if($submission->urgency_level)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Niveau d'urgence</label>
                            <p class="mt-1 text-lg font-medium text-red-900">
                                {{ ucfirst($submission->urgency_level) }}
                            </p>
                        </div>
                        @endif

                        <!-- Statut -->
                        @if($submission->status)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Statut</label>
                            <p class="mt-1">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    {{ $submission->status === 'COMPLETED' ? 'bg-green-100 text-green-800' : 
                                       ($submission->status === 'ABANDONED' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $submission->status }}
                                </span>
                            </p>
                        </div>
                        @endif
                        
                        @if($submission->current_step)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Étape actuelle</label>
                            <p class="mt-1 text-lg text-gray-900">{{ $submission->current_step }}</p>
                        </div>
                        @endif

                        <!-- Tracking -->
                        @if($submission->ip_address)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Adresse IP</label>
                            <p class="mt-1 text-sm font-mono text-gray-700">{{ $submission->ip_address }}</p>
                        </div>
                        @endif
                        
                        @if($submission->recaptcha_score !== null)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Score reCAPTCHA</label>
                            <p class="mt-1 text-lg font-medium {{ $submission->recaptcha_score >= 0.5 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($submission->recaptcha_score, 2) }}
                                <span class="text-xs text-gray-500">
                                    ({{ $submission->recaptcha_score >= 0.5 ? 'Légitime' : 'Suspect' }})
                                </span>
                            </p>
                        </div>
                        @endif
                        
                        @if($submission->referrer_url)
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-500">Page d'origine</label>
                            <p class="mt-1 text-sm">
                                <a href="{{ $submission->referrer_url }}" target="_blank" 
                                   class="text-blue-600 hover:text-blue-800 break-all">
                                    <i class="fas fa-external-link-alt mr-1"></i>{{ Str::limit($submission->referrer_url, 80) }}
                                </a>
                            </p>
                        </div>
                        @endif

                        <!-- Dates -->
                        <div>
                            <label class="text-sm font-medium text-gray-500">Date création</label>
                            <p class="mt-1 text-lg text-gray-900">{{ $submission->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        
                        @if($submission->completed_at)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Date complétion</label>
                            <p class="mt-1 text-lg text-gray-900">{{ $submission->completed_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        @endif
                        
                        @if($submission->abandoned_at)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Date abandon</label>
                            <p class="mt-1 text-lg text-red-600">{{ $submission->abandoned_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @php
                // Fusionner toutes les photos disponibles
                $allPhotos = [];
                
                // 1. Photos du champ 'photos' (urgence ou autres)
                if ($submission->photos && is_array($submission->photos)) {
                    foreach ($submission->photos as $photo) {
                        $allPhotos[] = [
                            'path' => $photo,
                            'type' => 'direct',
                            'source' => 'submission->photos'
                        ];
                    }
                }
                
                // 2. Photos du tracking_data (simulateur)
                if (isset($submission->tracking_data['photos']) && is_array($submission->tracking_data['photos'])) {
                    foreach ($submission->tracking_data['photos'] as $photo) {
                        $allPhotos[] = [
                            'path' => $photo,
                            'type' => 'tracking',
                            'source' => 'tracking_data'
                        ];
                    }
                }
                
                $totalPhotos = count($allPhotos);
            @endphp

            @if($totalPhotos > 0)
            <!-- Photos du formulaire -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-images mr-2 text-indigo-500"></i>Photos associées
                    </h2>
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm font-medium rounded-full">
                        {{ $totalPhotos }} {{ $totalPhotos > 1 ? 'photos' : 'photo' }}
                    </span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($allPhotos as $index => $photoData)
                            @php
                                $photoPath = $photoData['path'];
                                
                                // Nettoyer le chemin et extraire les informations
                                $cleanPath = str_replace('storage/', '', $photoPath);
                                $pathParts = explode('/', $cleanPath);
                                
                                // Chercher l'ID de soumission dans le chemin
                                $fileId = $submission->id; // Par défaut
                                $fileName = end($pathParts);
                                
                                // Si le chemin contient 'submissions/{id}/' ou 'uploads/submissions/{id}/'
                                if (preg_match('/submissions\/(\d+)\//', $cleanPath, $matches)) {
                                    $fileId = $matches[1];
                                }
                                
                                // Utiliser la route media.submission.photo
                                $photoUrl = route('media.submission.photo', ['id' => $fileId, 'file' => $fileName]);
                            @endphp
                            <div class="relative group">
                                <a href="{{ $photoUrl }}" target="_blank" class="block">
                                    <div class="relative overflow-hidden rounded-lg border-2 border-gray-200 hover:border-indigo-400 transition-all duration-300 shadow-md hover:shadow-xl">
                                        <img src="{{ $photoUrl }}" 
                                             alt="Photo {{ $index + 1 }}" 
                                             class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-110"
                                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-48 bg-red-50 border-2 border-red-200 rounded flex flex-col items-center justify-center p-4\'><i class=\'fas fa-exclamation-triangle text-red-500 text-3xl mb-2\'></i><p class=\'text-red-700 text-xs font-medium text-center\'>Image non trouvée</p><p class=\'text-red-500 text-xs text-center mt-1\'>{{ $cleanPath }}</p></div>';" />
                                        
                                        <!-- Overlay au hover -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0 to-black/0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-end pb-3">
                                            <i class="fas fa-search-plus text-white text-2xl mb-1"></i>
                                            <span class="text-white text-xs font-medium">Agrandir</span>
                                        </div>
                                        
                                        <!-- Badge numéro -->
                                        <div class="absolute top-2 left-2 bg-indigo-600 text-white text-xs font-bold px-2 py-1 rounded-full shadow-lg">
                                            #{{ $index + 1 }}
                                        </div>
                                    </div>
                                </a>
                                <!-- Nom du fichier -->
                                <p class="mt-2 text-xs text-gray-500 truncate text-center" title="{{ $fileName }}">
                                    {{ $fileName }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Note informative -->
                    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                            <div class="text-sm text-blue-800">
                                <strong>Note :</strong> Cliquez sur une photo pour l'ouvrir en taille réelle dans un nouvel onglet.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <!-- Message si aucune photo -->
            <div class="bg-gray-50 border-2 border-gray-200 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-600">
                        <i class="fas fa-images mr-2 text-gray-400"></i>Photos associées
                    </h2>
                </div>
                <div class="p-6 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-image text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Aucune photo envoyée</h3>
                    <p class="text-gray-500 mb-4">
                        Le client n'a pas joint de photos avec sa soumission.
                    </p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 max-w-xl mx-auto">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-lightbulb mr-2"></i>
                            <strong>Astuce :</strong> Utilisez la section "Débogage Photos" ci-dessus pour vérifier les données brutes.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            @if($submission->is_emergency)
            <!-- Informations d'urgence -->
            <div class="bg-red-50 border-2 border-red-200 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-red-200">
                    <h2 class="text-xl font-semibold text-red-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Informations d'Urgence
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-red-700">Type d'urgence</label>
                            <p class="mt-1 text-lg font-bold text-red-900">
                                {{ ucfirst(str_replace('-', ' ', $submission->emergency_type ?? 'Non spécifié')) }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-red-700">Niveau d'urgence</label>
                            <p class="mt-1 text-lg font-bold text-red-900">
                                {{ ucfirst($submission->urgency_level ?? 'urgent') }}
                            </p>
                        </div>
                        @if($submission->message)
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-red-700">Description</label>
                            <div class="mt-1 p-4 bg-white rounded border border-red-200">
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $submission->message }}</p>
                            </div>
                        </div>
                        @endif
                        @if($submission->work_type)
                        <div>
                            <label class="text-sm font-medium text-red-700">Type de travail</label>
                            <p class="mt-1 text-lg font-medium text-red-900">{{ $submission->work_type }}</p>
                        </div>
                        @endif
                        @if($submission->photos && count($submission->photos) > 0)
                        <div class="md:col-span-2 mt-4">
                            <label class="text-sm font-medium text-red-700">Photos de l'urgence</label>
                            <p class="mt-1 text-sm text-red-600">
                                <i class="fas fa-arrow-up mr-1"></i>Voir la section "Photos associées" ci-dessus pour toutes les photos.
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Détails du projet -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-home mr-2 text-green-500"></i>Détails du projet
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Type de bien</label>
                            <p class="mt-1 text-lg font-medium text-gray-900">
                                {{ $submission->property_type ? ucfirst(strtolower($submission->property_type)) : '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Surface</label>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $submission->surface ?? '-' }} m²</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Statut propriétaire</label>
                            <p class="mt-1 text-lg font-medium text-gray-900">
                                {{ $submission->ownership_status ? ucfirst(strtolower($submission->ownership_status)) : '-' }}
                            </p>
                        </div>
                    </div>

                    @if($submission->work_types)
                    <div class="mt-6">
                        <label class="text-sm font-medium text-gray-500">Types de travaux</label>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($submission->work_types as $workType)
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                    {{ ucfirst($workType) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($submission->roof_work_types)
                    <div class="mt-4">
                        <label class="text-sm font-medium text-gray-500">Travaux de plomberie</label>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($submission->roof_work_types as $type)
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded-full">
                                    {{ ucfirst($type) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($submission->facade_work_types)
                    <div class="mt-4">
                        <label class="text-sm font-medium text-gray-500">Travaux de façade</label>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($submission->facade_work_types as $type)
                                <span class="px-3 py-1 bg-orange-100 text-orange-800 text-sm font-medium rounded-full">
                                    {{ ucfirst($type) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($submission->isolation_work_types)
                    <div class="mt-4">
                        <label class="text-sm font-medium text-gray-500">Travaux d'isolation</label>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($submission->isolation_work_types as $type)
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                                    {{ ucfirst($type) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Résumé -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Résumé</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">ID Session</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ substr($submission->session_id, 0, 8) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Créée le</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $submission->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @if($submission->completed_at)
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Complétée le</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $submission->completed_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Durée</dt>
                        <dd class="text-sm font-medium text-gray-900">
                            {{ gmdate('H:i:s', $submission->completed_at->diffInSeconds($submission->created_at)) }}
                        </dd>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Étape actuelle</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $submission->current_step ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Informations de tracking -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-map-marker-alt mr-2 text-purple-500"></i>Géolocalisation
                </h3>
                <dl class="space-y-3">
                    @if($submission->ip_address)
                    <div>
                        <dt class="text-sm text-gray-500">Adresse IP</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $submission->ip_address }}</dd>
                    </div>
                    @endif
                    @if($submission->city)
                    <div>
                        <dt class="text-sm text-gray-500">Ville</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $submission->city }}</dd>
                    </div>
                    @endif
                    @if($submission->country)
                    <div>
                        <dt class="text-sm text-gray-500">Pays</dt>
                        <dd class="text-sm font-medium text-gray-900">
                            {{ $submission->country }}
                            @if($submission->country_code)
                                <span class="text-xs text-gray-500">({{ $submission->country_code }})</span>
                            @endif
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Source de trafic -->
            @if($submission->referrer_url)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-link mr-2 text-blue-500"></i>Source de trafic
                </h3>
                <div>
                    <dt class="text-sm text-gray-500 mb-2">Page d'origine</dt>
                    <dd class="text-sm">
                        <a href="{{ $submission->referrer_url }}" target="_blank" 
                           class="text-blue-600 hover:text-blue-800 break-all">
                            <i class="fas fa-external-link-alt mr-1"></i>{{ $submission->referrer_url }}
                        </a>
                    </dd>
                </div>
            </div>
            @endif

            <!-- Informations techniques -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle mr-2 text-gray-500"></i>Informations techniques
                </h3>
                <dl class="space-y-3">
                    @if($submission->user_agent)
                    <div>
                        <dt class="text-sm text-gray-500">User Agent</dt>
                        <dd class="text-xs font-mono text-gray-700 break-all">{{ $submission->user_agent }}</dd>
                    </div>
                    @endif
                    @if($submission->recaptcha_score !== null)
                    <div>
                        <dt class="text-sm text-gray-500">Score reCAPTCHA</dt>
                        <dd class="text-sm font-medium {{ $submission->recaptcha_score >= 0.5 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($submission->recaptcha_score, 2) }}
                            <span class="text-xs text-gray-500">
                                ({{ $submission->recaptcha_score >= 0.5 ? 'Légitime' : 'Suspect' }})
                            </span>
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Actions -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">Actions rapides</h3>
                <div class="space-y-3">
                    @if($submission->email)
                    <a href="mailto:{{ $submission->email }}" class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-envelope mr-2"></i>Envoyer un email
                    </a>
                    @endif
                    @if($submission->phone)
                    <a href="tel:{{ $submission->phone }}" class="block w-full text-center bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-phone mr-2"></i>Appeler
                    </a>
                    @endif
                    <form method="POST" action="{{ route('admin.submission.resend-email', $submission->id) }}" class="w-full">
                        @csrf
                        <button type="submit" class="block w-full text-center bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                            <i class="fas fa-paper-plane mr-2"></i>Renvoyer email à l'admin
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
