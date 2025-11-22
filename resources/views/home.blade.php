@extends('layouts.app')

@section('title', setting('company_name', 'Plombier Versailles') . ' - Plomberie Professionnelle 24/7')
@section('description', 'Plombier professionnel à ' . setting('company_city', 'Versailles') . '. Intervention rapide, devis gratuit. Urgence 24h/7j.')

@push('head')
<style>
    :root {
        --primary-color: {{ setting('primary_color', '#2563eb') }};
        --secondary-color: {{ setting('secondary_color', '#0284c7') }};
        --accent-color: {{ setting('accent_color', '#dc2626') }};
    }
    
    .bg-primary { background-color: var(--primary-color); }
    .text-primary { color: var(--primary-color); }
    .border-primary { border-color: var(--primary-color); }
    .hover\:bg-primary:hover { background-color: var(--primary-color); }
    .bg-secondary { background-color: var(--secondary-color); }
    .text-secondary { color: var(--secondary-color); }
    .bg-accent { background-color: var(--accent-color); }
    .text-accent { color: var(--accent-color); }
    
    /* Hero moderne */
    .hero-section {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        position: relative;
        overflow: hidden;
    }
    
    .hero-pattern {
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    
    /* Emergency pulse animation */
    @keyframes emergency-pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.05);
            opacity: 0.9;
        }
    }
    
    .emergency-card {
        animation: emergency-pulse 2s ease-in-out infinite;
        box-shadow: 0 20px 60px rgba(220, 38, 38, 0.3);
    }
    
    /* How it works steps */
    .step-card {
        position: relative;
        transition: all 0.3s ease;
    }
    
    .step-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }
    
    .step-number {
        font-size: 4rem;
        font-weight: 900;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        opacity: 0.2;
        position: absolute;
        top: -20px;
        right: 20px;
    }
    
    .service-card {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        border-color: var(--primary-color);
    }
    
    /* CTA Button */
    .cta-button {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .cta-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s ease;
    }
    
    .cta-button:hover::before {
        left: 100%;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .step-number {
            font-size: 2.5rem;
            top: -10px;
            right: 10px;
        }
    }
</style>
@endpush

@section('content')

<!-- Hero Section -->
<section class="hero-section hero-pattern text-white py-20 md:py-32">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-6xl font-black mb-6 leading-tight">
                Plombier Professionnel
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-white to-blue-200">
                    {{ $companySettings['city'] ?? 'Versailles' }}
                </span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-blue-100">
                Intervention Rapide • Devis Gratuit • Service 24h/7j
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                <a href="tel:{{ str_replace(' ', '', $companySettings['phone'] ?? '') }}" 
                   class="cta-button bg-white text-primary hover:bg-blue-50 px-8 py-4 rounded-full font-bold text-lg shadow-2xl inline-flex items-center gap-3 transition">
                    <i class="fas fa-phone-alt text-2xl"></i>
                    <span>{{ $companySettings['phone'] ?? '07 86 48 65 39' }}</span>
                </a>
                
                <a href="#devis" 
                   class="cta-button bg-accent hover:bg-red-700 text-white px-8 py-4 rounded-full font-bold text-lg shadow-2xl inline-flex items-center gap-3 transition">
                    <i class="fas fa-file-alt"></i>
                    <span>Devis Gratuit</span>
                </a>
            </div>
            
            <!-- Trust indicators -->
            <div class="grid grid-cols-3 gap-6 max-w-2xl mx-auto">
                <div class="text-center">
                    <div class="text-3xl font-bold mb-1">{{ $trustCounter }}+</div>
                    <div class="text-sm text-blue-200">Interventions</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold mb-1">98%</div>
                    <div class="text-sm text-blue-200">Clients Satisfaits</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold mb-1">24h/7j</div>
                    <div class="text-sm text-blue-200">Disponibilité</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Urgence SOS -->
<section class="py-16 bg-gradient-to-b from-red-50 to-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-64 h-64 bg-red-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-red-500 rounded-full blur-3xl"></div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto">
            <div class="emergency-card bg-gradient-to-br from-red-600 to-red-700 rounded-3xl p-8 md:p-12 text-white shadow-2xl">
                <div class="flex items-center justify-center mb-6">
                    <div class="bg-white text-red-600 rounded-full p-4 animate-pulse">
                        <i class="fas fa-exclamation-triangle text-4xl"></i>
                    </div>
                </div>
                
                <h2 class="text-3xl md:text-5xl font-black text-center mb-4">
                    🚨 URGENCE PLOMBERIE
                </h2>
                
                <p class="text-xl md:text-2xl text-center mb-8 text-red-100">
                    Fuite d'eau, Canalisation bouchée, Dégât des eaux ?
                </p>
                
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 mb-8">
                    <div class="text-center">
                        <div class="text-sm uppercase tracking-wider mb-2 text-red-200">Intervention Rapide</div>
                        <div class="text-2xl md:text-3xl font-bold mb-2">
                            {{ $companySettings['city'] ?? 'Versailles' }} ({{ substr($companySettings['postal_code'] ?? '78', 0, 2) }})
                        </div>
                        <div class="text-red-100">et tout le département</div>
                    </div>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4 mb-8">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 flex items-center gap-3">
                        <i class="fas fa-clock text-3xl"></i>
                        <div>
                            <div class="font-bold">Disponible 24h/7j</div>
                            <div class="text-sm text-red-100">Même nuits et week-ends</div>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 flex items-center gap-3">
                        <i class="fas fa-bolt text-3xl"></i>
                        <div>
                            <div class="font-bold">Intervention < 1h</div>
                            <div class="text-sm text-red-100">En cas d'urgence</div>
                        </div>
                    </div>
                </div>
                
                <a href="tel:{{ str_replace(' ', '', $companySettings['phone'] ?? '') }}" 
                   class="block bg-white text-red-600 hover:bg-red-50 text-center px-8 py-5 rounded-full font-black text-2xl shadow-2xl transition transform hover:scale-105">
                    <i class="fas fa-phone-alt mr-3"></i>
                    {{ $companySettings['phone'] ?? '07 86 48 65 39' }}
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Section Comment Ça Marche -->
<section class="py-20 bg-gradient-to-b from-white to-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-black mb-4 text-gray-900">
                Comment Ça Marche ?
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Un processus simple et transparent en 4 étapes
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-7xl mx-auto">
            <!-- Étape 1 -->
            <div class="step-card bg-white rounded-2xl p-8 shadow-lg relative">
                <div class="step-number">01</div>
                <div class="bg-primary text-white w-16 h-16 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fas fa-file-alt text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-gray-900">Demande de Devis</h3>
                <p class="text-gray-600">
                    Remplissez notre formulaire en ligne pour recevoir un devis personnalisé et gratuit.
                </p>
            </div>
            
            <!-- Étape 2 -->
            <div class="step-card bg-white rounded-2xl p-8 shadow-lg relative">
                <div class="step-number">02</div>
                <div class="bg-secondary text-white w-16 h-16 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fas fa-search text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-gray-900">Étude du Projet</h3>
                <p class="text-gray-600">
                    Nos experts analysent vos besoins et vous proposent la meilleure solution.
                </p>
            </div>
            
            <!-- Étape 3 -->
            <div class="step-card bg-white rounded-2xl p-8 shadow-lg relative">
                <div class="step-number">03</div>
                <div class="bg-primary text-white w-16 h-16 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fas fa-calendar-alt text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-gray-900">Planification</h3>
                <p class="text-gray-600">
                    Nous planifions les travaux selon vos disponibilités et nos délais d'intervention.
                </p>
            </div>
            
            <!-- Étape 4 -->
            <div class="step-card bg-white rounded-2xl p-8 shadow-lg relative">
                <div class="step-number">04</div>
                <div class="bg-secondary text-white w-16 h-16 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fas fa-tools text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-gray-900">Réalisation</h3>
                <p class="text-gray-600">
                    Nos équipes qualifiées réalisent vos travaux avec professionnalisme et qualité.
                </p>
            </div>
        </div>
        
        <div class="text-center mt-12">
            <a href="#devis" class="cta-button inline-flex items-center gap-3 bg-primary hover:bg-blue-700 text-white px-8 py-4 rounded-full font-bold text-lg shadow-lg transition">
                <i class="fas fa-rocket"></i>
                <span>Démarrer Mon Projet</span>
            </a>
        </div>
    </div>
</section>

<!-- Section Services -->
@if(!empty($services) && count($services) > 0)
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-black mb-4 text-gray-900">
                Nos Services de Plomberie
            </h2>
            <p class="text-xl text-gray-600">
                Des prestations complètes pour tous vos besoins
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            @foreach(array_slice($services, 0, 6) as $service)
            <div class="service-card bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl">
                <div class="bg-gradient-to-br from-primary to-secondary text-white w-16 h-16 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fas fa-wrench text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-gray-900">
                    {{ $service['name'] ?? 'Service' }}
                </h3>
                <p class="text-gray-600 mb-6">
                    {{ $service['description'] ?? '' }}
                </p>
                @if(isset($service['slug']))
                <a href="{{ route('services.show', $service['slug']) }}" 
                   class="text-primary hover:text-secondary font-semibold inline-flex items-center gap-2 transition">
                    En savoir plus
                    <i class="fas fa-arrow-right"></i>
                </a>
                @endif
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-12">
            <a href="{{ route('services.index') }}" 
               class="inline-flex items-center gap-3 bg-secondary hover:bg-blue-700 text-white px-8 py-4 rounded-full font-bold text-lg shadow-lg transition">
                <i class="fas fa-th"></i>
                <span>Voir Tous Nos Services</span>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Section Avis Clients -->
@if($reviews && count($reviews) > 0)
<section class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-black mb-4 text-gray-900">
                Ils Nous Font Confiance
            </h2>
            <div class="flex items-center justify-center gap-2 text-yellow-400 text-2xl mb-4">
                @for($i = 0; $i < 5; $i++)
                    <i class="fas fa-star"></i>
                @endfor
                <span class="text-gray-700 ml-2">({{ number_format($averageRating, 1) }}/5)</span>
            </div>
            <p class="text-xl text-gray-600">
                {{ $totalReviews }} avis clients
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            @foreach(array_slice($reviews->toArray(), 0, 6) as $review)
            <div class="bg-white rounded-2xl p-8 shadow-lg">
                <div class="flex items-center gap-1 text-yellow-400 mb-4">
                    @for($i = 0; $i < 5; $i++)
                        <i class="fas fa-star{{ $i < $review['rating'] ? '' : '-o' }}"></i>
                    @endfor
                </div>
                <p class="text-gray-700 mb-6 italic">"{{ $review['comment'] ?? '' }}"</p>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($review['author_name'] ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-bold text-gray-900">{{ $review['author_name'] ?? 'Client' }}</div>
                        <div class="text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($review['review_date'] ?? now())->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Section CTA Final -->
<section id="devis" class="py-20 bg-gradient-to-br from-primary to-secondary text-white relative overflow-hidden">
    <div class="hero-pattern absolute inset-0"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-4xl md:text-5xl font-black mb-6">
                Prêt à Démarrer Votre Projet ?
            </h2>
            <p class="text-xl md:text-2xl mb-12 text-blue-100">
                Obtenez votre devis gratuit en quelques minutes
            </p>
            
            <div class="flex flex-col sm:flex-row gap-6 justify-center">
                <a href="{{ route('form.step', 'propertyType') }}" 
                   class="cta-button bg-white text-primary hover:bg-blue-50 px-10 py-5 rounded-full font-bold text-xl shadow-2xl inline-flex items-center justify-center gap-3 transition">
                    <i class="fas fa-file-signature"></i>
                    <span>Demander un Devis</span>
                </a>
                
                <a href="tel:{{ str_replace(' ', '', $companySettings['phone'] ?? '') }}" 
                   class="cta-button bg-accent hover:bg-red-700 text-white px-10 py-5 rounded-full font-bold text-xl shadow-2xl inline-flex items-center justify-center gap-3 transition">
                    <i class="fas fa-phone-alt"></i>
                    <span>Appeler Maintenant</span>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

