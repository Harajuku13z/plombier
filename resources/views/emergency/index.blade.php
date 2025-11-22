@extends('layouts.app')

@section('title', '🚨 SOS URGENCE Plomberie 24h/7j - ' . setting('company_city', 'Versailles'))
@section('description', 'Plombier d\'urgence disponible 24h/7j à ' . setting('company_city', 'Versailles') . '. Intervention rapide pour fuite d\'eau, dégât des eaux, canalisation bouchée.')

@push('head')
<style>
    :root {
        --primary-color: {{ setting('primary_color', '#2563eb') }};
        --secondary-color: {{ setting('secondary_color', '#0284c7') }};
        --accent-color: {{ setting('accent_color', '#dc2626') }};
    }
    
    @keyframes emergency-pulse {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
        }
        50% {
            transform: scale(1.02);
            box-shadow: 0 0 0 20px rgba(220, 38, 38, 0);
        }
    }
    
    .emergency-alert {
        animation: emergency-pulse 2s infinite;
    }
</style>
@endpush

@section('content')

<!-- Hero Emergency Section -->
<section class="bg-gradient-to-br from-red-600 via-red-700 to-red-800 text-white py-16 md:py-24 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-96 h-96 bg-red-500 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-red-900 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <div class="inline-flex items-center justify-center bg-white text-red-600 rounded-full p-4 mb-6 emergency-alert">
                <i class="fas fa-exclamation-triangle text-5xl"></i>
            </div>
            
            <h1 class="text-4xl md:text-6xl font-black mb-6">
                🚨 SOS URGENCE PLOMBERIE
            </h1>
            
            <p class="text-2xl md:text-3xl mb-8 text-red-100">
                Intervention Rapide 24h/7j
            </p>
            
            <div class="bg-white/20 backdrop-blur-sm rounded-2xl p-6 mb-8 inline-block">
                <div class="text-xl font-bold mb-2">Zone d'intervention</div>
                <div class="text-3xl font-black">
                    {{ setting('company_city', 'Versailles') }} 
                    <span class="text-red-200">({{ substr(setting('company_postal_code', '78'), 0, 2) }})</span>
                </div>
                <div class="text-red-100 mt-2">et tout le département</div>
            </div>
            
            <a href="tel:{{ str_replace(' ', '', setting('company_phone', '')) }}" 
               class="inline-block bg-white text-red-600 hover:bg-red-50 px-12 py-6 rounded-full font-black text-3xl md:text-4xl shadow-2xl transition transform hover:scale-105 mb-8">
                <i class="fas fa-phone-alt mr-3"></i>
                {{ setting('company_phone', '07 86 48 65 39') }}
            </a>
            
            <div class="grid md:grid-cols-3 gap-6 mt-12">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6">
                    <i class="fas fa-clock text-4xl mb-3"></i>
                    <div class="font-bold text-xl">Disponible 24h/7j</div>
                    <div class="text-red-100 text-sm">Nuits, week-ends, jours fériés</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6">
                    <i class="fas fa-bolt text-4xl mb-3"></i>
                    <div class="font-bold text-xl">Intervention < 1h</div>
                    <div class="text-red-100 text-sm">En cas d'urgence critique</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6">
                    <i class="fas fa-tools text-4xl mb-3"></i>
                    <div class="font-bold text-xl">Équipé & Certifié</div>
                    <div class="text-red-100 text-sm">Matériel professionnel</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services d'Urgence -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-black text-gray-900 mb-4">
                Nos Services d'Urgence
            </h2>
            <p class="text-xl text-gray-600">Intervention rapide pour tous types d'urgence</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition">
                <div class="bg-red-100 text-red-600 w-14 h-14 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-tint text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Fuite d'eau</h3>
                <p class="text-gray-600">Robinetterie, tuyauterie, raccords - Intervention immédiate</p>
            </div>
            
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition">
                <div class="bg-red-100 text-red-600 w-14 h-14 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-water text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Dégât des eaux</h3>
                <p class="text-gray-600">Arrêt de l'eau, réparation, expertise</p>
            </div>
            
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition">
                <div class="bg-red-100 text-red-600 w-14 h-14 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-toilet text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">WC/Éviers Bouchés</h3>
                <p class="text-gray-600">Débouchage professionnel avec matériel adapté</p>
            </div>
            
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition">
                <div class="bg-red-100 text-red-600 w-14 h-14 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-fire text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Chauffe-eau en panne</h3>
                <p class="text-gray-600">Dépannage ou remplacement d'urgence</p>
            </div>
            
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition">
                <div class="bg-red-100 text-red-600 w-14 h-14 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-thermometer-empty text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Panne de chauffage</h3>
                <p class="text-gray-600">Réparation chaudière, radiateurs</p>
            </div>
            
            <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition">
                <div class="bg-red-100 text-red-600 w-14 h-14 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-wrench text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Casse de canalisation</h3>
                <p class="text-gray-600">Recherche de fuite, réparation</p>
            </div>
        </div>
    </div>
</section>

<!-- Formulaire d'Urgence -->
<section id="formulaire-urgence" class="py-16 bg-white">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-black text-gray-900 mb-4">
                Demande d'Intervention d'Urgence
            </h2>
            <p class="text-xl text-gray-600">
                Remplissez ce formulaire, nous vous recontactons immédiatement
            </p>
        </div>

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('urgence.submit') }}" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-xl p-8">
            @csrf
            
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">
                        <i class="fas fa-user text-red-600 mr-2"></i>
                        Nom complet *
                    </label>
                    <input type="text" name="name" required
                           value="{{ old('name') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="Jean Dupont">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">
                        <i class="fas fa-phone text-red-600 mr-2"></i>
                        Téléphone *
                    </label>
                    <input type="tel" name="phone" required
                           value="{{ old('phone') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="06 12 34 56 78">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">
                        <i class="fas fa-envelope text-red-600 mr-2"></i>
                        Email *
                    </label>
                    <input type="email" name="email" required
                           value="{{ old('email') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="jean.dupont@email.com">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">
                        <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                        Type d'urgence *
                    </label>
                    <select name="emergency_type" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="">-- Sélectionnez --</option>
                        <option value="fuite-eau">Fuite d'eau</option>
                        <option value="degat-eaux">Dégât des eaux</option>
                        <option value="wc-bouche">WC/Éviers bouchés</option>
                        <option value="chauffe-eau">Chauffe-eau en panne</option>
                        <option value="chauffage">Panne de chauffage</option>
                        <option value="canalisation">Casse de canalisation</option>
                        <option value="autre">Autre urgence</option>
                    </select>
                    @error('emergency_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-900 mb-2">
                    <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>
                    Adresse complète de l'intervention *
                </label>
                <input type="text" name="address" required
                       value="{{ old('address') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                       placeholder="35 Rue des Chantiers, 78000 Versailles">
                @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-900 mb-2">
                    <i class="fas fa-comment-alt text-red-600 mr-2"></i>
                    Description de l'urgence *
                </label>
                <textarea name="description" rows="5" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                          placeholder="Décrivez précisément l'urgence : localisation, symptômes, gravité...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-900 mb-2">
                    <i class="fas fa-camera text-red-600 mr-2"></i>
                    Photos de la situation (optionnel)
                </label>
                <input type="file" name="photos[]" multiple accept="image/*"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <p class="text-sm text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    Maximum 5 photos, 5MB chacune
                </p>
                @error('photos.*')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Alert Box -->
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
                <div class="flex items-start gap-3">
                    <i class="fas fa-lightbulb text-yellow-600 text-xl flex-shrink-0 mt-1"></i>
                    <div class="text-sm text-gray-700">
                        <strong>Conseil :</strong> Pour une urgence immédiate nécessitant une intervention dans l'heure, 
                        <strong>appelez-nous directement</strong> au {{ setting('company_phone', '07 86 48 65 39') }}. 
                        Le formulaire est traité sous 15-30 minutes.
                    </div>
                </div>
            </div>

            <button type="submit" 
                    class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white py-5 rounded-xl font-bold text-xl shadow-lg transition transform hover:scale-105 flex items-center justify-center gap-3">
                <i class="fas fa-paper-plane"></i>
                <span>Envoyer la Demande d'Urgence</span>
            </button>
        </form>
    </div>
</section>

<!-- Pourquoi nous choisir en urgence -->
<section class="py-16 bg-gradient-to-b from-white to-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto">
            <h2 class="text-4xl font-black text-gray-900 mb-12 text-center">
                Pourquoi nous appeler en urgence ?
            </h2>
            
            <div class="grid md:grid-cols-2 gap-8">
                <div class="flex items-start gap-4">
                    <div class="bg-primary text-white w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-gray-900 mb-2">Réactivité Garantie</h3>
                        <p class="text-gray-600">Prise en charge immédiate de votre urgence, 7j/7</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-4">
                    <div class="bg-primary text-white w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-tools text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-gray-900 mb-2">Équipement Complet</h3>
                        <p class="text-gray-600">Véhicule équipé pour 95% des interventions</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-4">
                    <div class="bg-primary text-white w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-certificate text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-gray-900 mb-2">Plombiers Certifiés</h3>
                        <p class="text-gray-600">Artisans qualifiés et assurés</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-4">
                    <div class="bg-primary text-white w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-euro-sign text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-gray-900 mb-2">Tarifs Transparents</h3>
                        <p class="text-gray-600">Devis clair avant intervention, pas de surprise</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Final -->
<section class="py-16 bg-gradient-to-br from-red-600 to-red-800 text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-black mb-6">
            Ne laissez pas l'urgence s'aggraver
        </h2>
        <p class="text-xl text-red-100 mb-8">
            Chaque minute compte en cas de fuite ou dégât des eaux
        </p>
        <a href="tel:{{ str_replace(' ', '', setting('company_phone', '')) }}" 
           class="inline-block bg-white text-red-600 hover:bg-red-50 px-12 py-6 rounded-full font-black text-2xl md:text-3xl shadow-2xl transition transform hover:scale-105">
            <i class="fas fa-phone-alt mr-3"></i>
            {{ setting('company_phone', '07 86 48 65 39') }}
        </a>
    </div>
</section>

@endsection

