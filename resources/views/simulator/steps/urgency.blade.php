@extends('layouts.app')

@section('title', 'Simulateur de Prix - Urgence')

@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    .progress-bar {
        background-color: {{ setting('primary_color', '#2563eb') }};
    }
    
    .urgency-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .urgency-card:hover {
        transform: translateY(-4px);
    }
    
    .urgency-card input[type="radio"]:checked + div {
        border-width: 3px !important;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
    }
    
    /* Couleur spécifique pour chaque option */
    .urgency-normal input:checked + div {
        background-color: #ecfdf5 !important;
        border-color: #10b981 !important;
    }
    
    .urgency-urgent input:checked + div {
        background-color: #fff7ed !important;
        border-color: #f97316 !important;
    }
    
    .urgency-emergency input:checked + div {
        background-color: #fef2f2 !important;
        border-color: #dc2626 !important;
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
            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                <div class="progress-bar h-3 rounded-full transition-all duration-500" 
                     style="width: {{ $progress }}%"></div>
            </div>
        </div>

        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-4">
                Quel est votre niveau d'urgence ?
            </h1>
            <p class="text-xl text-gray-600">
                Cela nous aide à prioriser votre demande
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('simulator.submit', 'urgency') }}">
            @csrf
            
            <div class="space-y-5 mb-8">
                <label class="urgency-card urgency-normal block" onclick="this.querySelector('input').checked = true; this.querySelector('form').submit();">
                    <input type="radio" name="urgency" value="normal" 
                           required
                           {{ (old('urgency', $data['urgency'] ?? '') == 'normal') ? 'checked' : '' }}>
                    <div class="bg-white border-2 border-gray-300 rounded-2xl p-6 hover:border-green-500 hover:shadow-lg">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md"
                                 style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                <i class="fas fa-calendar-check text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Normal</h3>
                                <p class="text-gray-600 font-medium">Sous 2 à 4 semaines</p>
                            </div>
                        </div>
                    </div>
                </label>

                <label class="urgency-card urgency-urgent block" onclick="this.querySelector('input').checked = true;">
                    <input type="radio" name="urgency" value="urgent" 
                           required
                           {{ (old('urgency', $data['urgency'] ?? '') == 'urgent') ? 'checked' : '' }}>
                    <div class="bg-white border-2 border-gray-300 rounded-2xl p-6 hover:border-orange-500 hover:shadow-lg">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md"
                                 style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);">
                                <i class="fas fa-clock text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Urgent</h3>
                                <p class="text-gray-600 font-medium">Sous 1 semaine</p>
                            </div>
                        </div>
                    </div>
                </label>

                <label class="urgency-card urgency-emergency block" onclick="this.querySelector('input').checked = true;">
                    <input type="radio" name="urgency" value="emergency" 
                           required
                           {{ (old('urgency', $data['urgency'] ?? '') == 'emergency') ? 'checked' : '' }}>
                    <div class="bg-white border-2 border-gray-300 rounded-2xl p-6 hover:border-red-600 hover:shadow-xl">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg animate-pulse"
                                 style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);">
                                <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900">Urgence</h3>
                                <p class="text-gray-600 font-medium">Dans les 48h - Intervention rapide</p>
                                <p class="text-red-600 text-sm font-semibold mt-2 flex items-center gap-2">
                                    <i class="fas fa-phone-alt"></i>
                                    <span>Pour une urgence immédiate : {{ $companySettings['phone'] }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex justify-between items-center">
                <a href="{{ route('simulator.previous', 'urgency') }}" 
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

