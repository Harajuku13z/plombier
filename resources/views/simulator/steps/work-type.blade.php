@extends('layouts.app')

@section('title', 'Simulateur de Prix - Type de Travaux')

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
                Quel type de travaux souhaitez-vous réaliser ?
            </h1>
            <p class="text-xl text-gray-600">
                Sélectionnez le service dont vous avez besoin
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('simulator.submit', 'work-type') }}">
            @csrf
            
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                @foreach($workTypes as $key => $workType)
                <label class="relative cursor-pointer group">
                    <input type="radio" name="work_type" value="{{ $key }}" 
                           class="peer sr-only" required
                           {{ (old('work_type', $data['work_type'] ?? '') == $key) ? 'checked' : '' }}>
                    <div class="bg-white border-3 border-gray-200 rounded-2xl p-6 transition-all duration-300
                                peer-checked:border-primary peer-checked:bg-blue-50 peer-checked:shadow-xl
                                hover:border-primary hover:shadow-lg">
                        <div class="flex items-start gap-4">
                            <div class="bg-gradient-to-br from-primary to-secondary text-white w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <i class="fas {{ $workType['icon'] }} text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">
                                    {{ $workType['name'] }}
                                </h3>
                                <p class="text-gray-600 text-sm">
                                    {{ $workType['description'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>

            <!-- Description optionnelle -->
            <div class="bg-white rounded-2xl p-6 shadow-lg mb-8">
                <label class="block text-lg font-bold text-gray-900 mb-3">
                    <i class="fas fa-comment-alt text-primary mr-2"></i>
                    Décrivez brièvement votre besoin (optionnel)
                </label>
                <textarea name="description" rows="4" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary"
                          placeholder="Exemple : Fuite sous l'évier de la cuisine depuis hier soir...">{{ old('description', $data['description'] ?? '') }}</textarea>
            </div>

            <!-- Buttons -->
            <div class="flex justify-between items-center">
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 border-2 border-gray-300 rounded-full font-semibold text-gray-700 hover:bg-gray-100 transition">
                    <i class="fas fa-home"></i>
                    <span>Annuler</span>
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

