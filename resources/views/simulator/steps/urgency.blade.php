@extends('layouts.app')

@section('title', 'Simulateur de Prix - Urgence')

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
                Quel est votre niveau d'urgence ?
            </h1>
            <p class="text-xl text-gray-600">
                Cela nous aide à prioriser votre demande
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('simulator.submit', 'urgency') }}">
            @csrf
            
            <div class="space-y-4 mb-8">
                <label class="relative cursor-pointer group block">
                    <input type="radio" name="urgency" value="normal" 
                           class="peer sr-only" required
                           {{ (old('urgency', $data['urgency'] ?? '') == 'normal') ? 'checked' : '' }}>
                    <div class="bg-white border-3 border-gray-200 rounded-2xl p-6 transition-all duration-300
                                peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:shadow-xl
                                hover:border-green-500 hover:shadow-lg">
                        <div class="flex items-center gap-4">
                            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <i class="fas fa-calendar-check text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Normal</h3>
                                <p class="text-gray-600">Sous 2 à 4 semaines</p>
                            </div>
                        </div>
                    </div>
                </label>

                <label class="relative cursor-pointer group block">
                    <input type="radio" name="urgency" value="urgent" 
                           class="peer sr-only" required
                           {{ (old('urgency', $data['urgency'] ?? '') == 'urgent') ? 'checked' : '' }}>
                    <div class="bg-white border-3 border-gray-200 rounded-2xl p-6 transition-all duration-300
                                peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:shadow-xl
                                hover:border-orange-500 hover:shadow-lg">
                        <div class="flex items-center gap-4">
                            <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <i class="fas fa-clock text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Urgent</h3>
                                <p class="text-gray-600">Sous 1 semaine</p>
                            </div>
                        </div>
                    </div>
                </label>

                <label class="relative cursor-pointer group block">
                    <input type="radio" name="urgency" value="emergency" 
                           class="peer sr-only" required
                           {{ (old('urgency', $data['urgency'] ?? '') == 'emergency') ? 'checked' : '' }}>
                    <div class="bg-white border-3 border-gray-200 rounded-2xl p-6 transition-all duration-300
                                peer-checked:border-red-600 peer-checked:bg-red-50 peer-checked:shadow-xl
                                hover:border-red-600 hover:shadow-lg">
                        <div class="flex items-center gap-4">
                            <div class="bg-gradient-to-br from-red-600 to-red-700 text-white w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg animate-pulse">
                                <i class="fas fa-exclamation-triangle text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Urgence</h3>
                                <p class="text-gray-600">Dans les 48h - Intervention rapide</p>
                                <p class="text-red-600 text-sm font-semibold mt-1">
                                    <i class="fas fa-phone-alt mr-1"></i>
                                    Pour une urgence immédiate, appelez-nous : {{ $companySettings['phone'] }}
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
                        class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-primary to-secondary hover:from-blue-700 hover:to-blue-800 text-white rounded-full font-bold text-lg shadow-lg transition transform hover:scale-105">
                    <span>Suivant</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

