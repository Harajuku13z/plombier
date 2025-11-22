@extends('layouts.app')

@section('title', 'Simulateur de Prix - Vos Coordonnées')

@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    .progress-bar {
        background-color: {{ setting('primary_color', '#2563eb') }};
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-white py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-semibold text-gray-600">Étape {{ $currentStepIndex + 1 }} sur {{ $totalSteps ?? 5 }}</span>
                <span class="text-sm font-semibold" style="color: {{ setting('primary_color', '#2563eb') }};">{{ $progress }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                <div class="progress-bar h-3 rounded-full transition-all duration-500" 
                     style="width: {{ $progress }}%"></div>
            </div>
        </div>

        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-4">
                Vos Coordonnées
            </h1>
            <p class="text-xl text-gray-600">
                Pour recevoir votre devis personnalisé gratuit
            </p>
        </div>

        <!-- Errors Display - TRÈS VISIBLE -->
        @if($errors->any())
            <div class="bg-red-600 text-white p-8 mb-8 rounded-2xl shadow-2xl border-4 border-red-800 animate-pulse">
                <div class="flex items-start gap-4">
                    <i class="fas fa-exclamation-triangle text-6xl flex-shrink-0"></i>
                    <div class="flex-1">
                        <p class="font-black text-3xl mb-4">
                            ⚠️ ERREUR - FORMULAIRE INCOMPLET
                        </p>
                        <ul class="space-y-3 text-xl">
                            @foreach($errors->all() as $error)
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-times-circle text-2xl mt-1"></i>
                                    <span class="font-bold">{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-red-600 text-white p-8 mb-8 rounded-2xl shadow-2xl border-4 border-red-800">
                <div class="flex items-start gap-4">
                    <i class="fas fa-exclamation-circle text-6xl flex-shrink-0"></i>
                    <div class="flex-1">
                        <p class="font-black text-3xl mb-2">ERREUR</p>
                        <p class="text-xl">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Debug Info - TOUJOURS VISIBLE -->
        <div class="bg-yellow-100 border-2 border-yellow-600 p-6 mb-6 rounded-xl">
            <p class="font-bold text-yellow-900 text-lg mb-3">
                <i class="fas fa-info-circle mr-2"></i>
                Debug Info - Données en Session :
            </p>
            <div class="bg-white p-4 rounded-lg text-sm font-mono">
                <p class="mb-2"><strong>work_types:</strong> {{ isset($data['work_types']) ? json_encode($data['work_types']) : '❌ MANQUANT' }}</p>
                <p class="mb-2"><strong>urgency:</strong> {{ $data['urgency'] ?? '❌ MANQUANT' }}</p>
                <p class="mb-2"><strong>property_type:</strong> {{ $data['property_type'] ?? '❌ MANQUANT' }}</p>
                <p class="mb-2"><strong>description:</strong> {{ isset($data['description']) ? '✅ Présent' : '⚠️ Vide' }}</p>
                <p class="mb-2"><strong>photo_paths:</strong> {{ isset($data['photo_paths']) ? count($data['photo_paths']) . ' photo(s)' : '⚠️ Aucune' }}</p>
            </div>
            <p class="text-yellow-800 mt-3 font-semibold">
                Si des données sont MANQUANTES ci-dessus, recommencez le simulateur depuis le début.
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('simulator.submit', 'contact') }}" id="contact-form">
            @csrf
            
            <div class="bg-white rounded-2xl p-8 shadow-xl mb-8">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Nom -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-user mr-2" style="color: {{ setting('primary_color', '#2563eb') }};"></i>
                            Nom complet *
                        </label>
                        <input type="text" name="name" required minlength="2" maxlength="255"
                               value="{{ old('name', $data['name'] ?? '') }}"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:border-blue-500 @error('name') border-red-500 bg-red-50 @enderror"
                               placeholder="Jean Dupont">
                        @error('name')
                            <p class="text-red-600 font-bold text-sm mt-2 flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-envelope mr-2" style="color: {{ setting('primary_color', '#2563eb') }};"></i>
                            Email *
                        </label>
                        <input type="email" name="email" required maxlength="255"
                               value="{{ old('email', $data['email'] ?? '') }}"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:border-blue-500 @error('email') border-red-500 bg-red-50 @enderror"
                               placeholder="jean.dupont@email.com">
                        @error('email')
                            <p class="text-red-600 font-bold text-sm mt-2 flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-phone mr-2" style="color: {{ setting('primary_color', '#2563eb') }};"></i>
                            Téléphone *
                        </label>
                        <input type="tel" name="phone" required minlength="10" maxlength="20"
                               value="{{ old('phone', $data['phone'] ?? '') }}"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:border-blue-500 @error('phone') border-red-500 bg-red-50 @enderror"
                               placeholder="06 12 34 56 78">
                        @error('phone')
                            <p class="text-red-600 font-bold text-sm mt-2 flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Code Postal -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-map-pin mr-2" style="color: {{ setting('primary_color', '#2563eb') }};"></i>
                            Code Postal *
                        </label>
                        <input type="text" name="postal_code" required minlength="4" maxlength="10"
                               value="{{ old('postal_code', $data['postal_code'] ?? '') }}"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:border-blue-500 @error('postal_code') border-red-500 bg-red-50 @enderror"
                               placeholder="78000">
                        @error('postal_code')
                            <p class="text-red-600 font-bold text-sm mt-2 flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <!-- Ville -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-city mr-2" style="color: {{ setting('primary_color', '#2563eb') }};"></i>
                            Ville *
                        </label>
                        <input type="text" name="city" required minlength="2" maxlength="100"
                               value="{{ old('city', $data['city'] ?? '') }}"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:border-blue-500 @error('city') border-red-500 bg-red-50 @enderror"
                               placeholder="Versailles">
                        @error('city')
                            <p class="text-red-600 font-bold text-sm mt-2 flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Adresse -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-map-marker-alt mr-2" style="color: {{ setting('primary_color', '#2563eb') }};"></i>
                            Adresse complète *
                        </label>
                        <input type="text" name="address" required minlength="5" maxlength="500"
                               value="{{ old('address', $data['address'] ?? '') }}"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:border-blue-500 @error('address') border-red-500 bg-red-50 @enderror"
                               placeholder="35 Rue des Chantiers">
                        @error('address')
                            <p class="text-red-600 font-bold text-sm mt-2 flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="border-l-4 rounded-lg p-6 mb-8" 
                 style="background-color: #eff6ff; border-color: {{ setting('primary_color', '#2563eb') }};">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-2xl flex-shrink-0 mt-1" style="color: {{ setting('primary_color', '#2563eb') }};"></i>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-2">Pourquoi ces informations ?</h4>
                        <p class="text-gray-700 text-sm">
                            Vos coordonnées nous permettent de vous contacter rapidement pour vous proposer un devis personnalisé adapté à votre situation. Nous respectons votre vie privée et ne partageons jamais vos données.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-between items-center">
                <a href="{{ route('simulator.previous', 'contact') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 border-2 border-gray-300 rounded-full font-semibold text-gray-700 hover:bg-gray-100 transition">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour</span>
                </a>
                
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-8 py-4 text-white rounded-full font-bold text-lg shadow-xl transition transform hover:scale-105"
                        style="background-color: #10b981;">
                    <i class="fas fa-check-circle"></i>
                    <span>Recevoir Mon Devis</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contact-form');
    
    // Validation en temps réel
    const inputs = form.querySelectorAll('input[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('border-red-500')) {
                validateField(this);
            }
        });
    });
    
    function validateField(field) {
        const value = field.value.trim();
        const minLength = parseInt(field.getAttribute('minlength')) || 0;
        
        if (!value || value.length < minLength) {
            field.classList.add('border-red-500', 'bg-red-50');
            field.classList.remove('border-gray-300');
            return false;
        } else if (field.type === 'email' && !value.includes('@')) {
            field.classList.add('border-red-500', 'bg-red-50');
            field.classList.remove('border-gray-300');
            return false;
        } else {
            field.classList.remove('border-red-500', 'bg-red-50');
            field.classList.add('border-gray-300');
            return true;
        }
    }
    
    form.addEventListener('submit', function(e) {
        console.log('Form submitting...');
        
        let isValid = true;
        let errors = [];
        
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
                const label = input.closest('div').querySelector('label').textContent.trim();
                errors.push(label.replace('*', '').trim());
                console.log('Invalid field:', input.name, 'Value:', input.value);
            } else {
                console.log('Valid field:', input.name, 'Value:', input.value);
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            console.error('Form validation failed. Errors:', errors);
            alert('⚠️ Veuillez remplir correctement tous les champs obligatoires :\n\n• ' + errors.join('\n• '));
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return false;
        }
        
        console.log('Form validation passed, submitting...');
        
        // Afficher un message de chargement
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Envoi en cours...';
    });
});
</script>
@endsection

