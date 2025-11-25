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
                        <i class="fas fa-user mr-2 text-blue-500"></i>Informations personnelles
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Civilité</label>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $submission->gender ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Nom complet</label>
                            <p class="mt-1 text-lg font-medium text-gray-900">
                                @if($submission->name)
                                    {{ $submission->name }}
                                    @if($submission->is_emergency)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                            🚨 URGENCE
                                        </span>
                                    @endif
                                @else
                                    {{ $submission->first_name }} {{ $submission->last_name }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="mt-1 text-lg text-gray-900">
                                <a href="mailto:{{ $submission->email }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-envelope mr-1"></i>{{ $submission->email ?? '-' }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Téléphone</label>
                            <p class="mt-1 text-lg text-gray-900">
                                <a href="tel:{{ $submission->phone }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-phone mr-1"></i>{{ $submission->phone ?? '-' }}
                                </a>
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-500">Code postal / Ville</label>
                            <p class="mt-1 text-lg text-gray-900">{{ $submission->postal_code ?? '-' }}</p>
                        </div>
                        @if($submission->address)
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-500">Adresse complète</label>
                            <p class="mt-1 text-lg text-gray-900">{{ $submission->address }}</p>
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
                
                // Debug logging
                \Log::info('Submission photos debug', [
                    'submission_id' => $submission->id,
                    'photos_field' => $submission->photos,
                    'tracking_data_photos' => $submission->tracking_data['photos'] ?? null,
                    'total_photos' => $totalPhotos,
                    'all_photos' => $allPhotos
                ]);
            @endphp

            <!-- Section toutes les données -->
            <div class="bg-white border-2 border-blue-300 rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-blue-200 flex items-center justify-between bg-blue-50">
                    <h2 class="text-lg font-semibold text-blue-900">
                        <i class="fas fa-database mr-2 text-blue-600"></i>Toutes les Données de la Soumission
                    </h2>
                    <button onclick="document.getElementById('all-data-section').classList.toggle('hidden')" 
                            class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                        <i class="fas fa-eye mr-1"></i>Afficher/Masquer
                    </button>
                </div>
                <div id="all-data-section" class="p-6 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $allFields = [
                                'ID' => $submission->id,
                                'Session ID' => $submission->session_id,
                                'User Identifier' => $submission->user_identifier,
                                'Type de formulaire' => $submission->is_emergency ? '🚨 URGENCE' : ($submission->work_type ?? 'Simulateur'),
                                'Civilité (gender)' => $submission->gender,
                                'Prénom (first_name)' => $submission->first_name,
                                'Nom (last_name)' => $submission->last_name,
                                'Nom complet (name)' => $submission->name,
                                'Email' => $submission->email,
                                'Téléphone (phone)' => $submission->phone,
                                'Code postal' => $submission->postal_code,
                                'Adresse complète' => $submission->address,
                                'Ville (city)' => $submission->city,
                                'Pays (country)' => $submission->country,
                                'Code pays (country_code)' => $submission->country_code,
                                'Type de bien (property_type)' => $submission->property_type,
                                'Surface (m²)' => $submission->surface,
                                'Statut propriétaire (ownership_status)' => $submission->ownership_status,
                                'Type de travail (work_type)' => $submission->work_type,
                                'Types de travaux (work_types)' => is_array($submission->work_types) ? implode(', ', $submission->work_types) : $submission->work_types,
                                'Travaux plomberie (roof_work_types)' => is_array($submission->roof_work_types) ? implode(', ', $submission->roof_work_types) : $submission->roof_work_types,
                                'Travaux façade (facade_work_types)' => is_array($submission->facade_work_types) ? implode(', ', $submission->facade_work_types) : $submission->facade_work_types,
                                'Travaux isolation (isolation_work_types)' => is_array($submission->isolation_work_types) ? implode(', ', $submission->isolation_work_types) : $submission->isolation_work_types,
                                'Message / Description' => $submission->message,
                                'Est urgence (is_emergency)' => $submission->is_emergency ? 'OUI' : 'NON',
                                'Type d\'urgence (emergency_type)' => $submission->emergency_type,
                                'Niveau d\'urgence (urgency_level)' => $submission->urgency_level,
                                'Statut' => $submission->status,
                                'Étape actuelle (current_step)' => $submission->current_step,
                                'Adresse IP (ip_address)' => $submission->ip_address,
                                'URL référente (referrer_url)' => $submission->referrer_url,
                                'User Agent' => $submission->user_agent,
                                'Score reCAPTCHA' => $submission->recaptcha_score,
                                'Date création' => $submission->created_at?->format('d/m/Y H:i:s'),
                                'Date mise à jour' => $submission->updated_at?->format('d/m/Y H:i:s'),
                                'Date complétion' => $submission->completed_at?->format('d/m/Y H:i:s'),
                                'Date abandon' => $submission->abandoned_at?->format('d/m/Y H:i:s'),
                            ];
                        @endphp
                        
                        @foreach($allFields as $label => $value)
                            <div class="border-b border-gray-200 pb-2">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $label }}</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">
                                    @if($value)
                                        @if(Str::startsWith($value, 'http'))
                                            <a href="{{ $value }}" target="_blank" class="text-blue-600 hover:text-blue-800 break-all">
                                                {{ Str::limit($value, 50) }}
                                            </a>
                                        @else
                                            {{ $value }}
                                        @endif
                                    @else
                                        <span class="text-gray-400 italic">Non renseigné</span>
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Form Data JSON -->
                    @if($submission->form_data && !empty($submission->form_data))
                    <div class="mt-6">
                        <h3 class="font-semibold text-sm text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-code mr-2 text-purple-600"></i>
                            Données formulaire complètes (form_data)
                        </h3>
                        <pre class="bg-gray-900 text-green-400 p-4 rounded text-xs overflow-x-auto max-h-64 overflow-y-auto">{{ json_encode($submission->form_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                    @endif
                    
                    <!-- Tracking Data JSON -->
                    @if($submission->tracking_data && !empty($submission->tracking_data))
                    <div class="mt-6">
                        <h3 class="font-semibold text-sm text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-chart-line mr-2 text-orange-600"></i>
                            Données de tracking (tracking_data)
                        </h3>
                        <pre class="bg-gray-900 text-orange-400 p-4 rounded text-xs overflow-x-auto max-h-64 overflow-y-auto">{{ json_encode($submission->tracking_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                    @endif
                    
                    <!-- Photos Array -->
                    @if($submission->photos && !empty($submission->photos))
                    <div class="mt-6">
                        <h3 class="font-semibold text-sm text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-images mr-2 text-indigo-600"></i>
                            Photos (submission->photos)
                        </h3>
                        <pre class="bg-gray-900 text-indigo-400 p-4 rounded text-xs overflow-x-auto">{{ json_encode($submission->photos, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Section débogage photos (compact) -->
            <div class="bg-gray-50 border-2 border-gray-300 rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-300 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-700">
                        <i class="fas fa-bug mr-2 text-gray-500"></i>Débogage Photos
                    </h2>
                    <button onclick="document.getElementById('debug-section').classList.toggle('hidden')" 
                            class="px-3 py-1 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300">
                        <i class="fas fa-eye mr-1"></i>Afficher/Masquer
                    </button>
                </div>
                <div id="debug-section" class="p-6 hidden">
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-semibold text-sm text-gray-600 mb-2">Photos directes (submission->photos)</h3>
                            <pre class="bg-gray-800 text-green-400 p-4 rounded text-xs overflow-x-auto">{{ json_encode($submission->photos, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        <div>
                            <h3 class="font-semibold text-sm text-gray-600 mb-2">Photos tracking (tracking_data['photos'])</h3>
                            <pre class="bg-gray-800 text-green-400 p-4 rounded text-xs overflow-x-auto">{{ json_encode($submission->tracking_data['photos'] ?? null, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        <div>
                            <h3 class="font-semibold text-sm text-gray-600 mb-2">Toutes les photos fusionnées</h3>
                            <pre class="bg-gray-800 text-green-400 p-4 rounded text-xs overflow-x-auto">{{ json_encode($allPhotos, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded p-4">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Total de {{ $totalPhotos }} photo(s) détectée(s)</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

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
                                        
                                        <!-- Badge source -->
                                        <div class="absolute top-2 right-2 bg-{{ $photoData['type'] === 'direct' ? 'green' : 'blue' }}-600 text-white text-xs px-2 py-1 rounded shadow-lg">
                                            {{ $photoData['source'] }}
                                        </div>
                                    </div>
                                </a>
                                <!-- Infos du fichier -->
                                <div class="mt-2 text-xs">
                                    <p class="text-gray-700 font-medium truncate text-center" title="{{ $fileName }}">
                                        {{ $fileName }}
                                    </p>
                                    <p class="text-gray-500 text-center">
                                        Path: {{ $cleanPath }}
                                    </p>
                                    <div class="flex justify-center gap-2 mt-1">
                                        <a href="{{ $photoUrl }}" target="_blank" class="text-indigo-600 hover:text-indigo-800">
                                            <i class="fas fa-external-link-alt"></i> Ouvrir
                                        </a>
                                        <button onclick="navigator.clipboard.writeText('{{ $photoUrl }}')" class="text-gray-600 hover:text-gray-800">
                                            <i class="fas fa-copy"></i> URL
                                        </button>
                                    </div>
                                </div>
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
