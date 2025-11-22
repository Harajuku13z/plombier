@extends('layouts.app')

@section('title', 'Simulateur de Prix - Type de Bien')

@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    .progress-bar {
        background-color: {{ setting('primary_color', '#2563eb') }};
    }
    .property-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .property-card:hover {
        transform: translateY(-4px);
    }
    .property-card input[type="radio"]:checked + div {
        background-color: #eff6ff !important;
        border-color: {{ setting('primary_color', '#2563eb') }} !important;
        border-width: 3px !important;
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25) !important;
    }
    
    .property-card:has(input:checked) {
        transform: translateY(-4px);
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
                Quel type de bien ?
            </h1>
            <p class="text-xl text-gray-600">
                Sélectionnez le type de votre propriété
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('simulator.submit', 'property-type') }}">
            @csrf
            
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <!-- Maison -->
                <label class="property-card">
                    <input type="radio" name="property_type" value="house" 
                           class="sr-only" required
                           {{ (old('property_type', $data['property_type'] ?? '') == 'house') ? 'checked' : '' }}>
                    <div class="bg-white border-2 border-gray-300 rounded-2xl p-8 text-center h-full">
                        <i class="fas fa-home text-6xl mb-4" style="color: {{ setting('primary_color', '#2563eb') }};"></i>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Maison</h3>
                        <p class="text-gray-600 font-medium">Maison individuelle ou mitoyenne</p>
                    </div>
                </label>

                <!-- Appartement -->
                <label class="property-card">
                    <input type="radio" name="property_type" value="apartment" 
                           class="sr-only" required
                           {{ (old('property_type', $data['property_type'] ?? '') == 'apartment') ? 'checked' : '' }}>
                    <div class="bg-white border-2 border-gray-300 rounded-2xl p-8 text-center h-full">
                        <i class="fas fa-building text-6xl mb-4" style="color: {{ setting('primary_color', '#2563eb') }};"></i>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Appartement</h3>
                        <p class="text-gray-600 font-medium">Appartement en immeuble</p>
                    </div>
                </label>

                <!-- Commercial -->
                <label class="property-card">
                    <input type="radio" name="property_type" value="commercial" 
                           class="sr-only" required
                           {{ (old('property_type', $data['property_type'] ?? '') == 'commercial') ? 'checked' : '' }}>
                    <div class="bg-white border-2 border-gray-300 rounded-2xl p-8 text-center h-full">
                        <i class="fas fa-store text-6xl mb-4" style="color: {{ setting('primary_color', '#2563eb') }};"></i>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Commercial</h3>
                        <p class="text-gray-600 font-medium">Local professionnel, boutique</p>
                    </div>
                </label>

                <!-- Autre -->
                <label class="property-card">
                    <input type="radio" name="property_type" value="other" 
                           class="sr-only" required
                           {{ (old('property_type', $data['property_type'] ?? '') == 'other') ? 'checked' : '' }}>
                    <div class="bg-white border-2 border-gray-300 rounded-2xl p-8 text-center h-full">
                        <i class="fas fa-question-circle text-6xl mb-4" style="color: {{ setting('primary_color', '#2563eb') }};"></i>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Autre</h3>
                        <p class="text-gray-600 font-medium">Copropriété, bureau, etc.</p>
                    </div>
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex justify-between items-center">
                <a href="{{ route('simulator.previous', 'property-type') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 border-2 border-gray-300 rounded-full font-semibold text-gray-700 hover:bg-gray-100 transition">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour</span>
                </a>
                
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-8 py-4 text-white rounded-full font-bold text-lg shadow-xl transition transform hover:scale-105"
                        style="background: linear-gradient(135deg, {{ setting('primary_color', '#2563eb') }} 0%, {{ setting('secondary_color', '#0284c7') }} 100%);">
                    <span>Suivant</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

