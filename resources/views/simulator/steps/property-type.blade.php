@extends('layouts.app')

@section('title', 'Simulateur de Prix - Type de Bien')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-white py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-semibold text-gray-600">Étape {{ $currentStepIndex + 1 }} sur 5</span>
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
                <label class="relative cursor-pointer group">
                    <input type="radio" name="property_type" value="house" 
                           class="peer sr-only" required
                           {{ (old('property_type', $data['property_type'] ?? '') == 'house') ? 'checked' : '' }}>
                    <div class="bg-white border-3 border-gray-200 rounded-2xl p-8 transition-all duration-300
                                peer-checked:border-primary peer-checked:bg-blue-50 peer-checked:shadow-xl
                                hover:border-primary hover:shadow-lg text-center">
                        <i class="fas fa-home text-5xl text-primary mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Maison</h3>
                        <p class="text-gray-600">Maison individuelle ou mitoyenne</p>
                    </div>
                </label>

                <!-- Appartement -->
                <label class="relative cursor-pointer group">
                    <input type="radio" name="property_type" value="apartment" 
                           class="peer sr-only" required
                           {{ (old('property_type', $data['property_type'] ?? '') == 'apartment') ? 'checked' : '' }}>
                    <div class="bg-white border-3 border-gray-200 rounded-2xl p-8 transition-all duration-300
                                peer-checked:border-primary peer-checked:bg-blue-50 peer-checked:shadow-xl
                                hover:border-primary hover:shadow-lg text-center">
                        <i class="fas fa-building text-5xl text-primary mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Appartement</h3>
                        <p class="text-gray-600">Appartement en immeuble</p>
                    </div>
                </label>

                <!-- Commercial -->
                <label class="relative cursor-pointer group">
                    <input type="radio" name="property_type" value="commercial" 
                           class="peer sr-only" required
                           {{ (old('property_type', $data['property_type'] ?? '') == 'commercial') ? 'checked' : '' }}>
                    <div class="bg-white border-3 border-gray-200 rounded-2xl p-8 transition-all duration-300
                                peer-checked:border-primary peer-checked:bg-blue-50 peer-checked:shadow-xl
                                hover:border-primary hover:shadow-lg text-center">
                        <i class="fas fa-store text-5xl text-primary mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Commercial</h3>
                        <p class="text-gray-600">Local professionnel, boutique</p>
                    </div>
                </label>

                <!-- Autre -->
                <label class="relative cursor-pointer group">
                    <input type="radio" name="property_type" value="other" 
                           class="peer sr-only" required
                           {{ (old('property_type', $data['property_type'] ?? '') == 'other') ? 'checked' : '' }}>
                    <div class="bg-white border-3 border-gray-200 rounded-2xl p-8 transition-all duration-300
                                peer-checked:border-primary peer-checked:bg-blue-50 peer-checked:shadow-xl
                                hover:border-primary hover:shadow-lg text-center">
                        <i class="fas fa-question-circle text-5xl text-primary mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Autre</h3>
                        <p class="text-gray-600">Copropriété, bureau, etc.</p>
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
                        class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-primary to-secondary hover:from-blue-700 hover:to-blue-800 text-white rounded-full font-bold text-lg shadow-lg transition transform hover:scale-105">
                    <span>Suivant</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

