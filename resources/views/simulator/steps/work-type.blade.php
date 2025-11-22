@extends('layouts.app')

@section('title', 'Simulateur de Prix - Type de Travaux')

@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    :root {
        --primary-color: {{ setting('primary_color', '#2563eb') }};
        --secondary-color: {{ setting('secondary_color', '#0284c7') }};
    }
    .text-primary { color: var(--primary-color) !important; }
    .border-primary { border-color: var(--primary-color) !important; }
    
    .work-type-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .work-type-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
    }
    
    .work-type-card input:checked + div {
        background-color: #eff6ff !important;
        border-color: {{ setting('primary_color', '#2563eb') }} !important;
        border-width: 3px !important;
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25) !important;
    }
    
    .work-type-icon {
        background-color: {{ setting('primary_color', '#2563eb') }};
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
    
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
            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                <div class="progress-bar h-3 rounded-full transition-all duration-500" 
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
            
            <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 mb-6">
                <p class="text-blue-900 text-sm font-semibold">
                    <i class="fas fa-info-circle mr-2"></i>
                    Vous pouvez sélectionner <strong>plusieurs types de travaux</strong>
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                @foreach($workTypes as $key => $workType)
                <label class="work-type-card">
                    <input type="checkbox" name="work_types[]" value="{{ $key }}" 
                           class="sr-only"
                           {{ in_array($key, old('work_types', $data['work_types'] ?? [])) ? 'checked' : '' }}>
                    <div class="bg-white border-2 border-gray-300 rounded-2xl p-6 h-full">
                        <div class="flex items-start gap-4">
                            <div class="work-type-icon text-white w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas {{ $workType['icon'] }} text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">
                                    {{ $workType['name'] }}
                                </h3>
                                <p class="text-gray-600 text-sm leading-relaxed">
                                    {{ $workType['description'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
            
            @error('work_types')
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                    <p class="text-red-700 text-sm font-semibold">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ $message }}
                    </p>
                </div>
            @enderror
            
            <div id="selection-error" class="hidden bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                <p class="text-red-700 text-sm font-semibold">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Veuillez sélectionner au moins un type de travaux
                </p>
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
                        class="inline-flex items-center gap-2 px-8 py-4 text-white rounded-full font-bold text-lg shadow-xl transition transform hover:scale-105"
                        style="background-color: {{ setting('primary_color', '#2563eb') }};">
                    <span>Suivant</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const errorDiv = document.getElementById('selection-error');
    
    form.addEventListener('submit', function(e) {
        const checkboxes = document.querySelectorAll('input[name="work_types[]"]:checked');
        
        if (checkboxes.length === 0) {
            e.preventDefault();
            errorDiv.classList.remove('hidden');
            window.scrollTo({ top: errorDiv.offsetTop - 100, behavior: 'smooth' });
            return false;
        }
        
        errorDiv.classList.add('hidden');
    });
});
</script>
@endsection

