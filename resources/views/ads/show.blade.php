@extends('layouts.app')

@php
    // S'assurer que les métadonnées sont disponibles pour le layout
    // Le layout utilise $pageTitle, $pageDescription, etc. en priorité
    // Ces variables sont passées depuis AdPublicController::show()
@endphp

@section('title', $pageTitle ?? 'Service professionnel')

@section('description', $pageDescription ?? 'Service professionnel de qualité. Devis gratuit et intervention rapide.')

@push('head')
<style>
    /* Variables de couleurs de branding */
    :root {
        --primary-color: {{ setting('primary_color', '#3b82f6') }};
        --secondary-color: {{ setting('secondary_color', '#1e40af') }};
        --accent-color: {{ setting('accent_color', '#f59e0b') }};
    }
    
    /* Fix responsive mobile - empêcher le scroll horizontal */
    body {
        overflow-x: hidden;
        max-width: 100vw;
    }
    
    /* Container principal */
    .container {
        max-width: 100%;
        overflow-x: hidden;
    }
    
    /* Forcer le word-wrap sur tous les textes */
    * {
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-word;
        white-space: normal !important;
    }
    
    /* Titres responsive */
    h1, h2, h3, h4, h5, h6 {
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-word;
        hyphens: auto;
        white-space: normal !important;
    }
    
    /* Contenu de l'annonce - responsive */
    .ad-content {
        max-width: 100%;
        overflow-x: hidden;
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-word;
    }
    
    .ad-content * {
        max-width: 100% !important;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
        word-break: break-word !important;
        white-space: normal !important;
    }
    
    /* Paragraphes et textes */
    .ad-content p,
    .ad-content span,
    .ad-content div,
    .ad-content a,
    .ad-content strong,
    .ad-content em,
    .ad-content b,
    .ad-content i {
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
        word-break: break-word !important;
        white-space: normal !important;
        max-width: 100% !important;
    }
    
    /* Tableaux responsive */
    .ad-content table {
        width: 100% !important;
        display: block;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .ad-content table thead,
    .ad-content table tbody,
    .ad-content table tr {
        display: block;
    }
    
    .ad-content table td,
    .ad-content table th {
        display: block;
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    
    /* Images responsive */
    .ad-content img {
        max-width: 100% !important;
        height: auto !important;
        display: block;
    }
    
    /* Masquer l'ancienne section Financement dans le contenu de l'annonce */
    .ad-content .old-financing-section {
        display: none !important;
    }
    
    /* Listes responsive */
    .ad-content ul,
    .ad-content ol {
        padding-left: 1.5rem;
        margin: 1rem 0;
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-word;
    }
    
    .ad-content li {
        margin-bottom: 0.5rem;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
        word-break: break-word !important;
        white-space: normal !important;
        max-width: 100% !important;
    }
    
    /* Boutons responsive */
    .btn-responsive {
        white-space: normal;
        word-wrap: break-word;
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
    
    /* Boutons de partage - un par ligne (tous écrans) */
    .ad-content .flex.items-center.space-x-3,
    .ad-content .flex.items-center.space-x-2,
    .ad-content div[class*="flex"][class*="space-x"] {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 0.75rem !important;
    }
    
    .ad-content .flex.items-center.space-x-3 > *,
    .ad-content .flex.items-center.space-x-2 > *,
    .ad-content div[class*="flex"][class*="space-x"] > * {
        width: 100% !important;
        margin: 0 !important;
    }
    
    /* Cibler spécifiquement les boutons de partage */
    .ad-content a[href*="facebook.com/sharer"],
    .ad-content a[href*="wa.me"],
    .ad-content a[href*="whatsapp"],
    .ad-content a[href*="mailto:"][href*="subject"],
    .ad-content button[onclick*="copyToClipboard"],
    .ad-content a[class*="bg-blue-600"],
    .ad-content a[class*="bg-green-500"],
    .ad-content a[class*="bg-gray-600"] {
        display: block !important;
        width: 100% !important;
        margin-bottom: 0.75rem !important;
    }
    
    @media (max-width: 640px) {
        /* Forcer les retours à la ligne sur mobile */
        body, body * {
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            word-break: break-word !important;
            white-space: normal !important;
        }
        
        /* Empêcher le texte de dépasser */
        p, span, div, a, h1, h2, h3, h4, h5, h6, li, td, th {
            max-width: 100% !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            word-break: break-word !important;
            white-space: normal !important;
        }
        
        .btn-responsive {
            padding: 0.625rem 0.875rem;
            font-size: 0.85rem;
            white-space: normal !important;
            word-wrap: break-word !important;
        }
        
        /* Réduire les paddings sur mobile */
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        /* Titres plus petits sur mobile */
        h1 {
            font-size: 1.75rem !important;
            line-height: 1.2;
            word-wrap: break-word !important;
            white-space: normal !important;
        }
        
        h2 {
            font-size: 1.5rem !important;
            line-height: 1.3;
            word-wrap: break-word !important;
            white-space: normal !important;
        }
        
        h3 {
            font-size: 1.25rem !important;
            line-height: 1.4;
            word-wrap: break-word !important;
            white-space: normal !important;
        }
        
        /* Padding réduit dans les sections */
        section {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        
        /* Hero section plus compacte */
        .hero-section {
            padding-top: 3rem;
            padding-bottom: 3rem;
        }
        
    /* Cards avec padding réduit */
    .card-padding {
        padding: 1rem;
    }
}

/* MASQUER DÉFINITIVEMENT toutes les sections de financement */
.old-financing-section,
.ad-content *[class*="financing"],
.ad-content *[class*="financement"] {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    height: 0 !important;
    overflow: hidden !important;
    margin: 0 !important;
    padding: 0 !important;
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <section class="relative py-12 md:py-20 text-white overflow-hidden hero-section">
        @if(!empty($featuredImage))
        @php
            // Nettoyer le chemin de l'image (enlever le préfixe uploads/ si déjà présent dans asset())
            $imagePath = str_starts_with($featuredImage, 'http') ? $featuredImage : asset($featuredImage);
        @endphp
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" 
             style="background-image: url('{{ $imagePath }}'); filter: blur(2px); transform: scale(1.1);"></div>
        <div class="absolute inset-0 bg-black bg-opacity-60"></div>
        @else
        <div class="absolute inset-0" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);"></div>
        @endif
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-4 md:mb-6 px-2">
                    <i class="fas fa-tools mr-2 md:mr-4"></i>
                    <span class="break-words">{{ $ad->title ?? 'Service professionnel' }}</span>
                </h1>
                <p class="text-base sm:text-lg md:text-xl lg:text-2xl mb-6 md:mb-8 leading-relaxed px-2">
                    Service professionnel à {{ $cityModel->name ?? 'votre ville' }} - Devis gratuit et intervention rapide
                </p>
                <div class="flex flex-col sm:flex-row gap-3 md:gap-4 justify-center px-2">
                    <a href="{{ route('form.step', 'propertyType') }}" 
                       class="text-white font-bold py-3 px-4 md:py-4 md:px-8 rounded-lg text-sm md:text-lg transition-colors shadow-lg btn-responsive"
                       style="background-color: var(--accent-color);"
                       onmouseover="this.style.backgroundColor='var(--secondary-color)';"
                       onmouseout="this.style.backgroundColor='var(--accent-color)';">
                        <i class="fas fa-calculator mr-2"></i>
                        <span class="break-words">Simulateur de devis</span>
                    </a>
                    <a href="tel:{{ setting('company_phone_raw') }}" 
                       class="text-white font-bold py-3 px-4 md:py-4 md:px-8 rounded-lg text-sm md:text-lg transition-colors shadow-lg btn-responsive"
                       style="background-color: var(--primary-color);"
                       onmouseover="this.style.backgroundColor='var(--secondary-color)';"
                       onmouseout="this.style.backgroundColor='var(--primary-color)';">
                        <i class="fas fa-phone mr-2"></i>
                        <span class="break-words">{{ setting('company_phone') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenu de l'annonce -->
    <section class="py-8 md:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="bg-white rounded-2xl shadow-lg p-4 md:p-8 lg:p-12 card-padding ad-content overflow-x-hidden">
                    {!! $ad->content_html ?? '<p>Contenu en cours de chargement...</p>' !!}
                </div>

                <!-- Section Simulateur de Prix (Mise en avant) -->
                <div class="mt-8 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl shadow-2xl p-6 md:p-8 lg:p-10 overflow-hidden group">
                    <div class="flex flex-col md:flex-row items-center gap-6 md:gap-8">
                        <!-- Texte et CTA -->
                        <div class="flex-1 text-white">
                            <div class="flex items-center mb-4">
                                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                                    <i class="fas fa-calculator text-white text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl md:text-3xl font-bold mb-1">Simulateur de Prix Gratuit</h3>
                                    <p class="text-blue-100 text-sm">Obtenez votre devis en 2 minutes</p>
                                </div>
                            </div>
                            
                            <p class="text-base md:text-lg text-blue-50 mb-6 leading-relaxed">
                                Calculez le coût estimatif de vos travaux de {{ $ad->service->name ?? 'plomberie' }} à {{ $cityModel->name ?? 'votre ville' }}. 
                                Notre simulateur intelligent vous fournit une estimation détaillée, personnalisée selon votre projet.
                            </p>
                            
                            <div class="space-y-3 mb-6">
                                <div class="flex items-center text-white">
                                    <i class="fas fa-check-circle text-green-300 mr-3"></i>
                                    <span>Estimation précise et personnalisée</span>
                                </div>
                                <div class="flex items-center text-white">
                                    <i class="fas fa-check-circle text-green-300 mr-3"></i>
                                    <span>100% gratuit, sans engagement</span>
                                </div>
                                <div class="flex items-center text-white">
                                    <i class="fas fa-check-circle text-green-300 mr-3"></i>
                                    <span>Réponse immédiate en ligne</span>
                                </div>
                            </div>
                            
                            <a href="{{ route('form.step', 'propertyType') }}" 
                               class="inline-block bg-white text-blue-600 font-bold px-8 py-4 rounded-full text-lg hover:bg-blue-50 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105 transform">
                                <i class="fas fa-arrow-right mr-2"></i>
                                Démarrer le simulateur
                            </a>
                        </div>
                        
                        <!-- Image du Simulateur (Carrée) -->
                        <div class="flex-1 w-full md:w-auto md:max-w-md">
                            <a href="{{ route('form.step', 'propertyType') }}" class="block relative rounded-2xl overflow-hidden shadow-2xl border-4 border-white/30 bg-white/10 backdrop-blur-sm cursor-pointer hover:border-blue-400 transition-all duration-300 group aspect-square">
                                @if(setting('simulator_image') && file_exists(public_path(setting('simulator_image'))))
                                <img src="{{ asset(setting('simulator_image')) }}" 
                                     alt="Simulateur de Prix" 
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600">
                                    <i class="fas fa-calculator text-white text-9xl opacity-70"></i>
                                </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-blue-900/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center p-6">
                                    <span class="text-white text-xl font-bold transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                        <i class="fas fa-mouse-pointer mr-2"></i>Cliquez pour calculer
                                    </span>
                                </div>
                            </a>
                            
                            <div class="mt-4 flex justify-center gap-2">
                                <span class="bg-white/20 backdrop-blur-sm text-white text-xs px-3 py-1 rounded-full">
                                    <i class="fas fa-clock mr-1"></i>2 min
                                </span>
                                <span class="bg-white/20 backdrop-blur-sm text-white text-xs px-3 py-1 rounded-full">
                                    <i class="fas fa-gift mr-1"></i>Gratuit
                                </span>
                                <span class="bg-white/20 backdrop-blur-sm text-white text-xs px-3 py-1 rounded-full">
                                    <i class="fas fa-shield-alt mr-1"></i>Sans engagement
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Informations Pratiques -->
                <div class="mt-8 bg-white rounded-2xl shadow-lg p-6 md:p-8 border border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                        Informations Pratiques
                    </h3>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-red-600 text-xl mt-1 mr-4"></i>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-1">Adresse</h4>
                                <p class="text-gray-700">{{ setting('company_address', '35 Rue des Chantiers') }}</p>
                                <p class="text-gray-700">{{ setting('company_postal_code', '78000') }} {{ setting('company_city', 'Versailles') }}</p>
                                @if(setting('company_country'))
                                <p class="text-gray-700">{{ setting('company_country', 'France') }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <i class="fas fa-phone text-green-600 text-xl mt-1 mr-4"></i>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-1">Téléphone</h4>
                                <a href="tel:{{ setting('company_phone_raw') }}" class="text-blue-600 hover:text-blue-800 text-lg font-semibold">
                                    {{ setting('company_phone', '07 86 48 65 39') }}
                                </a>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <i class="fas fa-envelope text-blue-600 text-xl mt-1 mr-4"></i>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-1">Email</h4>
                                <a href="mailto:{{ setting('company_email') }}" class="text-blue-600 hover:text-blue-800">
                                    {{ setting('company_email', 'contact@plombier-versailles78.fr') }}
                                </a>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <i class="fas fa-building text-purple-600 text-xl mt-1 mr-4"></i>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-1">Société</h4>
                                <p class="text-gray-700">{{ setting('company_name', 'Plombier Versailles') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 md:mt-12 rounded-2xl p-4 md:p-8 text-white text-center overflow-x-hidden" style="background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
                    <h3 class="text-xl md:text-2xl font-bold mb-3 md:mb-4 px-2 break-words">Prêt à Démarrer Votre Projet à {{ $cityModel->name ?? 'votre ville' }} ?</h3>
                    <p class="text-base md:text-lg mb-4 md:mb-6 px-2 break-words">Contactez-nous dès aujourd'hui pour un devis gratuit et personnalisé</p>
                    
                    <div class="flex flex-col sm:flex-row gap-3 md:gap-4 justify-center px-2">
                        <a href="{{ route('form.step', 'propertyType') }}" 
                           class="text-white font-bold py-3 px-4 md:py-4 md:px-8 rounded-lg text-sm md:text-lg transition-colors shadow-lg btn-responsive"
                           style="background-color: var(--accent-color);"
                           onmouseover="this.style.backgroundColor='var(--secondary-color)';"
                           onmouseout="this.style.backgroundColor='var(--accent-color)';">
                            <i class="fas fa-calculator mr-2"></i>
                            <span class="break-words">Simulateur de devis</span>
                        </a>
                        <a href="tel:{{ setting('company_phone_raw') }}" 
                           class="text-white font-bold py-3 px-4 md:py-4 md:px-8 rounded-lg text-sm md:text-lg transition-colors shadow-lg btn-responsive"
                           style="background-color: var(--primary-color);"
                           onmouseover="this.style.backgroundColor='var(--secondary-color)';"
                           onmouseout="this.style.backgroundColor='var(--primary-color)';">
                            <i class="fas fa-phone mr-2"></i>
                            <span class="break-words">Appeler Maintenant</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Nos Réalisations -->
    @if(!empty($portfolioItems) && count($portfolioItems) > 0)
    <section class="py-8 md:py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-8 md:mb-12">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3 md:mb-4 px-2 break-words">Nos Réalisations</h2>
                    <p class="text-base md:text-lg text-gray-600 px-2 break-words">Découvrez quelques-unes de nos réalisations récentes</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach(array_slice($portfolioItems, 0, 6) as $portfolioItem)
                    @if(is_array($portfolioItem) && !empty($portfolioItem['images']) && is_array($portfolioItem['images']))
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                        <div class="relative">
                            <img src="{{ asset($portfolioItem['images'][0]) }}" 
                                 alt="{{ $portfolioItem['title'] ?? 'Réalisation' }}" 
                                 class="w-full h-48 object-cover">
                            <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                                <div class="opacity-0 hover:opacity-100 transition-opacity duration-300">
                                    <i class="fas fa-search-plus text-white text-2xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">
                                {{ $portfolioItem['title'] ?? 'Réalisation' }}
                            </h3>
                            @if(!empty($portfolioItem['description']))
                            <p class="text-gray-600 text-sm mb-4">
                                {{ Str::limit($portfolioItem['description'], 100) }}
                            </p>
                            @endif
                            <div class="flex items-center justify-between">
                                @if(!empty($portfolioItem['work_type']))
                                <span class="px-3 py-1 rounded-full text-xs font-medium"
                                      style="background-color: rgba(var(--primary-color-rgb, 59, 130, 246), 0.1); color: var(--primary-color);">
                                    @switch($portfolioItem['work_type'])
                                        @case('roof')
                                            <i class="fas fa-home mr-1"></i>Plomberie
                                            @break
                                        @case('facade')
                                            <i class="fas fa-building mr-1"></i>Façade
                                            @break
                                        @case('isolation')
                                            <i class="fas fa-thermometer-half mr-1"></i>Isolation
                                            @break
                                        @default
                                            <i class="fas fa-tools mr-1"></i>Mixte
                                    @endswitch
                                </span>
                                @endif
                                @if(count($portfolioItem['images']) > 1)
                                <span class="text-gray-500 text-sm">
                                    <i class="fas fa-images mr-1"></i>{{ count($portfolioItem['images']) }} photos
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
                
                @if(count($portfolioItems) > 6)
                <div class="text-center mt-8">
                    <a href="{{ route('portfolio.index') }}" 
                       class="text-white font-bold py-3 px-8 rounded-lg transition-colors"
                       style="background-color: var(--primary-color);"
                       onmouseover="this.style.backgroundColor='var(--secondary-color)';"
                       onmouseout="this.style.backgroundColor='var(--primary-color)';">
                        <i class="fas fa-images mr-2"></i>
                        Voir Toutes nos Réalisations
                    </a>
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    <!-- Section Annonces Similaires -->
    @if(isset($relatedAds) && $relatedAds->count() > 0)
    <section class="py-8 md:py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-8 md:mb-12">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3 md:mb-4 px-2 break-words">Autres Services à {{ $cityModel->name ?? 'votre ville' }}</h2>
                    <p class="text-base md:text-lg text-gray-600 px-2 break-words">Découvrez nos autres services disponibles dans votre ville</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($relatedAds as $relatedAd)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                        <div class="p-4 md:p-6">
                            <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2 break-words">{{ $relatedAd->title }}</h3>
                            <p class="text-gray-600 text-sm mb-4 break-words">{{ Str::limit($relatedAd->meta_description, 100) }}</p>
                            <a href="{{ route('ads.show', $relatedAd->slug) }}" 
                               class="inline-block text-white font-semibold px-4 py-2 rounded-lg transition-colors text-sm md:text-base break-words"
                               style="background-color: var(--primary-color);"
                               onmouseover="this.style.backgroundColor='var(--secondary-color)';"
                               onmouseout="this.style.backgroundColor='var(--primary-color)';">
                                Voir le service
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Section Avis Clients -->
    <section class="py-8 md:py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-8 md:mb-12">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3 md:mb-4 px-2 break-words">Avis de Nos Clients</h2>
                    <p class="text-base md:text-lg text-gray-600 px-2 break-words">Ce que disent nos clients sur nos services</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @php
                        $reviews = \App\Models\Review::where('is_active', true)->take(3)->get();
                    @endphp
                    
                    @if($reviews->count() > 0)
                    @foreach($reviews as $review)
                    <div class="bg-gray-50 rounded-2xl p-4 md:p-6 shadow-lg hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center mb-3 md:mb-4">
                            <div class="w-10 h-10 md:w-12 md:h-12 rounded-full overflow-hidden mr-3 md:mr-4 flex-shrink-0">
                                @if($review->author_photo_url)
                                <img src="{{ $review->author_photo_url }}" alt="{{ $review->author_name }}" class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm md:text-lg">
                                    {{ $review->author_initials }}
                                </div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-gray-900 text-sm md:text-base break-words">{{ $review->author_name }}</h4>
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-yellow-400 text-xs md:text-sm {{ $i <= $review->rating ? '' : 'opacity-30' }}"></i>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-gray-700 mb-3 md:mb-4 text-sm md:text-base">
                            @if($review->review_text)
                                <p class="break-words">{{ Str::limit($review->review_text, 150) }}</p>
                            @else
                                <p class="text-gray-500 italic break-words">Avis sans contenu détaillé</p>
                            @endif
                        </div>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span>{{ $review->review_date ? \Carbon\Carbon::parse($review->review_date)->format('d/m/Y') : '' }}</span>
                            @if($review->source && $review->source !== 'manual')
                            <span class="px-2 py-1 rounded-full text-xs"
                                  style="background-color: rgba(var(--primary-color-rgb, 59, 130, 246), 0.1); color: var(--primary-color);">
                                @if(str_contains($review->source, 'Google'))
                                    <i class="fab fa-google mr-1"></i>Google Maps
                                @elseif(str_contains($review->source, 'Travaux'))
                                    <i class="fas fa-tools mr-1"></i>Travaux.com
                                @elseif(str_contains($review->source, 'LeBonCoin'))
                                    <i class="fas fa-shopping-cart mr-1"></i>LeBonCoin
                                @elseif(str_contains($review->source, 'Trustpilot'))
                                    <i class="fas fa-shield-alt mr-1"></i>Trustpilot
                                @elseif(str_contains($review->source, 'Facebook'))
                                    <i class="fab fa-facebook mr-1"></i>Facebook
                                @else
                                    <i class="fas fa-star mr-1"></i>{{ ucfirst($review->source) }}
                                @endif
                            </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="col-span-full text-center py-8">
                        <p class="text-gray-500">Aucun avis disponible pour le moment.</p>
                    </div>
                    @endif
                </div>
                
                <div class="text-center mt-8">
                    <a href="{{ route('reviews.all') }}" 
                       class="text-white font-bold py-3 px-8 rounded-lg transition-colors"
                       style="background-color: var(--primary-color);"
                       onmouseover="this.style.backgroundColor='var(--secondary-color)';"
                       onmouseout="this.style.backgroundColor='var(--primary-color)';">
                        Voir Tous les Avis
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
// Masquer TOUTES les références au financement dans le contenu de l'annonce
document.addEventListener('DOMContentLoaded', function() {
    const adContent = document.querySelector('.ad-content');
    if (!adContent) return;
    
    // Mots-clés de financement à détecter (très exhaustif)
    const financingKeywords = [
        'financement',
        'maprimerenov',
        "maprimerénov'",
        'prime rénov',
        'certificat',
        'cee',
        'éco-prêt',
        'eco-pret',
        'éco-ptz',
        'eco-ptz',
        'tva réduite',
        'tva à 5',
        'aide financière',
        'aide gouvernementale',
        'subvention',
        'économie d\'énergie',
        'économies d\'énergie',
        'avantage financier',
        'avantages financiers',
        'aide',
        'aides'
    ];
    
    // Fonction pour vérifier si un texte contient des mots-clés de financement
    function containsFinancingKeyword(text) {
        if (!text) return false;
        const lowerText = text.toLowerCase().replace(/'/g, "'").replace(/'/g, "'");
        return financingKeywords.some(keyword => lowerText.includes(keyword));
    }
    
    // Fonction pour masquer un élément et tous ses frères suivants jusqu'au prochain titre
    function hideElementAndFollowing(element) {
        if (!element) return;
        
        element.style.display = 'none';
        element.classList.add('old-financing-section');
        
        let nextEl = element.nextElementSibling;
        let count = 0;
        const maxElements = 20; // Limite de sécurité
        
        while (nextEl && count < maxElements) {
            const isHeading = /^H[1-6]$/.test(nextEl.tagName);
            
            // Si on trouve un nouveau titre qui NE parle PAS de financement, on arrête
            if (isHeading && !containsFinancingKeyword(nextEl.textContent)) {
                break;
            }
            
            // Sinon, on masque l'élément
            const currentEl = nextEl;
            nextEl = nextEl.nextElementSibling;
            
            currentEl.style.display = 'none';
            currentEl.classList.add('old-financing-section');
            count++;
            
            // Si c'est un titre de financement, continuer
            if (isHeading && containsFinancingKeyword(currentEl.textContent)) {
                continue;
            }
            
            // Si l'élément ne parle plus de financement, on peut arrêter
            if (!containsFinancingKeyword(currentEl.textContent)) {
                break;
            }
        }
    }
    
    // 1. PRIORITÉ MAX : Masquer tous les titres (h1-h6) mentionnant "financement" et TOUT ce qui suit
    const headings = adContent.querySelectorAll('h1, h2, h3, h4, h5, h6');
    headings.forEach(heading => {
        const text = heading.textContent.trim();
        if (containsFinancingKeyword(text)) {
            console.log('🚫 Masquage du titre financement:', text);
            hideElementAndFollowing(heading);
        }
    });
    
    // 2. Masquer tous les paragraphes restants mentionnant le financement
    const paragraphs = adContent.querySelectorAll('p');
    paragraphs.forEach(p => {
        if (p.style.display !== 'none' && containsFinancingKeyword(p.textContent)) {
            p.style.display = 'none';
            p.classList.add('old-financing-section');
        }
    });
    
    // 3. Masquer toutes les divs avec classes de financement
    const divs = adContent.querySelectorAll('div');
    divs.forEach(div => {
        if (div.style.display !== 'none' && containsFinancingKeyword(div.textContent)) {
            if (div.className.includes('bg-yellow') || 
                div.className.includes('bg-green') || 
                div.className.includes('border-l-4') ||
                div.className.includes('border-yellow')) {
                div.style.display = 'none';
                div.classList.add('old-financing-section');
            }
        }
    });
    
    // 4. Masquer les listes contenant des infos de financement
    const lists = adContent.querySelectorAll('ul, ol');
    lists.forEach(list => {
        if (list.style.display !== 'none' && containsFinancingKeyword(list.textContent)) {
            list.style.display = 'none';
            list.classList.add('old-financing-section');
        }
    });
    
    // 5. Nettoyage final : balayage de tous les éléments restants
    const allElements = adContent.querySelectorAll('*');
    allElements.forEach(el => {
        if (el.style.display !== 'none') {
            const text = el.textContent.trim();
            // Si l'élément contient UNIQUEMENT du contenu de financement (pas d'autre contenu utile)
            if (text.length > 20 && containsFinancingKeyword(text)) {
                const financingRatio = financingKeywords.filter(k => text.toLowerCase().includes(k)).length;
                if (financingRatio >= 2) {
                    el.style.display = 'none';
                    el.classList.add('old-financing-section');
                }
            }
        }
    });
    
    console.log('✅ Script de masquage du financement terminé');
});
</script>
@endpush

@endsection