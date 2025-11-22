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
                <span class="text-sm font-semibold text-primary">{{ $progress }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-gradient-to-r from-primary to-secondary h-3 rounded-full transition-all duration-500" 
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

        <!-- Form -->
        <form method="POST" action="{{ route('simulator.submit', 'contact') }}">
            @csrf
            
            <div class="bg-white rounded-2xl p-8 shadow-xl mb-8">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Nom -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-user text-primary mr-2"></i>
                            Nom complet *
                        </label>
                        <input type="text" name="name" required
                               value="{{ old('name', $data['name'] ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary @error('name') border-red-500 @enderror"
                               placeholder="Jean Dupont">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-envelope text-primary mr-2"></i>
                            Email *
                        </label>
                        <input type="email" name="email" required
                               value="{{ old('email', $data['email'] ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary @error('email') border-red-500 @enderror"
                               placeholder="jean.dupont@email.com">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-phone text-primary mr-2"></i>
                            Téléphone *
                        </label>
                        <input type="tel" name="phone" required
                               value="{{ old('phone', $data['phone'] ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary @error('phone') border-red-500 @enderror"
                               placeholder="06 12 34 56 78">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Code Postal -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-map-pin text-primary mr-2"></i>
                            Code Postal *
                        </label>
                        <input type="text" name="postal_code" required
                               value="{{ old('postal_code', $data['postal_code'] ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary @error('postal_code') border-red-500 @enderror"
                               placeholder="78000">
                        @error('postal_code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <!-- Ville -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-city text-primary mr-2"></i>
                            Ville *
                        </label>
                        <input type="text" name="city" required
                               value="{{ old('city', $data['city'] ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary @error('city') border-red-500 @enderror"
                               placeholder="Versailles">
                        @error('city')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Adresse -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                            Adresse complète *
                        </label>
                        <input type="text" name="address" required
                               value="{{ old('address', $data['address'] ?? '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary @error('address') border-red-500 @enderror"
                               placeholder="35 Rue des Chantiers">
                        @error('address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
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
                        style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-check-circle"></i>
                    <span>Recevoir Mon Devis</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

